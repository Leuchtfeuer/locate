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
	 * Call the action module
	 *
	 * @param array $factsArray
	 * @param \Bitmotion\Locate\Judge\Decision
	 */
	public function Process(&$factsArray, &$decision)
	{
		$httpResponseCode = $this->configArray['httpResponseCode'] ? $this->configArray['httpResponseCode'] : 301;

		if ($strTarget = $this->conf['page']) {
			$this->RedirectToPid($strTarget, $httpResponseCode);
		}
		$this->RedirectToUrl($this->configArray['url'], $httpResponseCode);
	}



	/**
	 * Redirect to a page
	 *
	 * @return    void
	 */
	private function RedirectToPid($strTarget, $httpResponseCode)
	{
			if (intval($strTarget)) {
				$strUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL(intval($strTarget));
				$strUrl = $GLOBALS['TSFE']->baseUrlWrap($strUrl);
				$strUrl = \t3lib_div::locationHeaderURL($strUrl);

			} else {
				throw new Exception(__CLASS__ . ' the configured redirect page is not an integer');
			}

			$this->RedirectToUrl($strUrl, $httpResponseCode);
	}


	/**
	 * This will redirect the user to a new web location. This can be a relative or absolute web path, or it
	 * can be an entire URL.
	 *
	 * @param string $strLocation
	 * @param integer $httpResponseCode
	 * @return void
	 */
	public function RedirectToUrl($strLocation, $httpResponseCode)
	{
		$this->Logger->Info(__CLASS__ . " Will redirect to '$strLocation' with code '$httpResponseCode'");

		// Check for redirect recursion
		if (\t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') != $strLocation) {
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

