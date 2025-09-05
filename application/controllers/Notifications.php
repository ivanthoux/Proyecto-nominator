<?php

class Notifications extends ManagerController
{

  protected $pathindex = '/notifications/all';
  protected $viewpath = 'manager/notifications/notification';

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
    $this->load->model(['Notifications_model']);
    $notifications_model = new Notifications_model();

    $viewpath = $this->viewpath . '_list';

    $this->data['filters'] = $notifications_model->get_filters();
    $this->data['activesidebar'] = 'notifications';

    $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

    $this->datatables_assets();
    $this->_render($viewpath);
  }

  public function form($_id = false)
  {
    $this->load->model(['Notifications_model', 'NotificationDetails_model']);
    if (empty($_id)) {
      redirect('/notifications/all', 'refresh');
    }
    $notificationId = $_id;
    $notifications_model = new Notifications_model();
    $notificationDetails_model = new NotificationDetails_model();

    $viewpath = $this->viewpath . '_form';

    $this->data['activesidebar'] = 'notifications';

    $notification = $notifications_model->get_by_id($notificationId);
    $this->data['notification'] = $notification;
    if (empty($notification)) {
      redirect('/notifications/all', 'refresh');
    }
    $this->data['notificationDetails'] = $notificationDetails_model->getByNotification($notificationId);

    $this->datatables_assets();
    $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
    $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/jquery.mask.min.js'];

    $this->_render($this->viewpath . '_form');
  }

  public function datatables()
  {
    $this->load->model('Notifications_model');
    $notification_model = new Notifications_model();
    echo json_encode($notification_model->datatables_ajax_list());
  }
}
