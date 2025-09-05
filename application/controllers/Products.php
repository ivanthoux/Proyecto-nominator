<?php

class Products extends ManagerController {
  protected $pathindex = '/products/all';
  protected $viewpath = 'manager/products/product';

  public function __construct() {
    parent::__construct();
  }

  public function all() {
    $this->load->model('Product_model');
    $viewpath = $this->viewpath.'_list';
    $this->datatables_assets();
    $this->data['filters'] = $this->Product_model->get_filters();
    $this->data['activesidebar'] = 'products';
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($viewpath);
  }

  public function form($_id = false) {
    $this->load->model(array('Product_model','ProductCategory_model','ProductPrice_model'));
    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      $this->Product_model->save($post);
      redirect('/products/all', 'refresh');
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $this->Product_model->get_by_id($_id);
      }
    }
    $this->data['prod_categories'] = $this->ProductCategory_model->get_all();
    $this->data['prod_prices'] = $this->ProductPrice_model->get_all();
    $this->data['activesidebar'] = 'products';
    $this->javascript[] = array('url' => 'libs/bootstrap3-typeahead.min.js');
    $this->javascript[] = $this->load->view('manager/products/product_form_js', $this->data, true);
    $this->_render($this->viewpath.'_form');
  }

  public function datatables() {
    $this->load->model('Product_model');
    echo json_encode($this->Product_model->datatables_ajax_list());
  }

}
