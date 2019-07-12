<?php

namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Redirect
 *
 * @package Bitmotion\Locate\Action
 */
class Redirect extends AbstractAction
{
    /**
     * @var bool
     */
    private $cookieMode = false;

    /**
     * @var string
     */
    private $cookieName = 'bm_locate';

    /**
     * @var int
     */
    private $redirectLanguageUid = 0;

    /**
     * @var int 
     */
    private $requestedLanguageUid = 0;

    /**
     * Call the action module
     */
    public function process(array &$facts, Decision &$decision)
    {
        $httpResponseCode = $this->configuration['httpResponseCode'] ? $this->configuration['httpResponseCode'] : 301;
        $this->redirectLanguageUid = (int)$this->configuration['sys_language'];

        if (class_exists(\TYPO3\CMS\Core\Context\Context::class)) {
            try {
                $languageAspect = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
                $this->requestedLanguageUid = (int)$languageAspect->getId();
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
                return;
            }
        } else {
            $this->requestedLanguageUid = (int)GeneralUtility::_GP('L');
        }
        
        // Initialize Cookie mode if necessary and prepare everything for possible redirects
        $this->initializeCookieMode();
        $this->handleCookieStuff();

        // Skip if no redirect is necessary
        if (!$this->shouldRedirect($this->requestedLanguageUid )) {
            return;
        };

        // Try to redirect to page (if not set, it will be the current page) on configured language
        if ($this->configuration['page'] || isset($this->configuration['sys_language'])) {
            $this->redirectToPid((string)$this->configuration['page'], $this->redirectLanguageUid, (int)$httpResponseCode);
        }

        // Try to redirect by configured URL (and language, if configured)
        if ($this->configuration['url'] && $this->configuration['sys_language']) {
            $this->redirectToUrl($this->configuration['url'], $httpResponseCode, $this->redirectLanguageUid);
        } elseif ($this->configuration['url']) {
            $this->redirectToUrl($this->configuration['url'], $httpResponseCode);
        }

        return;
    }

    /**
     * Set CookeMode Param to true if cookieHandling is enables
     */
    private function initializeCookieMode()
    {
        if (isset($this->configuration['cookieHandling']) && $this->configuration['cookieHandling'] == 1) {
            $this->logger->notice('Cookie mode is enabled.');
            $this->cookieMode = true;
        }
    }

    /**
     *
     */
    private function handleCookieStuff()
    {
        $currentLanguageUid = $this->requestedLanguageUid;

        if ($this->isCookieSet()) {
            if (!$this->isCookieInCurrentLanguage() && $this->shouldOverrideCookie()) {
                // Override cookie
                $this->redirectLanguageUid = $currentLanguageUid;
                $this->setCookie($currentLanguageUid);
                return;

            } elseif ($this->isCookieInCurrentLanguage()) {
                // Cookie is in current language
                $this->redirectLanguageUid = $currentLanguageUid;
                return;

            } else {
                // Cookie is not in current language
                $this->redirectLanguageUid = $this->getCookieValue();
            }

            // Override config array by cookie value
            $this->configuration['sys_language'] = $this->getCookieValue();

        } elseif ($this->cookieMode) {

            if ($currentLanguageUid !== $this->redirectLanguageUid) {
                // Set cookie value to target language
                $this->setCookie($this->redirectLanguageUid);
            } else {
                // Set cookie value to current language
                $this->setCookie($currentLanguageUid);
            }

        }
    }

    /**
     * @return bool
     */
    private function isCookieSet(): bool
    {
        return isset($_COOKIE[$this->cookieName]);
    }

    /**
     * @return bool
     */
    private function isCookieInCurrentLanguage(): bool
    {
        return $this->requestedLanguageUid == $_COOKIE[$this->cookieName];
    }

