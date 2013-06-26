<?php

namespace Bitmotion\System;



/**
 * Handle class autoloading
 *
 * Neede because of the dumb TYPO3 autoloader
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @see http://php.net/manual/en/function.spl-autoload-register.php
 * @see http://php.net/manual/en/function.set-include-path.php
 */
abstract class Autoloader {


	/**
	 * Class File Array - used by self::autoLoad to more quickly load
	 * core class objects without making a file_exists call.
	 *
	 * @var array ClassFile
	 */
	protected static $classFile = array();


	/**
	 * Register {@link Autoload()} with spl_autoload()
	 *
	 * @param boolean $enabled (optional)
	 * @return void
	 * @throws Exception if spl_autoload() is not found
	 * @see http://php.net/manual/en/function.spl-autoload-register.php
	 */
	public static function RegisterAutoload()
	{
		$extAutoloadPath = \t3lib_extMgm::extPath('locate').'ext_autoload.php';
		self::$classFile = include($extAutoloadPath);
		spl_autoload_register(array('\Bitmotion\System\Autoloader', 'Autoload'));
	}


	/**
	 * This is called by the PHP5 Autoloader.
	 *
	 * This is called just too much because class_exists() call the autloader unless the secend parameter is false, which is not the case in the T3 source code.
	 *
	 * @param string $strClassName Class name
	 * @return boolean whether or not a class was found / included
	 */
	public static function Autoload($strClassName)
	{
		if (substr($strClassName, 0 ,3)==='ux_') return false;
		if (substr($strClassName, 0 ,5)==='user_') return false;
		if (substr($strClassName, 0 ,6)==='t3lib_') return false;
		if (substr($strClassName, 0 ,6)==='tslib_') return false;

		// remove backslash from the beginning to find classes with namespace
		if ($strClassName{0} == '\\') {
			$strClassName = substr($strClassName, 1);
		}

		if (array_key_exists(($strClassName), self::$classFile)) {
			if (false === self::$classFile[strtolower($strClassName)]) {

				trigger_error('No valid path was registered for class \''.$strClassName.'\'.', E_USER_WARNING);
				return false;
			}

			require(self::$classFile[($strClassName)]);
			return true;
		}

		return false;
	}


}



