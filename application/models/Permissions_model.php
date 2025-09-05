<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  function get_all() {
    $all = $this->db->order_by("permission_group")->get('permissions')->result_array();
    return $all;
  }

  function get_all_grouped() {
    $all = $this->get_all();
    $newArray = array();

    foreach($all as $entity){
      if(!isset($newArray[$entity['permission_group']])){
        $newArray[$entity['permission_group']] = array();
      }
      $newArray[$entity['permission_group']][] = $entity;
    }
    return $newArray;
  }

  function get_by_role($id) {

    $all = $this->db->order_by("permission_group")
    ->join('role_permission', ' role_permission.permission_id = permissions.permission_id')
    ->where(array('role_permission.role_id' => $id))
    ->get('permissions')
    ->result_array();
    return $all;
  }

  function get_by_role_array($id){
    $permissions = $this->get_by_role($id);
    return $this->get_permissions_array($permissions,"permission_id");
  }

  function get_permissions_array($permissions,$key){
    $arr = [];
    foreach ($permissions as $permission){
      $arr[] = $permission[$key];
    }
    return $arr;
  }

  public function sync_role($id,$permissions){
    $this->db->delete('role_permission', array('role_id' => $id));
    $batch = [];
    if(!empty($permissions)){
      foreach ($permissions as $permission){
        $batch[]=["role_id"=>$id, "permission_id"=>$permission];
      }
      $this->db->insert_batch('role_permission', $batch);
    }    
  }

  public function sync_session($permission){
  }

}
