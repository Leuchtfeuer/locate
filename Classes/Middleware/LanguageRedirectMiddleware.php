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

use Leuchtfeuer\Locate\Processor\Court;
use Leuchtfeuer\Locate\Verdict\Redirect;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

class LanguageRedirectMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $typoScript = $this->getTypoScriptSetup();

        if (isset($typoScript['config.']['tx_locate']) && (int)$typoScript['config.']['tx_locate'] === 1) {
            $locateSetup = $typoScript['config.']['tx_locate.'];

            $config = [
                'verdicts' => $locateSetup['verdicts.'] ?? [],
                'facts' => $locateSetup['facts.'] ?? [],
                'judges' => $locateSetup['judges.'] ?? [],
                'settings' => [
                    'dryRun' => (bool)($locateSetup['dryRun'] ?? false),
                    'overrideQueryParameter' => $locateSetup['overrideQueryParameter'] ?? Redirect::OVERRIDE_PARAMETER,
                    'overrideSessionValue' => (bool)($locateSetup['overrideSessionValue'] ?? 0),
                    'sessionHandling' => (bool)($locateSetup['sessionHandling'] ?? 0),
                    'excludeBots' => (bool)($locateSetup['excludeBots'] ?? 1),
                    'simulateIp' => (string)($locateSetup['simulateIp'] ?? ''),
                ],
            ];

            return GeneralUtility::makeInstance(Court::class, $config)->run() ?? $handler->handle($request);
        }

        return $handler->handle($request);
    }

    protected function getTypoScriptSetup(): array
    {
        $backendConfigurationManager = GeneralUtility::makeInstance(BackendConfigurationManager::class);
        return $backendConfigurationManager->getTypoScriptSetup();
    }
}
