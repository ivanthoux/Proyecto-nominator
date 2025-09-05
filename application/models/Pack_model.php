<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pack_model extends Base_model
{

    protected $table = 'packs';
    protected $single_for_view = 'Paquete';
    protected $primary_key = 'pack_id';
    protected $timestamp = false;
    protected $column_order = ['pack_name', 'pack_sessions', 'pack_price', 'pack_commision'];
    protected $column_search = ['pack_name'];

    public function __construct()
    {
        parent::__construct();
        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }

    public function customLinks($row, $rol)
    {
        $edit_link = '';
        $remove_link = '';
        $rules_link = ''; //<a href="' . site_url('packrules/all/' . $row['pack_id']) . '" class="btn btn-default" title="Reglas del Producto"><span class="fa fa-bullhorn"></span></a>';
        if ($row['pack_active']) {
            $edit_link = '<a href="' . site_url('packs/form/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
            if ($rol == 'Super' || $rol === 'Administrador') {
                $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','packs/desactive/" . $row[$this->primary_key] . "')";
                $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
            }
        } else {
            $edit_link = '<a href="' . site_url('packs/form/' . $row[$this->primary_key]) . '" class="btn btn-info" title="Ver"><span class="fa fa-eye"></span></a>';
            if ($rol == 'Super' || $rol === 'Administrador') {
                $link = "app.activeConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','packs/active/" . $row[$this->primary_key] . "')";
                $remove_link = '<a class="btn bg-olive" onclick="' . $link . '" title="Re-Activar"><span class="fa fa-check"></span></a>';
            }
        }
        return $rules_link
            . $edit_link
            . $remove_link;
    }

    public function get_all($grouped = false, $onlyActive = false, $rules = false)
    {
        if ($grouped) {
            $this->db->select('pack_id, pack_name')->group_by('pack_name')->order_by('pack_name', 'ASC');
        } else {
            $this->db->order_by('pack_name ASC');
        }
        if ($onlyActive) {
            $this->db->where('pack_active', true);
        }

        $data = $this->db->get($this->table)->result_array();

        if ($rules) {
            foreach ($data as $key => $pack) {
                $rule = $this->db->select('packrule_id, rule_name, rule_type, rule_description')
                    ->where('packrule_pack', $pack['pack_id'])
                    ->join('rules as r', 'packrule_rule = rule_id')
                    ->order_by('rule_name')
                    ->get('pack_rules')->result_array();
                foreach ($rule as $r) {
                    $r['rule_name'] = htmlentities($r['rule_name'], ENT_NOQUOTES, 'UTF-8');
                    $r['rule_description'] = htmlentities($r['rule_description'], ENT_NOQUOTES, 'UTF-8');
                }
                $data[$key]['pack_rules'] = $rule;
            }
        }

        // $this->dd($data);
        // $this->dd($this->db->get_compiled_select($this->table));
        return $data;
    }

    public function pack_price_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function pack_commision_attribute($value, $row)
    {
        return '% ' . ($value);
    }

    public function get_filters()
    {
        $options['pack_active'][] = array('title' => 'Activos', 'value' => 1);
        $options['pack_active'][] = array('title' => 'De Baja', 'value' => 0);
        return $options;
    }

    public function extend_datatable_query()
    {
        $this->db->select("packs.*, concat(pack_session_min, '/', pack_session_max) pack_sessions");
        $get = $this->input->get();
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if ($filter == 'pack_active' && $val != '') {
                    $this->db->where('pack_active', ($val == 1 ? 1 : 0));
                } elseif (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }
    }

    public function dashboard($start = false, $end = false)
    {
        $this->db->select('pack_name, count(clientpack_id) quantity')
            ->join('client_packs', 'pack_id = clientpack_package', 'left')
            ->group_by('pack_name')
            ->order_by('pack_name');

        if (!empty($start) && !empty($end)) {
            $this->db->where('clientpack_created_at >= "' . $start . '" AND clientpack_created_at <= "' . $end . '"', false, false);
        }
        return $this->db->get($this->table)->result_array();
    }
}
