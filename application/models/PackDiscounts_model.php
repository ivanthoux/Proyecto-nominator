<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PackDiscounts_model extends Base_model
{

    protected $table = 'pack_discounts';
    protected $single_for_view = 'Descuento pago en término';
    protected $primary_key = 'packdiscount_id';
    protected $timestamp = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getDiscount($pack, $sessions, $created_at)
    {
        $this->db->select('packdiscount_value');

        return $this->db->where($sessions . ' between packdiscount_min_sessions and packdiscount_max_sessions', null, false)
            ->where('(("' . $created_at . '" between packdiscount_from and packdiscount_to) OR (packdiscount_from <= "' . $created_at . '" and packdiscount_to is null))', null, false)
            ->where('packdiscount_pack', $pack)
            ->order_by('packdiscount_max_sessions')
            ->limit(1)
            ->get($this->table)->row_array();
    }

    public function getActives($pack, $created_at = false)
    {
        return $this->db->select('*')
            ->where('(("' . ($created_at ? $created_at : 'CURRENT_TIMESTAMP') . '" between packdiscount_from and packdiscount_to) OR (packdiscount_from <= "' . ($created_at ? $created_at : 'CURRENT_TIMESTAMP') . '" and packdiscount_to is null))', null, false)
            ->where('packdiscount_pack', $pack)
            ->order_by('packdiscount_max_sessions')
            ->get($this->table)->result_array();
    }
}
