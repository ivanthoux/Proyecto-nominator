<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PackPoints_model extends Base_model
{

    protected $table = 'pack_points';
    protected $single_for_view = 'Puntaje de Paquete';
    protected $primary_key = 'packpoint_id';
    protected $timestamp = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function deleteAll($pack_id)
    {
        $this->db->delete($this->table, ['packpoint_pack' => $pack_id]);
    }

    public function getSessions($pack, $siisa, $veraz)
    {
        $select = 'min(packpoint_min_sessions) pack_sessions_min, ';
        $select .= ' max(packpoint_max_sessions) pack_sessions_max';
        $this->db->select($select);

        $where = ' ((packpoint_type = 1 AND ((packpoint_min_veraz <= ' . $veraz . ' AND packpoint_min_veraz > 0 AND packpoint_min_siisa <= ' . $siisa . ' AND packpoint_min_siisa > 0) AND (packpoint_aut_veraz <= ' . $veraz . ' and packpoint_aut_siisa <= ' . $siisa . ')))';
        $where .= ' OR (packpoint_type = 2 AND ((packpoint_min_veraz <= ' . $veraz . ' AND packpoint_min_veraz > 0) OR packpoint_aut_veraz <= ' . $veraz . '))';
        $where .= ' OR (packpoint_type = 3 AND ((packpoint_min_siisa <= ' . $siisa . ' AND packpoint_min_siisa > 0) OR packpoint_aut_siisa <= ' . $siisa . '))';
        $where .= ' OR (packpoint_type = 4 AND (((packpoint_min_veraz <= ' . $veraz . ' AND packpoint_min_veraz > 0) OR packpoint_aut_veraz <= ' . $veraz . ') OR ((packpoint_min_siisa <= ' . $siisa . ' AND packpoint_min_siisa > 0) OR packpoint_aut_siisa <= ' . $siisa . ')))';
        $where .= ')';
        return $this->db->where($where, null, false)
            ->where('packpoint_pack', $pack)
            ->get($this->table)->row_array();
    }
}
