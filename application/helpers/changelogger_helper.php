<?php

if (!function_exists('changeLogCreate') || !function_exists('changeLogChange')) {

    class ChangeLogger {

        private static $instancia;
        private $log = "";
        private $previous;

        public function __construct() {
            
        }

        public static function getInstance() {
            if (!self::$instancia instanceof self) {
                self::$instancia = new self;
            }
            return self::$instancia;
        }

        private function _add(&$log, $key, $value) {
            $log .= ((empty($log) ? "" : ", ")) . $key . ": " . $value;
        }

        private function _change(&$log, $key, $prev, $new) {
            $log .= ((empty($log) ? "" : ", ")) . $key . ": " . $prev . " -> " . $new;
        }

        public function create($model, $create) {
            $log = "";
            foreach ($create as $key => $value) {
                $this->_add($log, $key, $value);
            }
            $this->log = "Se crea: $log";
        }

        public function change($model, $toMerge = array()) {
            $log = "";
            if ($this->previous != null) {
                foreach ($this->previous as $key => $prev) {
                    foreach ($toMerge as $keyN => $new) {
                        if ($key === $keyN && $prev != $new) {
                            $this->_change($log, $key, $prev, $new);
                            break;
                        }
                    }
                }
            }
            $this->log = (empty($log) ? "" : "Se Modifica: $log");
        }

        public function remove($model, $remove) {
            $log = "";
            foreach ($remove as $key => $value) {
                $this->_add($log, $key, $value);
            }
            $this->log = "Se Eliminó: " . $log;
        }

        public function setPrevious($previous) {
            $this->previous = $previous;
        }

        public function getLog() {
            return $this->log;
        }

    }

    function changeLogCreate($model, $create) {
        $logger = ChangeLogger::getInstance();
        $logger->create($model, $create);
        return $logger->getLog();
    }

    function changeLogChange($model, $toMerge) {
        $logger = ChangeLogger::getInstance();
        $logger->change($model, $toMerge);
        return $logger->getLog();
    }

    function changeLogRemove($model, $remove) {
        $logger = ChangeLogger::getInstance();
        $logger->remove($model, $remove);
        return $logger->getLog();
    }

    function changeLogSetPrevious($previous) {
        $logger = ChangeLogger::getInstance();
        $logger->setPrevious($previous);
    }

}
