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

use Leuchtfeuer\Locate\Domain\DTO\Configuration;
use Leuchtfeuer\Locate\Processor\Court;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\LinkHandling\Exception\UnknownLinkHandlerException;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;

final class LanguageRedirectMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly LinkService $link,
        private readonly Court $court,
    ) {}

    /**
     * @throws UnknownLinkHandlerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isErrorPage($request)) {
            $frontendTyposcript = $request->getAttribute('frontend.typoscript');

            if ($frontendTyposcript instanceof FrontendTypoScript) {
                $typoScript = $frontendTyposcript->getConfigArray();

                if (isset($typoScript['tx_locate']) && (int)$typoScript['tx_locate'] === 1) {
                    $locateSetup = $typoScript['tx_locate.'] ?? [];

                    $config = new Configuration();
                    $config->setDryRun((bool)($locateSetup['dryRun'] ?? false));
                    $config->setOverrideQueryParameter($locateSetup['overrideQueryParameter'] ?? Configuration::OVERRIDE_PARAMETER);
                    $config->setOverrideSessionValue((bool)($locateSetup['overrideSessionValue'] ?? 0));
                    $config->setSessionHandling((bool)($locateSetup['sessionHandling'] ?? 0));
                    $config->setExcludeBots((bool)($locateSetup['excludeBots'] ?? 1));
                    $config->setSimulateIp((string)($locateSetup['simulateIp'] ?? ''));
                    $config->setJudges($locateSetup['judges.'] ?? []);
                    $config->setFacts($locateSetup['facts.'] ?? []);
                    $config->setVerdicts($locateSetup['verdicts.'] ?? []);

                    return $this->court->withConfiguration($config)->run() ?? $handler->handle($request);
                }
            }
        }

        return $handler->handle($request);
    }

    /**
     * @throws UnknownLinkHandlerException
     */
    private function isErrorPage(ServerRequestInterface $request): bool
    {
        $site = $request->getAttribute('site');
        if ($site instanceof Site) {
            $siteConfig = $site->getConfiguration();
            $routing = $request->getAttribute('routing');

            if ($routing instanceof PageArguments && is_array($siteConfig['errorHandling'] ?? null)) {
                $errorHandlers = $siteConfig['errorHandling'];
                $requestPageUid = $routing->getPageId();

                if (in_array($requestPageUid, $this->getErrorPageUids($errorHandlers))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array<int, array<string, string>> $errorHandlers
     * @return array<int>
     * @throws UnknownLinkHandlerException
     */
    private function getErrorPageUids(array $errorHandlers): array
    {
        $errorPageUids = [];

        foreach ($errorHandlers as $errorHandler) {
            if (isset($errorHandler['errorContentSource'])) {
                $pageUid = $this->link->resolve($errorHandler['errorContentSource'])['pageuid'] ?? null;
                if ($pageUid) {
                    $errorPageUids[] = (int)$pageUid;
                }
            }
        }

        return $errorPageUids;
    }
}
