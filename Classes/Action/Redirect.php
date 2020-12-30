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

namespace Leuchtfeuer\Locate\Action;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Redirect extends AbstractAction
{
    const SESSION_KEY = 'language';
    const OVERRIDE_PARAMETER = 'setLang';

    private $cookieMode = false;

    private $redirectLanguageUid = 0;

    private $requestedLanguageUid = 0;

    /**
     * @return ResponseInterface|null
     * @throws AspectNotFoundException
     */
    public function execute(): ?ResponseInterface
    {
        $this->redirectLanguageUid = (int)$this->configuration['sys_language'];
        $this->requestedLanguageUid = GeneralUtility::makeInstance(Context::class)->getAspect('language')->getId();

        // Initialize Cookie mode if necessary and prepare everything for possible redirects
        $this->initializeCookieMode();
        $this->handleCookieStuff();

        // Skip if no redirect is necessary
        if (!$this->shouldRedirect()) {
            $this->logger->info('No redirect necessary.');

            return null;
        }

        // Try to redirect to page (if not set, it will be the current page) on configured language
        if ($this->configuration['page'] || isset($this->configuration['sys_language'])) {
            $this->logger->info('Try to redirect to page');

            return $this->redirectToPage();
        }

        // Try to redirect by configured URL
        return $this->configuration['url'] ? $this->redirectToUrl($this->configuration['url']) : null;
    }

    /**
     * Set CookeMode Param to true if cookieHandling is enables
     */
    private function initializeCookieMode(): void
    {
        if ((bool)($this->configuration['cookieHandling'] ?? false) === true) {
            $this->logger->info('Cookie Handling is set.');
            $this->cookieMode = true;
        }
    }

    private function handleCookieStuff()
    {
        $currentLanguageUid = $this->requestedLanguageUid;

        if ($this->isCookieSet()) {
            // Cookie is not in current language
            $this->logger->info('Cookie is set.');

            if ($this->isCookieInCurrentLanguage() === false && $this->shouldOverrideCookie() === true) {
                // Override cookie
                $this->logger->info('Cookie is not in current language, so we override it.');
                $this->redirectLanguageUid = $currentLanguageUid;
                $this->setCookie($currentLanguageUid);
            } elseif ($this->isCookieInCurrentLanguage()) {
                // Cookie is in current language
                $this->logger->info('Cookie is in current language.');
                $this->redirectLanguageUid = $currentLanguageUid;
            } else {
                // Override config array by cookie value
                $this->logger->info('Cookie is not in current language and overriding is not allowed.');
                $this->redirectLanguageUid = $this->getCookieValue();
                $this->configuration['sys_language'] = $this->getCookieValue();
            }
        } elseif ($this->cookieMode === true) {
            $this->logger->info('Cookie is not set, but we are in cookie mode.');

            if ($currentLanguageUid !== $this->redirectLanguageUid) {
                // Set cookie value to target language
                $this->setCookie($this->redirectLanguageUid);
            } else {
                // Set cookie value to current language
                $this->setCookie($currentLanguageUid);
            }
        }
    }

    private function isCookieSet(): bool
    {
        return $this->getCookieValue() !== null;
    }

    private function isCookieInCurrentLanguage(): bool
    {
        return $this->requestedLanguageUid === $this->getCookieValue();
    }

    private function shouldOverrideCookie(): bool
    {
        if ((bool)($this->configuration['overrideCookie'] ?? false) === true) {
            return isset($GLOBALS['TYPO3_REQUEST']->getQueryParams()[$this->configuration['overrideParam'] ?? self::OVERRIDE_PARAMETER]);
        }

        return false;
    }

    private function setCookie(?int $value)
    {
        if ($value === null) {
            $value = (int)$this->configuration['sys_language'];
        }

        $this->session->set(self::SESSION_KEY, $value);
    }

    private function getCookieValue(): ?int
    {
        return $this->session->get(self::SESSION_KEY);
    }

    private function shouldRedirect(): bool
    {
        // Always redirect when we are not in cookie mode
        if ($this->cookieMode === false) {
            return true;
        }

        // Do not redirect, when cookie is set and cookie value matches given language id
        if ($this->isCookieInCurrentLanguage()) {
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
        $targetLanguageId = (int)$this->configuration['sys_language'];
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
        unset($queryParams[$this->configuration['overrideParam'] ?? self::OVERRIDE_PARAMETER]);

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