    /**
     * @return bool
     */
    private function shouldOverrideCookie(): bool
    {
        if (isset($this->configuration['overrideCookie']) && $this->configuration['overrideCookie'] == 1) {
            if (GeneralUtility::_GP('setLang') == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $value
     */
    private function setCookie(string $value)
    {
        if ($value === '') {
            setcookie($this->cookieName, $this->configuration['sys_language'], time() + 60 * 60 * 24 * 30, '/');
        } else {
            setcookie($this->cookieName, $value, time() + 60 * 60 * 24 * 30, '/');
        }
    }

    private function getCookieValue(): string
    {
        return $_COOKIE[$this->cookieName] ? $_COOKIE[$this->cookieName] : '';
    }

    private function shouldRedirect(int $sysLanguageUid): bool
    {
        if (!$this->cookieMode) {
            return true;
        }

        if (isset($_COOKIE[$this->cookieName]) && (int)$_COOKIE[$this->cookieName] === $sysLanguageUid) {
            return false;
        }

        return true;
    }

    /**
     * Redirect to a page
     *
     * @param string $target
     * @param string $language
     * @param int $httpResponseCode
     * @throws Exception
     */
    private function redirectToPid(string $target, string $language, int $httpResponseCode)
    {
        if ($language) {
            $languageId = (int)$language;
            $urlParameters = ['L' => intval($language)];
        } else {
            $languageId = 0;
            $urlParameters = [];
        }

        $this->getAdditionalUrlParams($urlParameters);
        $targetPageUid = intval($target);

        if ($targetPageUid) {
            if ($targetPageUid == $GLOBALS['TSFE']->id) {
                // Legacy (only TYPO3 8)
                if ($urlParameters['L']) {

                    if ($this->requestedLanguageUid == $urlParameters['L']) {
                        return;
                    }
                } else {
                    return;
                }
            }

            // Remove ID from urlParameters
            unset($urlParameters['id']);

            $url = $GLOBALS['TSFE']->cObj->getTypoLink_URL($targetPageUid, $urlParameters);
            $url = $GLOBALS['TSFE']->baseUrlWrap($url);
            $url = GeneralUtility::locationHeaderURL($url);

        } elseif ($language >= 0) {

            // Override urlParamter L if cookie is in use
            if ($this->cookieMode) {
                $urlParameters['L'] = $this->getCookieValue();
            }

            // Remove ID from urlParameters
            unset($urlParameters['id']);

            $url = $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, $urlParameters);
            $url = $GLOBALS['TSFE']->baseUrlWrap($url);
            $url = GeneralUtility::locationHeaderURL($url);

        } else {
            throw new Exception(__CLASS__ . ' the configured redirect page is not an integer');
        }

        $this->redirectToUrl($url, $httpResponseCode, $languageId);
    }

    /**
     * @param array $urlParameters
     * @return array
     */
    private function getAdditionalUrlParams(array &$urlParameters): array
    {
        $additionalUrlParams = GeneralUtility::_GET();

        if (is_array($additionalUrlParams) && count($additionalUrlParams)) {
            if (isset($additionalUrlParams['setLang'])) {
                unset ($additionalUrlParams['setLang']);
            }
            $urlParameters = array_merge($additionalUrlParams, $urlParameters);
        }

        return $urlParameters;
    }

    /**
     * This will redirect the user to a new web location. This can be a relative or absolute web path, or it
     * can be an entire URL.
     *
     * @param string $location
     * @param integer $httpResponseCode
     * @param integer $languageId
     * @return void
     */
    public function redirectToUrl(string $location, int $httpResponseCode, int $languageId = 0)
    {
        $this->logger->info(__CLASS__ . ' Will redirect to ' . $location . ' with code ' . $httpResponseCode);

        // Check for redirect recursion
        if (GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL') != $location) {

            // Set cookie if cookieMode is enabled
            // TODO: Is this necessary??? Cookie should only be set in handleCookieStuff()
            if ($this->cookieMode) {
                $this->setCookie($languageId);
            }

            // Clear the output buffer (if any)
            ob_clean();

            // this is the place where Qcodo answers ajax requests
            // Was "DOCUMENT_ROOT" set?
            if (array_key_exists('DOCUMENT_ROOT', $_SERVER) && ($_SERVER['DOCUMENT_ROOT']) AND !headers_sent()) {
                // If so, we're likley using PHP as a Plugin/Module
                // Use 'header' to redirect
                header("Location: $location", true, $httpResponseCode);
                exit;
            } else {
                // We're likely using this as a CGI
                // Use JavaScript to redirect
                printf('<script type="text/javascript">document.location = "%s";</script>', $location);
            }

            // End the Response Script
            exit();
        }
    }
}

