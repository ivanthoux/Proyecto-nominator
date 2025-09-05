<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | Hooks
  | -------------------------------------------------------------------------
  | This file lets you define "hooks" to extend CI without hacking the core
  | files.  Please see the user guide for info:
  |
  |	http://codeigniter.com/user_guide/general/hooks.html
  |
 */
$hook['post_controller_constructor'] = function() {
    if (!is_cli()) {
        /* Load permissions */
        $ci = & get_instance();
        if ($ci->session->userdata('admin_logged')) {
            $user_logged = $ci->session->userdata();
            $ci->load->model('Permissions_model');
            $access_manager = Access_manager::getInstance();
            $access_manager->setUserLogged($user_logged);
            $permissions = $ci->Permissions_model->get_all();
            foreach ($permissions as $permission) {
                $access_manager->define($permission["permission_key"], function($ability, $user) {
                    return $user["user_rol"] == "super" ? true : in_array($ability, $user["permissions"]);
                });
            }
        }
    }
};
