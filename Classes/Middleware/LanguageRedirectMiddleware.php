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

use Leuchtfeuer\Locate\Action\Redirect;
use Leuchtfeuer\Locate\Processor\Court;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LanguageRedirectMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $typoScript = $this->getTypoScriptSetup();

        if ((int)$typoScript['config.']['tx_locate'] === 1 && !empty($typoScript['config.']['tx_locate.'] ?? [])) {
            $locateSetup = $typoScript['config.']['tx_locate.'];

            $config = [
                'actions' => $locateSetup['actions.'] ?? [],
                'facts' => $locateSetup['facts.'] ?? [],
                'judges' => $locateSetup['judges.'] ?? [],
                'settings' => [
                    'dryRun' => (bool)($locateSetup['dryRun'] ?? false),
                    'overrideParam' => $locateSetup['overrideParam'] ?? Redirect::OVERRIDE_PARAMETER,
                    'overrideCookie' => (bool)($locateSetup['overrideCookie'] ?? 0),
                    'cookieHandling' => (bool)($locateSetup['cookieHandling'] ?? 0),
                    'excludeBots' => (bool)($locateSetup['excludeBots'] ?? 1),
                ],
            ];

            return GeneralUtility::makeInstance(Court::class, $config)->run() ?? $handler->handle($request);
        }

        return $handler->handle($request);
    }

    protected function getTypoScriptSetup(): array
    {
        if (!$GLOBALS['TSFE']->tmpl instanceof TemplateService || empty($GLOBALS['TSFE']->tmpl->setup)) {
            $GLOBALS['TSFE']->getConfigArray();
        }

        return $GLOBALS['TSFE']->tmpl->setup;
    }
}
