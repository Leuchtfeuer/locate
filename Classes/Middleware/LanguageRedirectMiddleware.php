<?php
declare(strict_types = 1);
namespace Bitmotion\Locate\Middleware;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

use Bitmotion\Locate\Action\Redirect;
use Bitmotion\Locate\Processor\Court;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

class LanguageRedirectMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $typoScript = $GLOBALS['TSFE']->tmpl->setup;

        if (isset($typoScript['plugin.']['tx_locate_pi1'])) {
            $typoScript['config.']['tx_locate'] = $typoScript['plugin.']['tx_locate_pi1'];
            $typoScript['config.']['tx_locate.'] = array_merge_recursive($typoScript['config.']['tx_locate.'] ?? [], $typoScript['plugin.']['tx_locate_pi1.']);
        }

        if (isset($typoScript['config.']['tx_locate']) && isset($typoScript['config.']['tx_locate.'])) {
            $locateSetup = $typoScript['config.']['tx_locate.'];

            $config = [
                'actions' => $locateSetup['actions.'] ?? [],
                'facts' => $locateSetup['facts.'] ?? [],
                'judges' => $locateSetup['judges.'] ?? [],
                'settings' => [
                    'cookieName' => $locateSetup['cookieName'] ?? Redirect::COOKIE_NAME,
                    'cookieLifetime' => isset($locateSetup['cookieLifetime']) ? (int)$locateSetup['cookieLifetime'] : Redirect::COOKIE_LIFETIME,
                    'dryRun' => isset($locateSetup['dryRun']) ? (bool)$locateSetup['dryRun'] : false,
                    'overrideParam' => $locateSetup['overrideParam'] ?? Redirect::OVERRIDE_PARAMETER,
                    'responseCode' => $locateSetup['httpResponseCode'] ?? HttpUtility::HTTP_STATUS_301,
                ],
            ];

            $court = GeneralUtility::makeInstance(Court::class, $config);
            $court->run();
        }

        return $handler->handle($request);
    }
}
