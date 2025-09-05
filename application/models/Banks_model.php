<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Banks_model extends Base_model
{

    protected $table = 'banks';
    protected $single_for_view = '';
    protected $primary_key = 'bank_id';
    protected $timestamp = false;
    protected $column_order = [];
    protected $column_search = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_name($name)
    {
        return $this->db->from($this->table)->where("bank_name LIKE '%$name%'")->get()->row_array();
    }
}
