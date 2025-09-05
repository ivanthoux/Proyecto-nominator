<?php
if (!function_exists('asset_url'))
{
    function asset_url()
    {
        $CI =& get_instance();
        return base_url() . 'assets/';
    }
}
if (!function_exists('img_url'))
{
    function img_url()
    {
        $CI =& get_instance();
        return base_url() . 'assets/img/';
    }
}
if (!function_exists('server_folder'))
{
    function server_folder()
    {
        $CI =& get_instance();
        return $CI->config->item('server_folder');
    }
}
