<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProductCategory_model extends Base_model {

  protected $table = 'product_categories';
  protected $single_for_view = 'categoria de producto';
  protected $primary_key = 'prodcat_id';
  protected $timestamp = false;
  protected $column_order = ['prodcat_title'];

  public function __construct() {
    parent::__construct();
    $this->datatable_edit_link = function ($row) {
      return site_url('productcategories/form/' . $row[$this->primary_key]);
    };
    $this->datatable_remove_link = function ($row) {
      return "app.deleteConfirm(" . $row[$this->primary_key] . ", '".$this->single_for_view. "','productcategories/remove/".$row[$this->primary_key]."')";
    };
  }

}
