<?php

namespace Bitmotion\Locate\Action;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * Call the action module
     *
     * @param array $factsArray
     * @param \Bitmotion\Locate\Judge\Decision
     */
    public function Process(&$factsArray, &$decision)
    {
        $httpResponseCode = $this->configArray['httpResponseCode'] ? $this->configArray['httpResponseCode'] : 301;

        $this->initializeCookieMode();
        $this->handleCookieStuff();

        if ($this->configArray['page'] || isset($this->configArray['sys_language'])) {
            $this->RedirectToPid($this->configArray['page'], $this->configArray['sys_language'], $httpResponseCode);
            return;
        }

        if ($this->configArray['sys_language'] && $this->shouldRedirect((int)$this->configArray['sys_language'])) {
            $this->RedirectToUrl($this->configArray['url'], $httpResponseCode,
                (int)$this->configArray['sys_language']);
        } else {
            $this->RedirectToUrl($this->configArray['url'], $httpResponseCode);
        }
    }

    /**
     *
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
                $this->setCookie(GeneralUtility::_GP('L'));
                return;
            } elseif ($this->isCookieInCurrentLanguage()) {
                return;
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
        return $_COOKIE[$this->cookieName];
    }

    /**
     * Redirect to a page
     *
     * @param string $strTarget         Start!23
     * @param string $strLanguage
     * @param string $httpResponseCode
     * @throws Exception
     */
    private function RedirectToPid($strTarget, $strLanguage, $httpResponseCode)
    {
        if ($strLanguage) {
            $languageId = (int)$strLanguage;
            $urlParameters = ['L' => intval($strLanguage)];
        } else {
            $languageId = 0;
            $urlParameters = [];
        }

        $this->getAdditionalUrlParams($urlParameters);

        $intTarget = intval($strTarget);

        if ($intTarget) {
            if ($intTarget == $GLOBALS['TSFE']->id) {
                if ($urlParameters['L']) {
                    if ($GLOBALS['TSFE']->sys_language_uid == $urlParameters['L']) {
                        return;
                    }
                } else {
                    return;
                }
            }

            $strUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL($intTarget, $urlParameters);
            $strUrl = $GLOBALS['TSFE']->baseUrlWrap($strUrl);
            $strUrl = GeneralUtility::locationHeaderURL($strUrl);

        } else {
            if ($strLanguage) {

                if ($urlParameters['L']) {
                    if ($GLOBALS['TSFE']->sys_language_uid == $urlParameters['L']) {
                        return;
                    }
                }

                $strUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, $urlParameters);
                $strUrl = $GLOBALS['TSFE']->baseUrlWrap($strUrl);
                $strUrl = GeneralUtility::locationHeaderURL($strUrl);

            } else {
                throw new Exception(__CLASS__ . ' the configured redirect page is not an integer');
            }
        }

        $this->RedirectToUrl($strUrl, $httpResponseCode, $languageId);
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
}

