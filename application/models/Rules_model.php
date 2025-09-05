<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rules_model extends Base_model
{

    protected $table = 'rules';
    protected $single_for_view = 'Regla';
    protected $primary_key = 'rule_id';
    protected $timestamp = false;
    protected $order = ['rule_name', 'desc'];
    protected $column_order = ['rule_name', 'rule_type', 'rule_type_doc_require', 'rule_active'];
    protected $column_search = ['rule_name'];

    public function __construct()
    {
        parent::__construct();
        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }

    public function customLinks($row, $rol)
    {
        $edit_link = '&nbsp;';
        $remove_link = '';
        $active_link = '';
        if ($row['rule_active']) {
            $edit_link = '<a href="' . site_url('rules/form/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
            if ($rol == 'Super') {
                $link = "app.deleteConfirmAjax(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','" . site_url("rules/remove/" . $row[$this->primary_key]) . "', [], '" . site_url('rules/all') . "')";
                $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
                $link = "app.desactiveConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','rules/desactive/" . $row[$this->primary_key] . "')";
                $active_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Baja"><span class="fa fa-ban"></span></a>';
            }
        } else {
            $edit_link = '<a href="' . site_url('rules/form/' . $row[$this->primary_key]) . '" class="btn btn-info" title="Ver"><span class="fa fa-eye"></span></a>';
            if ($rol == 'Super') {
                $link = "app.deleteConfirmAjax(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','" . site_url("rules/remove/" . $row[$this->primary_key]) . "', [], '" . site_url('rules/all') . "')";
                $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
                $link = "app.activeConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','rules/active/" . $row[$this->primary_key] . "')";
                $active_link = '<a class="btn bg-olive" onclick="' . $link . '" title="Re-Activar"><span class="fa fa-check"></span></a>';
            }
        }
        return $edit_link
            . $remove_link
            . $active_link;
    }

    public function get_all($active = true)
    {
        if ($active) {
            $this->db->where('rule_active', true);
        }
        return $this->db->get($this->table)->result_array();
    }

    public function extend_datatable_query()
    {
        $this->db->select('rules.*');
        $get = $this->input->get();
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if ($filter == 'rule_active' && $val != '') {
                    $this->db->where('rule_active', ($val == 1 ? 1 : 0));
                } elseif (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }
    }

    public function get_filters()
    {
        $options['rule_active'][] = array('title' => 'Activas', 'value' => 1);
        $options['rule_active'][] = array('title' => 'De Baja', 'value' => 0);
        return $options;
    }

    public function rule_type_attribute($value, $row)
    {
        switch ($value) {
            case "1":
                return 'Valor Mínimo';
            case "2":
                return 'Valor Máximo';
            default:
                return 'SI/NO';
        }
    }

    // public function rule_type_doc_attribute($value, $row)
    // {
    //     switch ($value) {
    //         case '1':
    //             return 'D.N.I.';
    //             break;
    //         case '2':
    //             return 'Recibo de Sueldo';
    //             break;
    //         case '3':
    //             return 'Boleta de Servicio';
    //             break;
    //     }
    // }

    public function rule_type_doc_require_attribute($value, $row)
    {
        switch ($value) {
            case "0":
                return 'Nó';
            case "1":
                return 'Sí';
        }
    }

    public function rule_active_attribute($value, $row)
    {
        switch ($value) {
            case "0":
                return 'Nó';
            case "1":
                return 'Sí';
        }
    }

    public function get_by_name_type($name, $type)
    {
        return $this->db->where('rule_name', $name)
            ->where('rule_type', $type)
            ->get($this->table)->row_array();
    }
}
