<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Middleware;

use Leuchtfeuer\Locate\Domain\Repository\RegionRepository;
use Leuchtfeuer\Locate\Utility\LocateUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Controller\ErrorPageController;
use TYPO3\CMS\Core\Error\PageErrorHandler\InvalidPageErrorHandlerException;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerInterface;
use TYPO3\CMS\Core\Error\PageErrorHandler\PageErrorHandlerNotConfiguredException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageUnavailableMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Not responsible if backend user is logged in or EXT:static_info_tables is not loaded.
        if (($GLOBALS['BE_USER']->user !== null && $GLOBALS['BE_USER']->user['uid'] > 0) || ExtensionManagementUtility::isLoaded('static_info_tables') === false) {
            return $handler->handle($request);
        }

        /** @var PageArguments $routing */
        $routing = $request->getAttribute('routing');
        $page = BackendUtility::getRecord('pages', $routing->getPageId());

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

    private function isPageAvailableInCurrentRegion(array $page): bool
    {
        $countryCode = GeneralUtility::makeInstance(LocateUtility::class)->getCountryIso2FromIP();
        $regionRepository = GeneralUtility::makeInstance(RegionRepository::class);
        $countries = $regionRepository->getCountriesForPage($page['uid']);

        if (($countryCode === false || $countryCode === '-') && $regionRepository->shouldApplyWhenNoIpMatches($page['uid'])) {
            $countryCode = '-';
            $countries[$countryCode] = true;
        }

        return (bool)$page['tx_locate_invert'] === false ? isset($countries[$countryCode]) : !isset($countries[$countryCode]);
    }

    /**
     * Checks if a site is configured, and an error handler is configured for this specific status code.
     *
     * @param ServerRequestInterface $request
     * @param int $statusCode
     * @return PageErrorHandlerInterface|null
     *
     * @throws InvalidPageErrorHandlerException
     */
    protected function getErrorHandlerFromSite(ServerRequestInterface $request, int $statusCode): ?PageErrorHandlerInterface
    {
        $site = $request->getAttribute('site');
        if ($site instanceof Site) {
            try {
                return $site->getErrorHandler($statusCode);
            } catch (PageErrorHandlerNotConfiguredException $e) {
                // No error handler found, so fallback back to the generic TYPO3 error handler.
            }
        }
        return null;
    }

    /**
     * Ensures that a response object is created as a "fallback" when no error handler is configured.
     *
     * @param ServerRequestInterface $request
     * @param int $statusCode
     * @param string $reason
     * @return ResponseInterface
     */
    protected function handleDefaultError(ServerRequestInterface $request, int $statusCode, string $reason = ''): ResponseInterface
    {
        if (strpos($request->getHeaderLine('Accept'), 'application/json') !== false) {
            return new JsonResponse(['reason' => $reason], $statusCode);
        }
        $content = GeneralUtility::makeInstance(ErrorPageController::class)->errorAction(
            'Page Not Found',
            'The page did not exist or was inaccessible.' . ($reason ? ' Reason: ' . $reason : '')
        );
        return new HtmlResponse($content, $statusCode);
    }
}
