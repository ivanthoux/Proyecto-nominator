<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends Base_model {

  protected $table = 'products';
  protected $single_for_view = 'producto';
  protected $primary_key = 'prod_id';
  protected $timestamp = false;
  protected $column_order = ['prod_title', 'prodprice_title','prodcat_title','prod_stock'];
  protected $column_search = ['prod_title', 'prod_description'];
  protected $products_in_proposal;

  public function __construct() {
    parent::__construct();
    $this->datatable_edit_link = function ($row) {
      return site_url('products/form/' . $row[$this->primary_key]);
    };
    $this->datatable_remove_link = function ($row) {
      return "app.deleteConfirm(" . $row[$this->primary_key] . ", '".$this->single_for_view. "','products/remove/".$row[$this->primary_key]."')";
    };
  }

  function extend_datatable_query() {
    $get = $this->input->get();
    if(!empty($get['filter'])){
      foreach($get['filter'] as $filter => $val){
        if($filter == 'prod_stock' && !empty($val)){
          $this->db->where('prod_stock >', 0);
        }elseif(!empty($val)){
          $this->db->where($filter, trim($val));
        }
      }
    }
    $this->db->join('product_prices', 'prodprice_id = prod_price', 'left');
    $this->db->join('product_categories', 'prodcat_id = prod_category','left');

    if($this->session->userdata('office_loaded')){
      $this->db->where('prod_office', $this->session->userdata('office_loaded')['office_id']);
    }
  }

  public function get_filters(){
    $filters = array('prod_stock','prod_category','prod_price');
    $options = array();
    foreach($filters as $field){
      if($field != 'prod_stock'){
        if($field == 'prod_category'){
          $this->db->select('prod_category, prodcat_title')->group_by($field)->order_by('prodcat_title','ASC');
          $this->db->join('product_categories', 'prodcat_id = prod_category','left');
          $temp = $this->db->get($this->table)->result_array();
          foreach($temp as $op){
            if(!empty($op[$field])){
              $options[$field][] = array('title' =>$op['prodcat_title'], 'value' =>$op['prod_category']);
            }
          }
        }
        if($field == 'prod_price'){
          $this->db->select('prod_price, prodprice_title')->group_by($field)->order_by('prodprice_title','ASC');
          $this->db->join('product_prices', 'prodprice_id = prod_price', 'left');
          $temp = $this->db->get($this->table)->result_array();
          foreach($temp as $op){
            if(!empty($op[$field])){
              $options[$field][] = array('title' =>$op['prodprice_title'], 'value' =>$op['prod_price']);
            }
          }
        }
      }
    }
    $options['prod_stock'][] = array('title' =>'Con Stock', 'value' =>1);
    return $options;
  }

}
