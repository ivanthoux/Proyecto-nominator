<?php

class Productcategories extends ManagerController {

  protected $pathindex = '/productcategories/all';
  protected $viewpath = 'manager/products/categories/productcategory';

  public function __construct() {
    parent::__construct();
  }
  public function index() {
    redirect($this->pathindex, 'refresh');
  }
  public function all() {
    $this->load->model('ProductCategory_model');
    $viewpath = $this->viewpath.'_list';
    $this->datatables_assets();
    $this->data['activesidebar'] = 'products';
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($viewpath);
  }

  public function form($_id = false) {
    $this->load->model('ProductCategory_model');
    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      $this->ProductCategory_model->save($post);
      redirect($this->pathindex, 'refresh');
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $this->ProductCategory_model->get_by_id($_id);
      }
    }
    $this->data['activesidebar'] = 'products';
    $this->_render($this->viewpath.'_form');
  }

  public function datatables() {
    $this->load->model('ProductCategory_model');
    echo json_encode($this->ProductCategory_model->datatables_ajax_list());
  }

  public function remove($_id) {
    $this->checkPermission('delete_productcategory');
    $this->load->model('ProductCategory_model');
    $this->ProductCategory_model->delete($_id);
    redirect($this->pathindex, 'refresh');
  }

}
