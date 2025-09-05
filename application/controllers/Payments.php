<?php

class Payments extends ManagerController {

    protected $pathindex = '/payments/all';
    protected $viewpath = 'manager/payments/payment';

    public function __construct() {
        parent::__construct();
    }

    public function index($client = false) {
        redirect($this->pathindex, 'refresh');
    }

    public function all() {
        $this->load->model(['Payment_model']);
        $payment_model = new Payment_model();

        $viewpath = $this->viewpath . '_list';
        $this->data['activesidebar'] = 'payments';
        
        $this->data['filters'] = $payment_model->get_filters();
        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        
        $this->datatables_assets();
        $this->_render($viewpath);
    }

    public function form($_id = false) {
        $this->load->model('Payment_model');
        $this->load->model('Client_model');
        $this->viewpath = 'manager/payments/payment';
        $viewpath = $this->viewpath . '_form';
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            // $this->dd($post);
            $this->Payment_model->save($post);
            if (!empty($client)) {
                $this->Client_model->less_balance($this->data['client'], $post['pay_amount']);
            }
            redirect('/payments/all/', 'refresh');
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $this->Payment_model->get_by_id($_id);
            }
        }
        $this->data['activesidebar'] = 'payments';
        $this->data['setting'] = $this->session->userdata('settings');
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
        $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
        $this->_render($this->viewpath . '_form');
    }

    public function datatables() {
        $this->load->model('Payment_model');
        echo json_encode($this->Payment_model->datatables_ajax_list());
    }

    public function remove($_id) {
        $this->load->model('Payment_model');
        $this->load->model('Client_model');
        $remove = $this->Payment_model->get_by_id($_id);
        $this->Payment_model->delete($_id);
        if (!empty($remove['pay_client'])) {
            redirect('/payments/all/', 'refresh');
        }
        redirect($this->pathindex, 'refresh');
    }

}
