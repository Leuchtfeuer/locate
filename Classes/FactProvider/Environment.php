<?php

namespace Bitmotion\Locate\FactProvider;

use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Provide multiple environment data from t3lib_div::getIndpEnv()
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage FactProvider
 */
class Environment extends AbstractFactProvider
{

    /*
     Conventions:
    output from parse_url():
    URL:	http://username:password@192.168.1.4:8080/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value#link1
    [scheme] => 'http'
    [user] => 'username'
    [pass] => 'password'
    [host] => '192.168.1.4'
    [port] => '8080'
    [path] => '/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/'
    [query] => 'arg1,arg2,arg3&p1=parameter1&p2[key]=value'
    [fragment] => 'link1'Further definition: [path_script] = '/typo3/32/temp/phpcheck/index.php'
    [path_dir] = '/typo3/32/temp/phpcheck/'
    [path_info] = '/arg1/arg2/arg3/'
    [path] = [path_script/path_dir][path_info]Keys supported:URI______:
    REQUEST_URI		=	[path]?[query]		= /typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value
    HTTP_HOST		=	[host][:[port]]		= 192.168.1.4:8080
    SCRIPT_NAME		=	[path_script]++		= /typo3/32/temp/phpcheck/index.php		// NOTICE THAT SCRIPT_NAME will return the php-script name ALSO. [path_script] may not do that (eg. '/somedir/' may result in SCRIPT_NAME '/somedir/index.php')!
    PATH_INFO		=	[path_info]			= /arg1/arg2/arg3/
    QUERY_STRING	=	[query]				= arg1,arg2,arg3&p1=parameter1&p2[key]=value
    HTTP_REFERER	=	[scheme]://[host][:[port]][path]	= http://192.168.1.4:8080/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value
    (Notice: NO username/password + NO fragment)CLIENT____:
    REMOTE_ADDR		=	(client IP)
    REMOTE_HOST		=	(client host)
    HTTP_USER_AGENT	=	(client user agent)
    HTTP_ACCEPT_LANGUAGE	= (client accept language)SERVER____:
    SCRIPT_FILENAME	=	Absolute filename of script		(Differs between windows/unix). On windows 'C:\\blabla\\blabl\\' will be converted to 'C:/blabla/blabl/'Special extras:
    TYPO3_HOST_ONLY =		[host] = 192.168.1.4
    TYPO3_PORT =			[port] = 8080 (blank if 80, taken from host value)
    TYPO3_REQUEST_HOST = 		[scheme]://[host][:[port]]
    TYPO3_REQUEST_URL =		[scheme]://[host][:[port]][path]?[query] (scheme will by default be "http" until we can detect something different)
    TYPO3_REQUEST_SCRIPT =  	[scheme]://[host][:[port]][path_script]
    TYPO3_REQUEST_DIR =		[scheme]://[host][:[port]][path_dir]
    TYPO3_SITE_URL = 		[scheme]://[host][:[port]][path_dir] of the TYPO3 website frontend
    TYPO3_SITE_PATH = 		[path_dir] of the TYPO3 website frontend
    TYPO3_SITE_SCRIPT = 		[script / Speaking URL] of the TYPO3 website
    TYPO3_DOCUMENT_ROOT =		Absolute path of root of documents: TYPO3_DOCUMENT_ROOT.SCRIPT_NAME = SCRIPT_FILENAME (typically)
    TYPO3_SSL = 			Returns TRUE if this session uses SSL/TLS (https)
    TYPO3_PROXY = 			Returns TRUE if this session runs over a well known proxyNotice: [fragment] is apparently NEVER available to the script!Testing suggestions:
    - Output all the values.
    - In the script, make a link to the script it self, maybe add some parameters and click the link a few times so HTTP_REFERER is seen
    - ALSO TRY the script from the ROOT of a site (like 'http://www.mytest.com/' and not 'http://www.mytest.com/test/' !!)
    */


    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     */
    public function Process(&$factsArray)
    {
        /** @var array $envFactArray */
        $envFactArray = GeneralUtility::getIndpEnv('_ARRAY');

        foreach ($envFactArray as $key => $value) {
            $factPropertyName = $this->GetFactPropertyName($key);
            $factsArray[$factPropertyName] = $value;
        }

        foreach ($_SERVER as $key => $value) {
            $factsArray['SERVER_' . $key] = $value;
        }
    }

}
