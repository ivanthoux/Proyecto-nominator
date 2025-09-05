<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Expense_model extends Base_model
{

    protected $table = 'expenses';
    protected $single_for_view = 'gastos';
    protected $primary_key = 'exp_id';
    protected $timestamp = false;
    protected $column_order = ['exp_date', 'exp_category', 'exp_amount', 'exp_type', 'user_firstname'];
    protected $column_search = ['exp_category', 'exp_name'];

    protected $available_types = [
        '1' => 'Efectivo',
        '2' => 'Cheque',
        '3' => 'Transferencia',
        '4' => 'Otro'
    ];
    protected $available_categories = [
        'combustible' => 'Combustible',
        'luz' => 'Luz',
        'agua' => 'Agua',
        'cable-internet' => 'Cable/Internet',
        'servicio' => 'Servicio',
        'alquiler' => 'Alquiler',
        'empleado' => 'Empleado',
        'proveedor' => 'Proveedor',
        'otro' => 'Otro'
    ];

    protected $available_receipt_types = [
        'fc-a' => 'Factura A Compras',
        'fc-c' => 'Factura C Compras',
        'nota-credito' => 'Nota de Crédito',
        'nota-debito' => 'Nota de Débito'
    ];

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('user_rol_label') == 'Super' || can("view_all_expenses")) {
            $this->datatable_edit_link = function ($row) {
                return site_url('expenses/form/' . $row[$this->primary_key]);
            };
            $this->datatable_remove_link = function ($row) {
                return "app.deleteConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','expenses/remove/" . $row[$this->primary_key] . "')";
            };
        }
    }

    public function save($data)
    {
        $data['exp_date'] = date('Y-m-d', strtotime($data['exp_date']));
        if (!empty($data[$this->primary_key])) {
            $client = $this->update($data);
        } else {
            if ($this->session->userdata('user_office')) {
                $data['exp_office'] = $this->session->userdata('user_office');
                $data['exp_officelocation'] = $this->session->userdata('user_officelocation');
            }
            $data['exp_created_by'] = $this->session->userdata('user_id');
            $data['exp_created_at'] = date('Y-m-d G:i:s');
            $client = $this->insert($data);
        }
    }

    public function get_filters()
    {
        $filters = array('exp_type', 'exp_category');
        $options = array();
        foreach ($filters as $field) {
            if ($field == 'exp_type' || $field == 'exp_category') {
                $this->db->select($field)->group_by($field)->order_by($field, 'ASC');
                $temp = $this->db->get($this->table)->result_array();
                foreach ($temp as $op) {
                    if (!empty($op[$field])) {
                        $options[$field][] = array('title' => $op[$field], 'value' => $op[$field]);
                    }
                }
            }
        }
        // echo '<pre>';
        // print_r($options);
        // die();
        return $options;
    }

    public function exp_amount_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function exp_date_attribute($value, $row)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function exp_category_attribute($value, $row)
    {
        return $row['exp_category'] . ' ' . $row['exp_name'];
    }

    function extend_datatable_query()
    {
        $get = $this->input->get();
        $this->db->select('expenses.*, u.user_firstname');
        $this->db->join('users as u', 'user_id = exp_created_by');
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }
        if ($this->session->userdata('user_rol') != 'super' && !can("view_all_expenses")) {
            $this->db->where('exp_office', $this->session->userdata('user_office'));
            if ($this->session->userdata('user_officelocation')) {
                $this->db->where('exp_officelocation', $this->session->userdata('user_officelocation'));
            }
        }
        if (!empty($get['datefilter'])) {
            $dates = explode('/', $get['datefilter']);
            $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
            $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
            $this->db->where('exp_date >= "' . $start . '" AND exp_date <= "' . $end . '"');
        }
        if ($this->session->userdata('user_rol_label') != 'Super' && !can("view_all_expenses")) {
            $this->db->where('exp_created_by', $this->session->userdata('user_id'));
            $this->db->where('exp_presented', 0);
        }
    }


    public function get_available_expense_types()
    {
        return $this->available_types;
    }
    public function get_available_expense_categories()
    {
        return $this->available_categories;
    }
    public function get_available_receipt_types()
    {
        return $this->available_receipt_types;
    }
}
