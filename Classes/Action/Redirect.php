<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class Redirect
 */
class Redirect extends AbstractAction
{
    const COOKIE_NAME = 'bm_locate';
    const OVERRIDE_PARAMETER = 'setLang';
    const HTTP_RESPONSE_CODE = 301;

    private $cookieMode = false;

    private $redirectLanguageUid = 0;

    private $requestedLanguageUid = 0;

    private $httpStatus = '';

    private $cookieName = '';

    /**
     * Call the action module
     *
     * @throws \Exception
     */
    public function process(array &$facts, Decision &$decision)
    {
        $this->redirectLanguageUid = (int)$this->configuration['sys_language'];
        $this->requestedLanguageUid = GeneralUtility::makeInstance(Context::class)->getAspect('language')->getId();

        // Initialize Cookie mode if necessary and prepare everything for possible redirects
        $this->initializeCookieMode();
        $this->handleCookieStuff();

        // Skip if no redirect is necessary
        if (!$this->shouldRedirect($this->requestedLanguageUid)) {
            $this->logger->info('No redirect necessary.');

            return;
        }

        $this->httpStatus = $this->configuration['httpResponseCode'] ? (int)$this->configuration['httpResponseCode'] : HttpUtility::HTTP_STATUS_301;

        // Try to redirect to page (if not set, it will be the current page) on configured language
        if ($this->configuration['page'] || isset($this->configuration['sys_language'])) {
            $this->logger->info('Try to redirect to page');
            $this->redirectToPid((int)$this->configuration['page'], $this->redirectLanguageUid);
        }

        // Try to redirect by configured URL (and language, if configured)
        if ($this->configuration['url'] && $this->configuration['sys_language']) {
            $this->redirectToUrl($this->configuration['url']);
        } elseif ($this->configuration['url']) {
            $this->redirectToUrl($this->configuration['url']);
        }
    }

    /**
     * Set CookeMode Param to true if cookieHandling is enables
     */
    private function initializeCookieMode(): void
    {
        if (isset($this->configuration['cookieHandling']) && (bool)$this->configuration['cookieHandling'] === true) {
            $this->logger->info('Cookie Handling is set.');
            $this->cookieMode = true;
            $this->cookieName = $this->configuration['cookieName'] ?? self::COOKIE_NAME;
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
        return isset($_COOKIE[$this->cookieName]);
    }

    private function isCookieInCurrentLanguage(): bool
    {
        return $this->requestedLanguageUid === (int)$_COOKIE[$this->cookieName];
    }

    private function shouldOverrideCookie(): bool
    {
        if (isset($this->configuration['overrideCookie']) && (bool)$this->configuration['overrideCookie'] === true) {
            $overrideParam = $this->configuration['overrideParam'] ?? self::OVERRIDE_PARAMETER;
            if ((bool)GeneralUtility::_GP($overrideParam) === true) {
                return true;
            }
        }

        return false;
    }

    private function setCookie(int $value)
    {
        if ($value === null) {
            setcookie($this->cookieName, (string)$this->configuration['sys_language'], time() + 60 * 60 * 24 * 30, '/');
        } else {
            setcookie($this->cookieName, (string)$value, time() + 60 * 60 * 24 * 30, '/');
        }
    }

    private function getCookieValue(): int
    {
        return (int)$_COOKIE[$this->cookieName] ?? $this->redirectLanguageUid ?? 0;
    }

    private function shouldRedirect(int $sysLanguageUid): bool
    {
        // Always redirect when we are not in cookie mode
        if ($this->cookieMode === false) {
            return true;
        }

        // Do not redirect, when cookie is set and cookie value matches given language id
        if (isset($_COOKIE[$this->cookieName]) && (int)$_COOKIE[$this->cookieName] === $sysLanguageUid) {
            return false;
        }

        return true;
    }

    /**
     * Redirect to a page
     * @throws Exception
     */
    private function redirectToPid(int $page, int $language): void
    {
        $urlParameters = [];
        $this->getAdditionalUrlParams($urlParameters);

        $contentObjectRenderer = !empty($GLOBALS['TSFE']->cObj) ? $GLOBALS['TSFE']->cObj : new ContentObjectRenderer($GLOBALS['TSFE']);

        if ($page > 0) {
            if ($page === $GLOBALS['TSFE']->id) {
                $this->logger->info('Target page matches current page. No redirect.');

                return;
            }

            $url = $contentObjectRenderer->getTypoLink_URL($page, $urlParameters);
            $url = $GLOBALS['TSFE']->baseUrlWrap($url);
            $url = GeneralUtility::locationHeaderURL($url);
        } elseif ($language >= 0) {
            $urlParameters['L'] = $language;
            $url = $contentObjectRenderer->getTypoLink_URL($GLOBALS['TSFE']->id, $urlParameters);
            $url = $GLOBALS['TSFE']->baseUrlWrap($url);
            $url = GeneralUtility::locationHeaderURL($url);
        } else {
            throw new Exception(__CLASS__ . ' the configured redirect page is not an integer');
        }

        $this->redirectToUrl($url);
    }

    private function getAdditionalUrlParams(array &$urlParameters): void
    {
        $additionalUrlParams = GeneralUtility::_GET();

        if (is_array($additionalUrlParams) && count($additionalUrlParams)) {
            if (isset($additionalUrlParams['setLang'])) {
                unset($additionalUrlParams['setLang']);
            }

            if (isset($additionalUrlParams['id'])) {
                unset($additionalUrlParams['id']);
            }

            $urlParameters = array_merge($additionalUrlParams, $urlParameters);
        }
    }

    /**
     * This will redirect the user to a new web location. This can be a relative or absolute web path, or it
     * can be an entire URL.
     */
    public function redirectToUrl(string $location): void
    {
        if (GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL') !== $location) {
            $this->logger->info(sprintf('%s Will redirect to %s with code %s.', __CLASS__, $location, $this->httpStatus));
            HttpUtility::redirect($location, $this->httpStatus);
            exit;
        }

        $this->logger->info('No redirect.');
    }
}
