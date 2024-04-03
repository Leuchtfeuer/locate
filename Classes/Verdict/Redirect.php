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

namespace Leuchtfeuer\Locate\Verdict;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Redirect extends AbstractVerdict
{
    public const SESSION_KEY = 'language';
    public const OVERRIDE_PARAMETER = 'setLang';

    private bool $sessionMode = false;

    private int $redirectLanguageUid = 0;

    private int $requestedLanguageUid = 0;

    /**
     * @return ResponseInterface|null
     * @throws AspectNotFoundException
     */
    public function execute(): ?ResponseInterface
    {
        $this->redirectLanguageUid = isset($this->configuration['sys_language']) ? (int)$this->configuration['sys_language'] : 0;
        $this->requestedLanguageUid = GeneralUtility::makeInstance(Context::class)->getAspect('language')->getId();

        // Initialize Session mode if necessary and prepare everything for possible redirects
        $this->initializeSessionMode();
        $this->handleSessionStuff();

        // Skip if no redirect is necessary
        if (!$this->shouldRedirect()) {
            $this->logger->info('No redirect necessary.');

            return null;
        }

        // Try to redirect to page (if not set, it will be the current page) on configured language
        if ((isset($this->configuration['page']) && !empty($this->configuration['page'])) || isset($this->configuration['sys_language'])) {
            $this->logger->info('Try to redirect to page');

            return $this->redirectToPage();
        }

        // Try to redirect by configured URL
        return $this->configuration['url'] ? $this->redirectToUrl($this->configuration['url']) : null;
    }

    /**
     * Set sessionMode Param to true if sessionHandling is enables
     */
    private function initializeSessionMode(): void
    {
        if ((bool)($this->configuration['sessionHandling'] ?? false) === true) {
            $this->logger->info('Session Handling is set.');
            $this->sessionMode = true;
        }
    }

    private function handleSessionStuff(): void
    {
        $currentLanguageUid = $this->requestedLanguageUid;

        if ($this->isSessionValueSet()) {
            // Session is not in current language
            $this->logger->info('Session value is set.');

            if ($this->isSessionInCurrentLanguage() === false && $this->shouldOverrideSessionValue() === true) {
                // Override session
                $this->logger->info('Session is not in current language, so we override it.');
                $this->redirectLanguageUid = $currentLanguageUid;
                $this->setSessionValue($currentLanguageUid);
            } elseif ($this->isSessionInCurrentLanguage()) {
                // Session is in current language
                $this->logger->info('Session is in current language.');
                $this->redirectLanguageUid = $currentLanguageUid;
            } else {
                // Override config array by session value
                $this->logger->info('Session is not in current language and overriding is not allowed.');
                $this->redirectLanguageUid = $this->getSessionValue();
                $this->configuration['sys_language'] = $this->getSessionValue();
            }
        } elseif ($this->sessionMode === true) {
            $this->logger->info('Session is not set, but we are in session mode.');

            if ($currentLanguageUid !== $this->redirectLanguageUid) {
                // Set session value to target language
                $this->setSessionValue($this->redirectLanguageUid);
            } else {
                // Set session value to current language
                $this->setSessionValue($currentLanguageUid);
            }
        }
    }

    private function isSessionValueSet(): bool
    {
        return $this->getSessionValue() !== null;
    }

    private function isSessionInCurrentLanguage(): bool
    {
        return $this->requestedLanguageUid === $this->getSessionValue();
    }

    private function shouldOverrideSessionValue(): bool
    {
        if ((bool)($this->configuration['overrideSessionValue'] ?? false) === true) {
            return isset($GLOBALS['TYPO3_REQUEST']->getQueryParams()[$this->configuration['overrideQueryParameter'] ?? self::OVERRIDE_PARAMETER]);
        }

        return false;
    }

    private function setSessionValue(?int $value): void
    {
        if ($value === null) {
            $value = (int)$this->configuration['sys_language'];
        }

        $this->session->set(self::SESSION_KEY, $value);
    }

    private function getSessionValue(): ?int
    {
        return $this->session->get(self::SESSION_KEY);
    }

    private function shouldRedirect(): bool
    {
        // Always redirect when we are not in session mode
        if ($this->sessionMode === false) {
            return true;
        }

        // Redirect when URL is set and URL does not match actual URL
        if (isset($this->configuration['url'])) {
            $configUri = new Uri($this->configuration['url']);
            $typo3Uri = $GLOBALS['TYPO3_REQUEST']->getUri();

            if ($configUri->getHost() !== $typo3Uri->getHost() || $configUri->getScheme() !== $typo3Uri->getScheme() || $configUri->getPath() !== $typo3Uri->getPath()) {
                return true;
            }
        }

        // Do not redirect, when session is set and session value matches given language id
        if ($this->isSessionInCurrentLanguage()) {
            return false;
        }

        return true;
    }

    /**
     * Redirect to a page
     */
    private function redirectToPage(): ?RedirectResponse
    {
        $pageId = (int)($this->configuration['page'] ?? $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageId());
        $targetLanguageId = isset($this->configuration['sys_language']) ? (int)$this->configuration['sys_language'] : 0;
        $page = BackendUtility::getRecord('pages', $pageId, '*', '', false);

        // Page is in current language - no redirect necessary
        if (($page['sys_language_uid'] === $targetLanguageId && $targetLanguageId !== 0) || $this->requestedLanguageUid === $targetLanguageId) {
            $this->logger->info('Target language equals current language. No redirect.');

            return null;
        }

        // Overlay page record for languages other than the default one
        if ($targetLanguageId !== 0) {
            $page = GeneralUtility::makeInstance(PageRepository::class)->getPageOverlay($page, $targetLanguageId);

            // Overlay record does not exist
            if (!isset($page['_PAGES_OVERLAY_UID'])) {
                $this->logger->info(sprintf('There is no page overlay for page %d and language %d', $page['uid'], $targetLanguageId));

                return null;
            }
        }

        /** @var Site $site */
        $site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
        $queryParams = $GLOBALS['TYPO3_REQUEST']->getQueryParams();
        unset($queryParams[$this->configuration['overrideQueryParameter'] ?? self::OVERRIDE_PARAMETER]);

        $uri = $site->getRouter()->generateUri(
            $page['uid'],
            array_merge(
                $queryParams,
                [
                    '_language' => $targetLanguageId,
                ]
            ),
            (string)$GLOBALS['TYPO3_REQUEST']->getUri()->getFragment()
        );

        return $this->redirectToUrl((string)$uri);
    }

    /**
     * This will redirect the user to a new web location. This can be a relative or absolute web path, or it
     * can be an entire URL.
     *
     * @param string $location
     * @return RedirectResponse|null
     */
    public function redirectToUrl(string $location): ?RedirectResponse
    {
        if (GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL') !== $location) {
            $this->logger->info(sprintf('%s will redirect to %s.', __CLASS__, $location));

            return new RedirectResponse($location, 307);
        }

        $this->logger->info('No redirect.');

        return null;
    }
}
