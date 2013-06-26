<?php
namespace Bitmotion\Locate\Log\Writer;

/**
 * Cross Media Publishing - CMP3
 * www.cross-media.net
 *
 * LICENSE
 *
 * This source file is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * @subpackage Log
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */




/**
 * Writes log messages to memory
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Logging
 * @package    CMP3
 */
class Memory extends AbstractWriter
{

	protected $strLog = '';


    /**
     * Class Constructor
     *
     */
    public function __construct()
    {
        $this->_formatter = new \Bitmotion\Locate\Log\Formatter\Simple();
    }

    /**
     * Close the stream resource.
     *
     * @return void
     */
    public function GetLog()
    {
        return $this->strLog;
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        $line = $this->_formatter->format($event);

        $this->strLog .= $line . "\n";
    }

}






