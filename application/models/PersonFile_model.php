<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PersonFile_model extends Base_model
{

    protected $table = 'person_files';
    protected $single_for_view = 'documento de persona';
    protected $primary_key = 'personfile_id';
    protected $timestamp = false;
    protected $order = ['rule_name', 'asc'];
    protected $column_order = ['personfile_type', 'personfile_file_type', 'personfile_date'];
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
        if ($rol == 'Super') {
            $edit_link = '<a href="' . site_url('personfiles/form/' . $row['personfile_person'] . '/' . $row[$this->primary_key]) . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
            $link = "app.deleteConfirm(" . $row[$this->primary_key] . ", '" . $this->single_for_view . "','personfiles/remove/" . $row['personfile_person'] . '/' . $row[$this->primary_key] . "')";
            $remove_link = '<a class="btn btn-danger" onclick="' . $link . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
        }
        return '<a href="' . site_url('resources/persons/' . $row['personfile_file']) . '" class="btn bg-info" title="Visualizar" target="_blanck"><span class="fa fa-search"></span></a>'
            . $edit_link
            . $remove_link;
    }

    public function personfile_file_type_attribute($value, $row)
    {
        $translatedTypes = [
            'personal file' => 'Legajo personal',
            'capacitations' => 'Capacitaciones',
            'incidents' => 'Incidencias',
        ];
        return $translatedTypes[$value] ?? $value;
    }

    public function rule_name_attribute($value, $row)
    {
        return (empty($value) ? '[ERROR] CAMBIAR TIPO DOCUMENTO' : $value);
    }

    public function personfile_date_attribute($value, $row)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function extend_datatable_query()
    {
        $get = $this->input->get();
        $this->db->select('person_files.*,')
            ->join('persons', 'personfile_person = person_id');
        if (!empty($get['person'])) {
            $this->db->where('person_id', $get['person']);
        }
    }

    public function get_files_by_person($person_id)
    {
        return $this->db->select('personfile_file, personfile_type')
            ->where('personfile_person', $person_id)
            ->get($this->table)->result_array();
    }
}
