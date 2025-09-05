<?php

class Movements extends ManagerController
{

  protected $pathindex = '/movements/all';
  protected $viewpath = 'manager/movements/movement';

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    redirect($this->pathindex, 'refresh');
  }

  public function all($from = false, $to = false)
  {
    $this->load->model('Movement_model');
    $this->load->model('Payment_model');
    $movement_model = new Movement_model();
    $payment_model = new Payment_model();

    $viewpath = $this->viewpath . '_list';
    $this->data['activesidebar'] = 'movements';

    if (empty($from) || empty($to)) {
      $start = date('Y-m-d 00:00:00');
      $end = date('Y-m-d 23:59:00');
    } else {
      $start = date('Y-m-d 00:00:00', strtotime(trim($from)));
      $end = date('Y-m-d 23:59:00', strtotime(trim($to)));
    }

    $this->data['start'] = $start;
    $this->data['end'] = $end;
    $this->data['filters'] = $movement_model->get_filters();
    $this->data['balances'] = $movement_model->balances($start, $end);
    $this->data['balances_not_closed'] = $movement_model->balance_not_closed($start, $end);
    $this->data['unpresented_check_payments'] = $payment_model->getChecksSummary(['start' => $start, 'end' => $end, 'pay_presented' => 0]);
    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function export($start = false, $end = false)
  {
    $this->load->model(['Movement_model', 'User_model']);
    $user_model = new User_model();

    $post = [];
    $get = $this->input->get();
    $get['export'] = true;
    if ($start && $end){
      $get['datefilter'] = "$start/$end";
    }
    if (!empty($get['search'])){
      $post['search'] = [
        'value'=>$get['search'],
        'regex'=>false
      ];
    }
    $_GET = $get;
    $_POST = $post;

    $datatable = $this->Movement_model->datatables_ajax_list();
    $datatable['title'] = 'Movimientos de Caja';

    if (!empty($get['datefilter'])) {
      $datatable['title'] .= ' ' . $get['datefilter'];
    }
    if (!empty($get['created_by'])) {
      $user = $user_model->get_by_id($get['created_by']);
      $datatable['title'] .= ' ' . $user['firstname'] . ' ' . $user['lastname'];
    }
    $datatable['filename'] = 'caja_movimientos';

    $this->load->view('manager/reports/caja_movimientos', $datatable);
  }

  public function datatables()
  {
    // $this->dd($this->input->post());
    $this->load->model('Movement_model');
    $datatable = $this->Movement_model->datatables_ajax_list();

    $balances = $this->Movement_model->balances();
    $datatable['income_outcome'] = money_formating($balances['income'] - $balances['outcome']);
    $datatable['income'] = money_formating($balances['income']);
    $datatable['outcome'] = money_formating($balances['outcome']);

    $balances_not_closed = $this->Movement_model->balance_not_closed();
    $datatable['income_outcome_no'] = money_formating($balances_not_closed['income'] - $balances_not_closed['outcome']);
    // $this->dd($datatable);

    echo json_encode($datatable);
  }

  public function close($from = false, $to = false)
  {
    $this->load->model(['Movement_model', 'Closing_model']);
    $closing_model = new Closing_model();
    $movement_model = new Movement_model();

    if (!empty($from) && !empty($to)) {
      $start = date('Y-m-d 00:00:00', strtotime(trim($from)));
      $end = date('Y-m-d 23:59:00', strtotime(trim($to)));
    }

    $balances = $movement_model->balance_not_closed($start, $end);
    $closing = array(
      'closing_balance' => floatval($balances['income'] - $balances['outcome']),
      'closing_cash' => floatval($balances['income_cash'] - $balances['outcome_cash']),
      // 'closing_card' => floatval($balances['income_card']),
      'closing_check' => floatval($balances['income_check']),
      'closing_transfer' => floatval($balances['income_transfer']),
      'closing_ctacte' => floatval($balances['income_cc']),
      'closing_description' => $this->input->post('obs')
    );

    $id = $closing_model->save($closing);
    $movement_model->close($start, $end, $id);

    redirect($this->pathindex, 'refresh');
  }

}
