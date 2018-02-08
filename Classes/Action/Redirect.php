<?php

namespace Bitmotion\Locate\Action;

use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * Redirect Action class
 *
 * @TsProperty page integer Page id to redirect to. Dafault:
 * @TsProperty url string Url to redirect to. Dafault:
 * @TsProperty httpResponseCode integer HTTP response code used for redirection. Dafault: 301
 *
 * @author Rene Fritz (typo3-ext@bitmotion.de)
 * @author Florian WEssels (typo3-ext@bitmotion.de)
 * @package    Locate
 * @subpackage Action
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
     * Call the action module
     *
     * @param array $factsArray
     * @param \Bitmotion\Locate\Judge\Decision
     */
    public function Process(&$factsArray, &$decision)
    {
        $httpResponseCode = $this->configArray['httpResponseCode'] ? $this->configArray['httpResponseCode'] : 301;
        $this->redirectLanguageUid = (int)$this->configArray['sys_language'];

        // Initialize Cookie mode if necessary and prepare everything for possible redirects
        $this->initializeCookieMode();
        $this->handleCookieStuff();

        // Skip if no redirect is necessary
        if (!$this->shouldRedirect((int)GeneralUtility::_GP('L'))) {
            return;
        };

        // Try to redirect to page (if not set, it will be the current page) on configured language
        if ($this->configArray['page'] || isset($this->configArray['sys_language'])) {
            $this->RedirectToPid($this->configArray['page'], $this->redirectLanguageUid, $httpResponseCode);
        }

        // Try to redirect by configured URL (and language, if configured)
        if ($this->configArray['url'] && $this->configArray['sys_language']) {
            $this->RedirectToUrl($this->configArray['url'], $httpResponseCode, $this->redirectLanguageUid);
        } elseif ($this->configArray['url']) {
            $this->RedirectToUrl($this->configArray['url'], $httpResponseCode);
        }

        return;
    }

    /**
     * Set CookeMode Param to true if cookieHandling is enables
     */
    private function initializeCookieMode()
    {
        if (isset($this->configArray['cookieHandling']) && $this->configArray['cookieHandling'] == 1) {
            $this->cookieMode = true;
        }
    }

    /**
     *
     */
    private function handleCookieStuff()
    {
        if ($this->isCookieSet()) {
            if (!$this->isCookieInCurrentLanguage() && $this->shouldOverrideCookie()) {
                // Override Cookie
                $this->redirectLanguageUid = (int)GeneralUtility::_GP('L');
                $this->setCookie(GeneralUtility::_GP('L'));
                return;
            } elseif ($this->isCookieInCurrentLanguage()) {
                // Cookie is in Current language
                $this->redirectLanguageUid = (int)GeneralUtility::_GP('L');
                return;
            } else {
                // Cookie is not in current language
                $this->redirectLanguageUid = $this->getCookieValue();
            }

            $this->configArray['sys_language'] = $this->getCookieValue();
        } elseif ($this->cookieMode) {

            $this->setCookie(GeneralUtility::_GP('L'));
        }
    }

    /**
     * @return bool
     */
    private function isCookieSet()
    {
        return isset($_COOKIE[$this->cookieName]);
    }

    /**
     * @return bool
     */
    private function isCookieInCurrentLanguage()
    {
        return GeneralUtility::_GP('L') == $_COOKIE[$this->cookieName];
    }

    /**
     * @return bool
     */
    private function shouldOverrideCookie()
    {
        if (isset($this->configArray['overrideCookie']) && $this->configArray['overrideCookie'] == 1) {
            if (GeneralUtility::_GP('setLang') == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $value
     */
    private function setCookie($value)
    {
        if ($value === null || $value === '') {
            setcookie($this->cookieName, $this->configArray['sys_language'], time() + 60 * 60 * 24 * 30, '/');
        } else {
            setcookie($this->cookieName, $value, time() + 60 * 60 * 24 * 30, '/');
        }
    }

    /**
     * @return string
     */
    private function getCookieValue()
    {
        return $_COOKIE[$this->cookieName] ? $_COOKIE[$this->cookieName] : 0;
    }

    /**
     * @param int $sysLanguageUid
     * @return bool
     */
    private function shouldRedirect($sysLanguageUid)
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
     * @param string $httpResponseCode
     * @throws Exception
     */
    private function RedirectToPid($target, $language, $httpResponseCode)
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
                if ($urlParameters['L']) {
                    if ($GLOBALS['TSFE']->sys_language_uid == $urlParameters['L']) {
                        return;
                    }
                } else {
                    return;
                }
            }

            $url = $GLOBALS['TSFE']->cObj->getTypoLink_URL($targetPageUid, $urlParameters);
            $url = $GLOBALS['TSFE']->baseUrlWrap($url);
            $url = GeneralUtility::locationHeaderURL($url);
        } else {
            if ($language >= 0) {

                // Override urlParamter L if cookie is in use
                if ($this->cookieMode) {
                    $urlParameters['L'] = $this->getCookieValue();
                }

                $url = $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, $urlParameters);
                $url = $GLOBALS['TSFE']->baseUrlWrap($url);
                $url = GeneralUtility::locationHeaderURL($url);

            } else {
                throw new Exception(__CLASS__ . ' the configured redirect page is not an integer');
            }
        }
        $this->RedirectToUrl($url, $httpResponseCode, $languageId);
    }

    /**
     * @param array $urlParameters
     * @return array
     */
    private function getAdditionalUrlParams(&$urlParameters)
    {
        $additionalUrlParams = $GLOBALS['HTTP_GET_VARS'];
        unset ($additionalUrlParams['setLang']);
        $urlParameters = array_merge($additionalUrlParams, $urlParameters);

        return $urlParameters;
    }

    /**
     * This will redirect the user to a new web location. This can be a relative or absolute web path, or it
     * can be an entire URL.
     *
     * @param string $strLocation
     * @param integer $httpResponseCode
     * @return void
     */
    public function RedirectToUrl($strLocation, $httpResponseCode, $languageId = 0)
    {
        $this->Logger->Info(__CLASS__ . " Will redirect to '$strLocation' with code '$httpResponseCode'");

        // Check for redirect recursion
        if (GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL') != $strLocation) {

            // Set cookie if cookieMode is enabled
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
                header("Location: $strLocation", true, $httpResponseCode);
                exit;
            } else {
                // We're likely using this as a CGI
                // Use JavaScript to redirect
                printf('<script type="text/javascript">document.location = "%s";</script>', $strLocation);
            }

            // End the Response Script
            exit();
        }
    }
}

