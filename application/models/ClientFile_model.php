<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ClientFile_model extends Base_model
{

    protected $table = 'client_files';
    protected $single_for_view = 'documento de cliente';
    protected $primary_key = 'clientfile_id';
    protected $timestamp = false;
    protected $order = ['rule_name', 'asc'];
    protected $column_order = ['rule_name', 'clientfile_date'];
    protected $column_search = [];

    public function __construct()
    {
        $this->datatable_customs_link = function ($row) {
            return $this->view_packages($row, $this->session->userdata('user_rol_label'));
        };
    }

    public function view_packages($row, $rol)
    {
        $edit_link = '';
        $remove_link = '';
        if ($row['client_active']) {
            if ($rol == 'Super') {
                $edit_link = '<a href="' . site_url('clientfiles/form/' . $row['clientfile_client'] . '/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
                $link = "app.deleteConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','clientfiles/remove/" . $row['clientfile_client'] . '/' . $row[$this->primary_key] . "')";
                $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
            }
        }
        return '<a href="' . site_url('resources/clients/' . $row['clientfile_file']) . '" class="btn bg-info" title="Visualizar" target="_blanck"><span class="fa fa-search"></span></a>'
            . $edit_link
            . $remove_link;
    }

    public function rule_name_attribute($value, $row)
    {
        return (empty($value) ? '[ERROR] CAMBIAR TIPO DOCUMENTO' : $value);
    }

    // public function clientfile_type_attribute($value, $row)
    // {
    //     switch ($value) {
    //         case 1:
    //             return 'D.N.I. Frente';
    //         case 2:
    //             return 'D.N.I. Dorso';
    //         case 3:
    //             return 'Recibo de Sueldo';
    //         case 4:
    //             return 'Boleta de Servicio';
    //     }
    // }

    public function clientfile_date_attribute($value, $row)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function extend_datatable_query()
    {
        $get = $this->input->get();
        $this->db->select('client_files.*, rule_name, client_active')
            ->join('rules', 'rule_id = clientfile_type', 'left')
            ->join('clients', 'clientfile_client = client_id');
        if (!empty($get['client'])) {
            $this->db->where('client_id', $get['client']);
        }
    }

    public function get_files_by_client($client_id)
    {
        return $this->db->select('clientfile_file, clientfile_type')
            ->where('clientfile_client', $client_id)
            ->get($this->table)->result_array();
    }
}
