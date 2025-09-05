<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Closing_model extends Base_model
{

    protected $table = 'closings';
    protected $single_for_view = 'cierres de caja';
    protected $primary_key = 'closing_id';
    protected $timestamp = false;
    protected $column_order = ['closing_created_at', 'user_firstname', 'closing_balance', 'closing_cash', 'closing_check', 'closing_transfer', 'closing_ctacte'];
    protected $column_search = ['user_firstname', 'closing_created_at'];

    public function __construct()
    {
        parent::__construct();
        if (in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario'])) {
            $this->datatable_customs_link = function ($row) {
                $buttons = '<a class="btn btn-success print-item" onclick="app.receiptOfClosing(' . $row['closing_id'] . ')"><i class="fa fa-print"></i> <span class="hidden-xs">Recibo<span></a>';
                $buttons .= $this->view_obs($row);
                $buttons .= $this->confirm_received($row);
                return $buttons;
            };
        } else {
            $this->datatable_customs_link = function ($row) {
                $buttons = '<a class="btn btn-success print-item" onclick="app.receiptOfClosing(' . $row['closing_id'] . ')"><i class="fa fa-print"></i> <span class="hidden-xs">Recibo<span></a>';
                if (empty($row['closing_received'])) {
                    $buttons = '<span class="btn btn-warning" style="cursor:default" title="No Recibido"><span class="fa fa-check-circle-o"></span></span>';
                } else {
                    $buttons = '<span class="btn bg-olive" style="cursor:default" title="Recibido"><span class="fa fa-check-circle-o"></span></span>';
                }
                return $buttons;
            };
        }
    }

    function confirm_received($row)
    {
        if (empty($row['closing_received'])) {
            return '<a href="' . site_url('closings/received/' . $row['closing_id'] . '/1') . '" class="btn bg-primary" title="Confirmar recibido"><span class="fa fa-check-circle-o"></span></a>';
        } else {
            return '<a href="javascript:;" class="btn bg-olive" style="cursor:default" title="Recibido"><span class="fa fa-check-circle-o"></span></a>';
        }
    }

    function view_obs($row)
    {
        return '<div class="hide">' . nl2br($row['closing_description']) . '</div><a onclick="$(this).prev().toggleClass(\'hide\')" class="btn bg-info" data-obs="" title="Ver Detalle billetes"><span class="fa fa-money"></span></a>';
    }

    public function closing_created_at_attribute($value, $row)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function closing_balance_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function closing_cash_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function closing_card_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function closing_check_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function closing_transfer_attribute($value, $row)
    {
        return money_formating($value);
    }

    public function save($data)
    {
        if (!empty($data[$this->primary_key])) {
            return $this->update($data);
        } else {
            $data['closing_created_by'] = $this->session->userdata('user_id');
            $data['closing_created_at'] = date('Y-m-d G:i:s');
            return $this->insert($data);
        }
    }

    public function get_filters()
    {
        $filters = array('closing_created_by', 'closing_received');
        $options = array();
        foreach ($filters as $field) {
            if ($field == 'closing_created_by') {
                $this->db->select('closing_created_by, CONCAT(user_firstname, " ",user_lastname) as username')->group_by($field)->order_by('username', 'ASC');
                $this->db->join('users', 'user_id = closing_created_by');
                $temp = $this->db->get($this->table)->result_array();
                foreach ($temp as $op) {
                    if (!empty($op[$field])) {
                        $options[$field][] = array('title' => $op['username'], 'value' => $op[$field]);
                    }
                }
            }
            if ($field == 'closing_received') {
                $options[$field][] = array('title' => 'Aprobado', 'value' => 'Aprobado');
                $options[$field][] = array('title' => 'Pendiente', 'value' => 'Pendiente');
            }
        }
        return $options;
    }

    function extend_datatable_query()
    {
        $get = $this->input->get();

        $this->db->select('closings.*, user_firstname');
        if (!empty($get['datefilter'])) {
            $dates = explode('/', $get['datefilter']);
            $start = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
            $end = date('Y-m-d 23:59:00', strtotime(trim($dates[1])));
            $this->db->where('closing_created_at >= "' . $start . '" AND closing_created_at <= "' . $end . '"');
        }

        if (!in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario'])) {
            $this->db->where('closing_created_by', $this->session->userdata('user_id'));
        }

        $this->db->join('users as u', 'user_id = closing_created_by');
    }
}
