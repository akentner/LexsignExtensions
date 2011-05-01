<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Log extends Zend_Application_Resource_Log
{

    public function init()
    {
        foreach ($this->getOptions() as $option) {
            if (array_key_exists('writerParams', $option)
                && array_key_exists('stream', $option['writerParams'])) {
                $file = $option['writerParams']['stream'];
                $dir = dirname($file);
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
            }
        }

        $log = parent::init();
        $log->addPriority('INIT', 8);

        $log->log(__METHOD__ . ' (START)', 8);

        foreach ($this->getOptions() as $optionName => $option) {
            $log->log('  -- init Log: ' . $optionName . ' as ' .
                $option['writerName'], 8);
        }

        Zend_Registry::set("log", $log);
        $log->log(__METHOD__ . ' (END)', 8);
        return $log;
    }

}