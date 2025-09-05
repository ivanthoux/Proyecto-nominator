<?php

class Productprices extends ManagerController {

  protected $pathindex = '/productprices/all';
  protected $viewpath = 'manager/products/prices/productprice';

  public function __construct() {
    parent::__construct();
  }
  public function index() {
    redirect($this->pathindex, 'refresh');
  }
  public function all() {
    $this->load->model('ProductPrice_model');
    $viewpath = $this->viewpath.'_list';
    $this->datatables_assets();
    $this->data['activesidebar'] = 'products';
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($viewpath);
  }

  public function form($_id = false) {
    $this->load->model('ProductPrice_model');
    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      $this->ProductPrice_model->save($post);
      redirect($this->pathindex, 'refresh');
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $this->ProductPrice_model->get_by_id($_id);
      }
    }
    $this->data['activesidebar'] = 'products';
    $this->_render($this->viewpath.'_form');
  }

  public function datatables() {
    $this->load->model('ProductPrice_model');
    echo json_encode($this->ProductPrice_model->datatables_ajax_list());
  }

  public function remove($_id) {
    $this->checkPermission('delete_productprice');
    $this->load->model('ProductPrice_model');
    $this->ProductPrice_model->delete($_id);
    redirect($this->pathindex, 'refresh');
  }

}
