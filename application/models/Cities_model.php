<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cities_model extends Base_model
{

    protected $table = 'cities';
    protected $single_for_view = 'Localidades';
    protected $primary_key = 'id';
    protected $timestamp = false;
    protected $order = [];
    protected $column_order = [];
    protected $column_search = [];

    public function __construct()
    {
        parent::__construct();
    }


    public function getByRegion($region_id)
    {
        return $this->db->where('region_id', $region_id)
            ->get($this->table)->result_array();
    }
}
