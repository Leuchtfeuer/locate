<?php

die();

if (!isCli()) {
	echo "<pre>\n";
}

$outputExtAutoload = str_replace(');?>', '', file_get_contents(dirname(__FILE__).'/ext_autoload-setup-tpl.php'));

$searchDir = dirname(__FILE__).'/Classes/';
$nextDir = dirname(__FILE__).'/';

$fileArray = _get_files($searchDir);


foreach ($fileArray as $file) {

	$shortFilePath = str_replace($nextDir, '', $file);

	if (strpos($shortFilePath, '.svn'))
		continue;

	echo basename($file) . ":\n";

	$strContent = file_get_contents($file);

	$strNamespace = '';
	if (preg_match('#^\s*namespace\s+([a-zA-Z0-9_\\\]+)#mi', $strContent, $matches)) {
		$strNamespace = $matches[1] . '\\';
	}

	preg_match_all('#^\s*(abstract)*\s*(class|interface)\s+([a-zA-Z0-9_]+)#mi', $strContent, $matches, PREG_SET_ORDER);

	foreach ($matches as $match) {
		$className = $match[3];
		if ($className) {

			echo '                         ' . $strNamespace . $className .  "\n";
			if (substr($className, 0 ,5)!=='Zend_' OR $className === 'Zend_Loader') {
				$outputExtAutoload .= "\n\t'" . ($strNamespace.$className) . "' => \$PATH_extension.'$shortFilePath',";
			}
		}
	}
}

$outputExtAutoload .= "\n\n);\n?>";

file_put_contents(dirname(__FILE__).'/ext_autoload.php', $outputExtAutoload);

/**
 * Returns an array with the names of folders in a specific path
 *
 * @param	string		Path to list directories from
 * @return	array		Returns an array with the directory entries as values. If no path, the return value is nothing.
 */
function _get_files($strPath, $filearray=array())
{
	if ($strPath)	{
		$d = @dir($strPath); /*@*/
		if (is_object($d))	{
			while($entry = $d->read()) {
				if (@is_dir($strPath.'/'.$entry) && $entry!= '..' && $entry!= '.')	{ /*@*/
				    $filearray = _get_files($strPath.$entry.'/', $filearray);
				} elseif (@is_file($strPath.$entry) && $entry!= '..' && $entry!= '.')	{ /*@*/
				    $filearray[] = $strPath.$entry;
				}
			}
			$d->close();
		}
		return $filearray;
	}
}

function isCli() {

	if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
		return true;
	} else {
		return false;
	}
}