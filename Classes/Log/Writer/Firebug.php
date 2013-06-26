<?php
namespace Bitmotion\Locate\Log\Writer;

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 *
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

# FIXME Zend_Wildfire_*

/**
 * Writes log messages to the Firebug Console via FirePHP.
 *
 *
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Firebug extends AbstractWriter
{

    /**
     * Maps logging priorities to logging display styles
     * @var array
     */
    protected $_priorityStyles = array(\Bitmotion\Locate\Log\Logger::EMERG  => Zend_Wildfire_Plugin_FirePhp::ERROR,
                                       \Bitmotion\Locate\Log\Logger::ALERT  => Zend_Wildfire_Plugin_FirePhp::ERROR,
                                       \Bitmotion\Locate\Log\Logger::CRIT   => Zend_Wildfire_Plugin_FirePhp::ERROR,
                                       \Bitmotion\Locate\Log\Logger::ERR    => Zend_Wildfire_Plugin_FirePhp::ERROR,
                                       \Bitmotion\Locate\Log\Logger::WARN   => Zend_Wildfire_Plugin_FirePhp::WARN,
                                       \Bitmotion\Locate\Log\Logger::NOTICE => Zend_Wildfire_Plugin_FirePhp::INFO,
                                       \Bitmotion\Locate\Log\Logger::INFO   => Zend_Wildfire_Plugin_FirePhp::INFO,
                                       \Bitmotion\Locate\Log\Logger::DEBUG  => Zend_Wildfire_Plugin_FirePhp::LOG);

    /**
     * The default logging style for un-mapped priorities
     * @var string
     */
    protected $_defaultPriorityStyle = Zend_Wildfire_Plugin_FirePhp::LOG;

    /**
     * Flag indicating whether the log writer is enabled
     * @var boolean
     */
    protected $_enabled = true;

    /**
     * Class constructor
     */
    public function __construct()
    {
        if (php_sapi_name()=='cli') {
            $this->setEnabled(false);
        }

        $this->_formatter = new \Bitmotion\Locate\Log\Formatter\Firebug();
    }

    /**
     * Enable or disable the log writer.
     *
     * @param boolean $enabled Set to TRUE to enable the log writer
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->_enabled;
        $this->_enabled = $enabled;
        return $previous;
    }

    /**
     * Determine if the log writer is enabled.
     *
     * @return boolean Returns TRUE if the log writer is enabled.
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Set the default display style for user-defined priorities
     *
     * @param string $style The default log display style
     * @return string Returns previous default log display style
     */
    public function setDefaultPriorityStyle($style)
    {
        $previous = $this->_defaultPriorityStyle;
        $this->_defaultPriorityStyle = $style;
        return $previous;
    }

    /**
     * Get the default display style for user-defined priorities
     *
     * @return string Returns the default log display style
     */
    public function getDefaultPriorityStyle()
    {
        return $this->_defaultPriorityStyle;
    }

    /**
     * Set a display style for a logging priority
     *
     * @param int $priority The logging priority
     * @param string $style The logging display style
     * @return string|boolean The previous logging display style if defined or TRUE otherwise
     */
    public function setPriorityStyle($priority, $style)
    {
        $previous = true;
        if (array_key_exists($priority,$this->_priorityStyles)) {
            $previous = $this->_priorityStyles[$priority];
        }
        $this->_priorityStyles[$priority] = $style;
        return $previous;
    }

    /**
     * Get a display style for a logging priority
     *
     * @param int $priority The logging priority
     * @return string|boolean The logging display style if defined or FALSE otherwise
     */
    public function getPriorityStyle($priority)
    {
        if (array_key_exists($priority,$this->_priorityStyles)) {
            return $this->_priorityStyles[$priority];
        }
        return false;
    }

    /**
     * Log a message to the Firebug Console.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event)
    {
        if (!$this->getEnabled()) {
            return;
        }

        if (array_key_exists($event['priority'],$this->_priorityStyles)) {
            $type = $this->_priorityStyles[$event['priority']];
        } else {
            $type = $this->_defaultPriorityStyle;
        }

        $message = $this->_formatter->format($event);

        $label = isset($event['firebugLabel'])?$event['firebugLabel']:null;

        Zend_Wildfire_Plugin_FirePhp::getInstance()->send($message,
                                                          $label,
                                                          $type,
                                                          array('traceOffset'=>6));
    }
}
