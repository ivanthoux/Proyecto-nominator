<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PackRules_model extends Base_model {

    protected $table = 'pack_rules';
    protected $single_for_view = 'regla del producto';
    protected $primary_key = 'packrule_id';
    protected $timestamp = false;
    protected $order = ['rule_name', 'desc'];
    protected $column_order = ['rule_name', 'rule_type', 'rule'];
    protected $column_search = ['rule_name'];

    public function __construct() {
        parent::__construct();
        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }

    public function customLinks($row, $rol) {
        $edit_link = '&nbsp;';
        $remove_link = '';
        if ($row['pack_active']) {
            $edit_link = '<a href="' . site_url('packrules/form/' . $row['packrule_pack'] . '/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
            if ($rol == 'Super') {
                $link = "app.deleteConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','packrules/remove/" . $row[$this->primary_key] . "')";
                $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
            }
        }
        return $edit_link
                . $remove_link;
    }

    public function extend_datatable_query() {
        $get = $this->input->get();
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }

        $this->db->select('pack_rules.*, r.rule_name, r.rule_type, p.pack_active');
        $this->db->join('rules as r', 'r.rule_id = packrule_rule');
        $this->db->join('packs as p', 'p.pack_id = packrule_pack');

        if (!empty($get['pack_id'])) {
            $this->db->where('packrule_pack = ' . $get['pack_id'], false, false);
        }
    }

    public function get_by_id($id) {
        return $this->db->select('pack_rules.*, rule_type')
                ->join('rules', 'packrule_rule = rule_id')
                ->where('packrule_id', $id)
                ->get($this->table)->row_array();
    }
    
    public function get_by_pack_rule($pack, $rule) {
        return $this->db->where('packrule_pack', $pack)
                ->where('packrule_rule', $rule)
                ->get($this->table)->result_array();
    }
    
    public function get_by_rule($rule) {
        return $this->db->where('packrule_rule', $rule)
                ->get($this->table)->result_array();
    }

    public function rule_type_attribute($value, $row) {
        switch ($row['rule_type']) {
            case "1":
                return 'Valor Mínimo';
            case "2":
                return 'Valor Máximo';
            default:
                return 'SI/NO';
        }
    }

    public function rule_attribute($value, $row) {
        switch ($row['rule_type']) {
            case "1":
            case "2":
                return money_formating($row['packrule_value']);
            default:
                return ($row['packrule_value'] == 1 ? 'Si' : 'No');
        }
    }

}
