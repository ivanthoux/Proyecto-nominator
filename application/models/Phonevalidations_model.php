<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Phonevalidations_model extends Base_model
{

    protected $table = 'phone_validations';
    protected $single_for_view = '';
    protected $primary_key = 'phonevalidation_id';
    protected $timestamp = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function validate($id, $code)
    {
        $row = $this->db->where('phonevalidation_id', $id)
            ->where('phonevalidation_code', $code)->get($this->table)->row_array();
        if ($row) {
            $this->db->set('phonevalidation_validated_at', date('Y-m-d H:i:s'));
            $this->db->where('phonevalidation_id', $id);
            $this->db->update($this->table);
        }
        return $row;
    }

    public function getLast($phone)
    {
        return $this->db->where('phonevalidation_phone', '54' . $phone)
            ->where('phonevalidation_code IS NOT null', null, false)
            ->order_by('phonevalidation_created_at', 'DESC')
            ->limit(1)
            ->get($this->table)->row_array();
    }
}
