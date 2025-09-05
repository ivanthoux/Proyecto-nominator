<?php

class Expenses extends ManagerController
{

  protected $pathindex = '/expenses/all';
  protected $viewpath = 'manager/expenses/expense';

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    redirect($this->pathindex, 'refresh');
  }

  public function all()
  {
    $this->load->model(['Expense_model']);
    $expense_model = new Expense_model();

    $viewpath = $this->viewpath . '_list';
    $this->data['activesidebar'] = 'expenses';

    $this->datatables_assets();

    $this->data['filters'] = $expense_model->get_filters();
    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($viewpath);
  }

  public function form($_id = false)
  {
    $this->load->model('Expense_model');
    $this->load->model('Client_model');
    $this->viewpath = 'manager/expenses/expense';
    $viewpath = $this->viewpath . '_form';
    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      $this->Expense_model->save($post);
      redirect('/expenses/all/', 'refresh');
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $this->Expense_model->get_by_id($_id);
      }
    }
    $this->data['activesidebar'] = 'expenses';
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->_render($this->viewpath . '_form');
  }

  public function datatables()
  {
    $this->load->model('Expense_model');
    echo json_encode($this->Expense_model->datatables_ajax_list());
  }

  public function remove($_id)
  {
    $this->load->model('Expense_model');
    $remove = $this->Expense_model->get_by_id($_id);
    $this->Expense_model->delete($_id);
    redirect($this->pathindex, 'refresh');
  }
}
