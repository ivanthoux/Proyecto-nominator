<?php

class Closings extends ManagerController
{
  protected $pathindex = '/closings/all';
  protected $viewpath = 'manager/closings/closing';

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    redirect($this->pathindex, 'refresh');
  }

  public function all($id = false)
  {
    $this->load->model('Closing_model');
    $closing_model = new Closing_model();

    $viewpath = $this->viewpath . '_list';

    $this->data['filters'] = $closing_model->get_filters();
    $this->data['closing_id'] = $id;
    $this->data['activesidebar'] = 'closings';
    $this->javascript[] = ['url' => 'https://printjs-4de6.kxcdn.com/print.min.js'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function form($_id = false)
  {
    $this->load->model(array('Closing_model'));
    $post = $this->input->post();
    if (!empty($post)) { //saving user received
      $this->Closing_model->save($post);
      redirect($this->pathindex, 'refresh');
    } else { //render new user form or edit user
      if (!empty($_id)) {
        $this->data['edit'] = $this->Closing_model->get_by_id($_id);
      }
    }
    $this->data['activesidebar'] = 'closings';
    $this->css[] = array('url' => 'libs/bootstrap-colorpicker.min.css');
    $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/bootstrap-colorpicker.min.js'];
    $this->javascript[] = $this->load->view('manager/closings/closing_form_js', $this->data, true);
    $this->_render($this->viewpath . '_form');
  }

  public function datatables()
  {
    $this->load->model('Closing_model');
    echo json_encode($this->Closing_model->datatables_ajax_list());
  }

  public function remove($_id)
  {
    $this->checkPermission('delete_closing');
    $this->load->model('Closing_model');
    $this->Closing_model->delete($_id);
    redirect($this->pathindex, 'refresh');
  }

  public function received($_id, $status)
  {
    $this->checkPermission('receive_closing');
    $this->load->model('Closing_model');
    $edit = $this->Closing_model->get_by_id($_id);
    $edit['closing_received'] = intval($status);
    $this->Closing_model->save($edit);
    redirect($this->pathindex, 'refresh');
  }

  public function receipt($_id = false)
  {
    if (!$_id) {
      return;
    }

    $this->load->model([
      'User_model',
      'Closing_model',
      'Movement_model',
      'Expense_model',
      'Payment_model'
    ]);
    $closing = $this->Closing_model->get_by_id($_id);
    $user = $this->User_model->get_by_id($closing['closing_created_by']);

    $this->data['exp_types'] = $this->Expense_model->get_available_expense_types();
    $this->data['closing_date'] = date('d/m/Y', strtotime($closing['closing_created_at']));
    $this->data['closing_amount'] = money_formating($closing['closing_balance']);
    $this->data['user_name'] = $user['user_firstname'] . " " . $user['user_lastname'];
    $this->data['closing_data'] = $this->Movement_model->get_for_closing([
      'status' => 'all',
      'user_id' => $closing['closing_created_by'],
      'pay_presented' => $_id
    ]);
    $this->data['check_payments'] = $this->Payment_model->getChecksSummary(['pay_presented' => $_id]);
    $this->data['total_balance'] = $closing['closing_balance'];
    $this->data['input_cash'] = 0;
    $this->data['output_cash'] = 0;
    $this->data['transfer'] = 0;
    $this->data['cheque'] = 0;
    $this->data['ctacte'] = 0;
    foreach ($this->data['closing_data'] as $move) {
      if ($move['mov'] == 'payments' && $move['type'] == 'efectivo' && stristr($move['payment_details'], 'Efectivo')) {
        $this->data['input_cash'] += $move['amount'];
      }
      $transferencias = [
        'Transferencia',
        'BANCO CORRIENTES - PAGOS.LINEA.nominator',
        'BANCO NACION - PAGOS.LINEA.NACION',
        'MERCADO PAGO - PAGOS.LINEA.MP',
        'BANCO MACRO - PAGOS.nominator.LINEA'
      ];
      if (in_array($move['type'], $transferencias)) {
        $this->data['transfer'] += $move['amount'];
      }
      if (stristr($move['type'], 'Cheque')) {
        $this->data['cheque'] += $move['amount'];
      }
      if (stristr($move['type'], 'Cuenta Corriente')) {
        $this->data['ctacte']++;
      }

      if ($move['mov'] == 'expenses' && $move['type'] == 1) {
        $this->data['output_cash'] += $move['amount'];
      }
    }
    $this->data['input_cash'] = $this->data['input_cash'] - $this->data['output_cash'];
    $post = $this->input->post();
    if (isset($post['ajax']) && $post['ajax'] == true) {
      echo json_encode(['status' => 'ok', 'html' => $this->load->view('manager/closings/closing_receipt', $this->data, true)]);
      return;
    }
    $this->_render('manager/closings/closing_receipt');
  }
}
