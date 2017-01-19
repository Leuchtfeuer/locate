<?php
namespace Bitmotion\Locate\Action;



/**
 * Redirect Action class
 *
 * @TsProperty page integer Page id to redirect to. Dafault:
 * @TsProperty url string Url to redirect to. Dafault:
 * @TsProperty httpResponseCode integer HTTP response code used for redirection. Dafault: 301
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Action
 */
class Redirect extends AbstractAction {


	/**
	 * @var bool
	 */
	private $ignoreRedirects = false;

	/**
	 * Call the action module
	 *
	 * @param array $factsArray
	 * @param \Bitmotion\Locate\Judge\Decision
	 */
	public function Process(&$factsArray, &$decision)
	{
		$httpResponseCode = $this->configArray['httpResponseCode'] ? $this->configArray['httpResponseCode'] : 301;

		$this->checkIfRedirectIsAllowed($this->configArray);

		if ($this->configArray['page'] OR $this->configArray['sys_language'] ) {
			$this->RedirectToPid($this->configArray['page'], $this->configArray['sys_language'], $httpResponseCode);
			return;
		}

		if ($this->configArray['sys_language'] && $this->shouldRedirect((int)$this->configArray['sys_language'])) {
			$this->RedirectToUrl($this->configArray['url'], $httpResponseCode, (int)$this->configArray['sys_language']);
		}

		$this->RedirectToUrl($this->configArray['url'], $httpResponseCode);

	}

	/**
	 * @param array $configuration
	 */
	private function checkIfRedirectIsAllowed($configuration)
	{
		if (isset($configuration['cookieHandling']) && $configuration['cookieHandling'] == 1) {
			$this->ignoreRedirects = true;
		}
	}

	/**
	 * @param int $sysLanguageUid
	 * @return bool
	 */
	private function shouldRedirect($sysLanguageUid)
	{
		if (!$this->ignoreRedirects) {
			return true;
		}

		if (isset($_COOKIE['bm_locate']) && (int)$_COOKIE['bm_locate'] === $sysLanguageUid) {
			return false;
		}

		return true;
	}


	/**
	 * Redirect to a page
	 *
	 * @return    void
	 */
	private function RedirectToPid($strTarget, $strLanguage, $httpResponseCode)
	{
			if ($strLanguage) {
				$languageId = (int)$strLanguage;
				$urlParameters = array('L' => intval($strLanguage));
			} else {
				$languageId = 0;
				$urlParameters = array();
			}
			if (!$this->shouldRedirect($languageId)) {
				return;
			}

			$intTarget = intval($strTarget);

			if ($intTarget) {
				if ($intTarget == $GLOBALS['TSFE']->id) {
					if($urlParameters['L']) {
						if ($GLOBALS['TSFE']->sys_language_uid == $urlParameters['L']) {
							return;
						}
					} else {
						return;
					}
				}

				$strUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL($intTarget, $urlParameters);
				$strUrl = $GLOBALS['TSFE']->baseUrlWrap($strUrl);
				$strUrl = \t3lib_div::locationHeaderURL($strUrl);

			} else if ($strLanguage) {

				if($urlParameters['L']) {
					if ($GLOBALS['TSFE']->sys_language_uid == $urlParameters['L']) {
						return;
					}
				}

				$strUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, $urlParameters);
				$strUrl = $GLOBALS['TSFE']->baseUrlWrap($strUrl);
				$strUrl = \t3lib_div::locationHeaderURL($strUrl);

			} else {
				throw new Exception(__CLASS__ . ' the configured redirect page is not an integer');
			}

			$this->RedirectToUrl($strUrl, $httpResponseCode, $languageId);
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
		if (\t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') != $strLocation) {
			// Clear the output buffer (if any)
			setcookie('bm_locate', $languageId, time() + 60 * 60 * 24 * 30);
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

