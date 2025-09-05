<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProductPrice_model extends Base_model {

  protected $table = 'product_prices';
  protected $single_for_view = 'lista de precio de producto';
  protected $primary_key = 'prodprice_id';
  protected $timestamp = false;
  protected $column_order = ['prodprice_title'];

  public function __construct() {
    parent::__construct();
    $this->datatable_edit_link = function ($row) {
      return site_url('productprices/form/' . $row[$this->primary_key]);
    };
    $this->datatable_remove_link = function ($row) {
      return "app.deleteConfirm(" . $row[$this->primary_key] . ", '".$this->single_for_view. "','productprices/remove/".$row[$this->primary_key]."')";
    };
  }

}
