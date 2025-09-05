<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Phonevalidationsession_model extends Base_model
{

    protected $table = 'phone_validation_session';
    protected $single_for_view = '';
    protected $primary_key = 'id';
    protected $timestamp = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getLast()
    {
        return $this->db->order_by('expiration', 'DESC')
            ->limit(1)
            ->get($this->table)->row_array();
    }
}
