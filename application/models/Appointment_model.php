<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_model extends Base_model {

  protected $table = 'appointments';
  protected $single_for_view = 'Turno';
  protected $primary_key = 'appoint_id';
  protected $timestamp = false;
  protected $column_order = ['appoint_client','appoint_resource', 'appoint_title', 'appoint_date'];
  protected $order = ['appoint_date','asc'];

  public function __construct() {
    parent::__construct();

  }

  public function get_filters(){
    $filters = array('appoint_client','appoint_resource','appoint_clientpack'); //appoint_agendacolor
    $options = array();
    foreach($filters as $field){
      if($field == 'appoint_client'){
        $this->db->select('appoint_client, c.client_firstname, c.client_lastname')->group_by($field)->order_by('c.client_firstname','ASC');
        $this->db->join('clients as c', 'client_id = appoint_client');
        $temp = $this->db->get($this->table)->result_array();
        foreach($temp as $op){
          if(!empty($op[$field])){
            $options[$field][] = array('title' =>$op['client_firstname'].' '.$op['client_lastname'], 'value' =>$op[$field]);
          }
        }
      }else if($field == 'appoint_resource'){
        $this->db->select('appoint_resource, user_firstname')->group_by($field)->order_by('user_firstname','ASC');
        $this->db->join('users as u', 'user_id = appoint_resource');
        $temp = $this->db->get($this->table)->result_array();
        foreach($temp as $op){
          if(!empty($op[$field])){
            $options[$field][] = array('title' =>$op['user_firstname'], 'value' =>$op[$field]);
          }
        }
      }else if($field == 'appoint_clientpack'){
        $this->db->select('pack_id, pack_name')->group_by('pack_name')->order_by('pack_name','ASC');
        $this->db->join('client_packs', 'appoint_clientpack = clientpack_id');
        $this->db->join('packs', 'clientpack_package = pack_id', 'left');
        $this->db->where('appoint_client>',0);
        $temp = $this->db->get($this->table)->result_array();

        foreach($temp as $op){
          if(!empty($op['pack_id'])){
            $options[$field][] = array('title' =>$op['pack_name'], 'value' =>$op['pack_name']);
          }
        }
      // }else if($field == 'appoint_agendacolor'){
      //   $this->db->select('pack_agendacolor')->group_by('pack_agendacolor')->order_by('pack_agendacolor','ASC');
      //   $this->db->join('client_packs', 'appoint_clientpack = clientpack_id');
      //   $this->db->join('packs', 'clientpack_package = pack_id', 'left');
      //   $this->db->where('appoint_client>',0);
      //   $temp = $this->db->get($this->table)->result_array();
      //
      //   foreach($temp as $op){
      //     if(!empty($op['pack_agendacolor'])){
      //       $options[$field][] = array('title' =>$op['pack_agendacolor'], 'value' =>$op['pack_agendacolor']);
      //     }
      //   }
      }
    }
    // echo '<pre>';
    // print_r($options);
    // die();
    return $options;
  }

  public function simpleview($params){
    $params['start'] = date('Y-m-d G:i:s', strtotime($params['start']));
    $params['end'] = date('Y-m-d G:i:s', strtotime($params['end']));
    $this->db->select("appoint_id, appoint_used, appoint_client");
    $this->db->select("IF(client_id IS NOT NULL AND pack_agendacolor IS NOT NULL, pack_agendacolor, '#ee2d65') as backgroundColor");
    $this->db->select("appoint_description as description");
    $this->db->select("IF( client_id IS NULL, CONCAT(CONCAT(appoint_description,' '),(SELECT pack_name FROM packs WHERE pack_id = appoint_clientpack)), CONCAT(CONCAT(CONCAT(CONCAT(client_firstname,' '),LEFT(client_lastname,1)),'. '),COALESCE(clientpack_title,''))) as title");
    $this->db->select("DATE_FORMAT(appoint_start,'%Y-%m-%dT%TZ') as start, DATE_FORMAT(appoint_end,'%Y-%m-%dT%TZ') as end");
    $this->db->join('clients', 'appoint_client = client_id','left');
    $this->db->join('client_packs', 'appoint_clientpack = clientpack_id','left');
    $this->db->join('packs', 'clientpack_package = pack_id','left');
    $this->db->where('appoint_start >= "'.$params['start'].'" AND appoint_start <= "'.$params['end'].'"');

    if(!empty($params['filter'])){
      foreach($params['filter'] as $filter => $val){
        if(!empty($val)){
          if($filter == 'appoint_clientpack'){
            $this->db->where('pack_name', trim($val));
          // }elseif($filter == 'appoint_agendacolor'){
          //   $this->db->where('pack_agendacolor', trim($val));
          }else{
            $this->db->where($filter, trim($val));
          }
        }
      }
    }

    if($this->session->userdata('office_loaded')){
      $this->db->where('appoint_office', $this->session->userdata('office_loaded')['office_id']);
    }
    $response = $this->db->get($this->table)->result_array();

    foreach($response as $event=>$val){
      // $response[$event]['editable'] = $val['appoint_used'] ? false : true;
      $response[$event]['editable'] = false;
      $response[$event]['borderColor'] = 'grey';
      $response[$event]['backgroundColor'] = $val['appoint_used'] ? 'grey' : $response[$event]['backgroundColor'];
    }
    return $response;
  }

  function dashboard($start, $end){
    $this->db->select('count(*) as qty');
    $this->db->select("CONCAT(CONCAT(user_firstname,' '),user_lastname) as user_name");
    $this->db->join('client_packs', 'appoint_clientpack = clientpack_id');
    $this->db->join('users', 'appoint_resource = user_id');
    $this->db->where('appoint_used', 1);
    $this->db->where('appoint_start >= "'.$start.'" AND appoint_start <= "'.$end.'"',false,false);
    $this->db->group_by('appoint_resource')->order_by('qty','DESC');

    return $this->db->get('appointments')->result_array();
  }

}
