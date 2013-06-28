<?php
namespace Bitmotion\Locate\Log;


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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 *
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Logger
{
    /**
     * Emergency: system is unusable
     * @var integer
     */
    const EMERG   = 0;

    /**
     * Alert: action must be taken immediately
     * @var integer
     */
    const ALERT   = 1;

    /**
     * Critical: critical conditions
     * @var integer
     */
    const CRIT    = 2;

    /**
     * Error: error conditions
     * @var integer
     */
    const ERR     = 3;

    /**
     * Warning: warning conditions
     * @var integer
     */
    const WARN    = 4;

    /**
     * Notice: normal but significant condition
     * @var integer
     */
    const NOTICE  = 5;

    /**
     * Informational: informational messages
     * @var integer
     */
    const INFO    = 6;

    /**
     * Debug: debug messages
     * @var integer
     */
    const DEBUG   = 7;

    /**
     * @var array of priorities where the keys are the
     * priority numbers and the values are the priority names
     */
    protected $_priorities = array();

    /**
     * @var array of Zend_Log_Writer_Abstract
     */
    protected $_writers = array();

    /**
     * @var array of Zend_Log_Filter_Interface
     */
    protected $_filters = array();

    /**
     * @var array of extra log event
     */
    protected $_extras = array();

    /**
     * Class constructor.  Create a new logger
     *
     * @param Zend_Log_Writer_Abstract|null  $writer  default writer
     */
    public function __construct(\Bitmotion\Locate\Log\Writer\AbstractWriter $writer = null)
    {
        $r = new \ReflectionClass($this);
        $this->_priorities = array_flip($r->getConstants());

        if ($writer !== null) {
            $this->addWriter($writer);
        }
    }

    /**
     * Class destructor.  Shutdown log writers
     *
     * @return void
     */
    public function __destruct()
    {
        foreach($this->_writers as $writer) {
            $writer->shutdown();
        }
    }

    /**
     * Undefined method handler allows a shortcut:
     *   $log->priorityName('message')
     *     instead of
     *   $log->log('message', Logger::PRIORITY_NAME)
     *
     * @param  string  $method  priority name
     * @param  string  $params  message to log
     * @return void
     * @throws \Bitmotion\Locate\Log\Exception
     */
    public function __call($method, $params)
    {
        $priority = strtoupper($method);
        if (($priority = array_search($priority, $this->_priorities)) !== false) {
            $this->log(array_shift($params), $priority);
        } else {


            throw new \Bitmotion\Locate\Log\Exception('Bad log priority');
        }
    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return void
     * @throws \Bitmotion\Locate\Log\Exception
     */
    public function log($message, $priority)
    {
        // sanity checks
        if (empty($this->_writers)) {


            throw new \Bitmotion\Locate\Log\Exception('No writers were added');
        }

        if (! isset($this->_priorities[$priority])) {


            throw new \Bitmotion\Locate\Log\Exception('Bad log priority');
        }
		$this->_extras = array();

        // pack into event required by filters and writers
        $event = array_merge(array('timestamp'    => date('c'),
                                    'message'      => (string)$message,
                                    'priority'     => $priority,
                                    'priorityName' => $this->_priorities[$priority]),
                              $this->_extras);

        // abort if rejected by the global filters
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // send to each writer
        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
    }

	/**
	 * Log a message at a priority and add some data
	 *
	 * @param  string   $message   Message to log
	 * @param  mixed   	 $data      Any data
	 * @param  integer  $priority  Priority of message
	 * @return void
	 * @throws \Bitmotion\Locate\Log\Exception
	 */
	public function LogData($message, $data, $priority=self::INFO)
	{
		$this->_extras['data'] = $data;

		return $this->log($message, $priority);
	}

    /**
     * Add a custom priority
     *
     * @param  string   $name      Name of priority
     * @param  integer  $priority  Numeric priority
     * @throws \Bitmotion\Locate\Log\Exception
     */
    public function addPriority($name, $priority)
    {
        // Priority names must be uppercase for predictability.
        $name = strtoupper($name);

        if (isset($this->_priorities[$priority])
            || array_search($name, $this->_priorities)) {

            throw new \Bitmotion\Locate\Log\Exception('Existing priorities cannot be overwritten');
        }

        $this->_priorities[$priority] = $name;
    }

    /**
     * Add a filter that will be applied before all log writers.
     * Before a message will be received by any of the writers, it
     * must be accepted by all filters added with this method.
     *
     * @param  int|Zend_Log_Filter_Interface $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new \Bitmotion\Locate\Log\Filter\Priority($filter);
        } elseif(!is_object($filter) || ! $filter instanceof Zend_Log_Filter_Interface) {


            throw new \Bitmotion\Locate\Log\Exception('Invalid filter provided');
        }

        $this->_filters[] = $filter;
    }

    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param  \Zend_Log_Writer_Abstract $writer
     * @param string $strName
     * @return void
     */
    public function AddWriter(\Bitmotion\Locate\Log\Writer\AbstractWriter $writer, $strName=null)
    {
    	if ($strName) {
        	$this->_writers[$strName] = $writer;
    	} else {
    		$this->_writers[] = $writer;
    	}
    }


    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param string $strName
     * @return \Zend_Log_Writer_Abstract
     */
    public function GetWriter($strName)
    {
    	return $this->_writers[$strName];
    }

    /**
     * Set an extra item to pass to the log writers.
     *
     * @param  $name    Name of the field
     * @param  $value   Value of the field
     * @return void
     */
    public function setEventItem($name, $value) {
        $this->_extras = array_merge($this->_extras, array($name => $value));
    }

}
