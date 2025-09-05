<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Regions_model extends Base_model
{

    protected $table = 'regions';
    protected $single_for_view = 'Provincias';
    protected $primary_key = 'id';
    protected $timestamp = false;
    protected $order = [];
    protected $column_order = [];
    protected $column_search = [];

    public function __construct()
    {
        parent::__construct();
    }


    public function getByCountry($country_id)
    {
        return $this->db->where('country_id', $country_id)
            ->get($this->table)->row_array();
    }
}
