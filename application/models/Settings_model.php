<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings_model extends Base_model {

    protected $table = 'settings';
    protected $primary_key = 'setting_id';
    protected $timestamp = false;

    /**
     * Datatables Columns.
     *
     * @var array
     */
    protected $column_order = ['setting_id', 'setting_data'];
    protected $column_search = ['setting_id', 'setting_data'];
    protected $dates = ['setting_edited'];

    public function validation_rules($edit = false) {
        $config = array(
            array(
                'field' => 'setting_id',
                'label' => 'clave',
                'rules' => 'required',
            ),
        );

        return $config;
    }

}
