<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Log extends Zend_Application_Resource_Log
{

    protected $_log;

    /**
     * initializes Zend_Log as resource
     *
     * @return Zend_Log
     */
    public function init()
    {
        foreach ($this->getOptions() as $option) {
            if (array_key_exists('writerParams', $option)
                && array_key_exists('stream', $option['writerParams'])) {
                $file = $option['writerParams']['stream'];
                $dir = dirname($file);
                $this->_initDir($dir);
            }
        }
        $this->_log = parent::init();
        $this->_log->addPriority('INIT', 8);
        $this->_log->log(__METHOD__ . ' (START)', 8);

        $this->_logOptions();
        Zend_Registry::set("log", $this->_log);

        $this->_log->log(__METHOD__ . ' (END)', 8);
        return $this->_log;
    }

    /**
     * initializes a directory
     *
     * @param string $dir directory to initialize
     */
    protected function _initDir($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * log logging options
     *
     */
    protected function _logOptions()
    {
        foreach ($this->getOptions() as $optionName => $option) {
            $this->_log->log('  -- init Log: ' . $optionName . ' as ' .
                $option['writerName'], 8);
        }
    }
}