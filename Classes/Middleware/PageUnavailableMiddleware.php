<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Middleware;

use Doctrine\DBAL\Exception;
use Leuchtfeuer\Locate\Domain\Repository\RegionRepository;
use Leuchtfeuer\Locate\Utility\LocateUtility;
use Leuchtfeuer\Locate\Utility\TypeCaster;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Controller\ErrorPageController;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Error\PageErrorHandler\InvalidPageErrorHandlerException;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerInterface;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerNotConfiguredException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageUnavailableMiddleware implements MiddlewareInterface
{
    /**
     * @throws InvalidPageErrorHandlerException|Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Not responsible if backend user is logged in.
        if (($GLOBALS['BE_USER'] !== null && $GLOBALS['BE_USER']->user !== null && $GLOBALS['BE_USER']->user['uid'] > 0)) {
            return $handler->handle($request);
        }

        /** @var SiteLanguage $language */
        $language = $request->getAttribute('language');
        $languageId = $language->getLanguageId();
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        /** @var PageArguments $routing */
        $routing = $request->getAttribute('routing');
        if ($languageId > 0) {
            $page = $pageRepository->getPageOverlay($routing->getPageId(), $languageId);
            if (isset($page['_LOCALIZED_UID'])) {
                $page['uid'] = $page['_LOCALIZED_UID'];
            }
        } else {
            $page = $pageRepository->getPage($routing->getPageId());
        }

        if (($page['tx_locate_regions'] ?? 0) > 0 && !$this->isPageAvailableInCurrentRegion($page)) {
            $errorHandler = $this->getErrorHandlerFromSite($request, 451);
            $message = 'The requested page is not available in your country.';

            if ($errorHandler instanceof PageErrorHandlerInterface) {
                return $errorHandler->handlePageError($request, $message, []);
            }

            return $this->handleDefaultError($request, 451, $message);
        }

        return $handler->handle($request);
    }

    /**
     * @param array<string, mixed> $page
     * @throws Exception
     */
    private function isPageAvailableInCurrentRegion(array $page): bool
    {
        $pageUid = TypeCaster::toInt($page['uid']);
        $locateInvert = TypeCaster::toBool($page['tx_locate_invert']);
        $countryCode = GeneralUtility::makeInstance(LocateUtility::class)->getCountryIso2FromIP();
        $regionRepository = GeneralUtility::makeInstance(RegionRepository::class);
        $countries = $regionRepository->getCountriesForPage($pageUid);

        if (($countryCode === false || $countryCode === '-') && $regionRepository->shouldApplyWhenNoIpMatches($pageUid)) {
            $countryCode = '-';
            $countries[$countryCode] = true;
        }

        return $locateInvert === false ? isset($countries[$countryCode]) : !isset($countries[$countryCode]);
    }

    /**
     * Checks if a site is configured, and an error handler is configured for this specific status code.
     *
     *
     * @throws InvalidPageErrorHandlerException
     */
    protected function getErrorHandlerFromSite(ServerRequestInterface $request, int $statusCode): ?PageErrorHandlerInterface
    {
        $site = $request->getAttribute('site');
        if ($site instanceof Site) {
            try {
                return $site->getErrorHandler($statusCode);
            } catch (PageErrorHandlerNotConfiguredException) {
                // No error handler found, so fallback back to the generic TYPO3 error handler.
            }
        }
        return null;
    }

    /**
     * Ensures that a response object is created as a "fallback" when no error handler is configured.
     */
    protected function handleDefaultError(ServerRequestInterface $request, int $statusCode, string $reason = ''): ResponseInterface
    {
        if (str_contains($request->getHeaderLine('Accept'), 'application/json')) {
            return new JsonResponse(['reason' => $reason], $statusCode);
        }
        $content = GeneralUtility::makeInstance(ErrorPageController::class)->errorAction(
            'Page Not Found',
            'The page did not exist or was inaccessible.' . ($reason !== '' && $reason !== '0' ? ' Reason: ' . $reason : '')
        );
        return new HtmlResponse($content, $statusCode);
    }
}
