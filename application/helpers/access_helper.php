<?php

if (!function_exists('can')) {

    /**
     * Class Access_manager Permite administrar permisos en la aplicación
     */
    class Access_manager {

        private static $instancia;
        private $abilities = [];
        private $user_logged;

        private function __construct() {
            
        }

        public static function getInstance() {
            if (!self::$instancia instanceof self) {
                self::$instancia = new self;
            }
            return self::$instancia;
        }

        public function setUserLogged($user_logged) {
            $this->user_logged = $user_logged;
        }

        public function has($ability) {
            return isset($this->abilities[$ability]);
        }

        public function allows($ability) {
            if ($this->has($ability)) {
                return call_user_func($this->abilities[$ability], $ability, $this->user_logged);
            }
            return $this->user_logged['user_rol'] == "super" ? true : false;
        }

        public function define($ability, callable $callback) {
            $this->abilities[$ability] = $callback;
        }

    }

    function can($ability) {
        $accessManager = Access_manager::getInstance();
        return $accessManager->allows($ability);
    }

}
