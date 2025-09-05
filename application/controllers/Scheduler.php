<?php

class Scheduler extends ManagerController {

  protected $pathindex = '/scheduler/team';
  protected $viewpath = 'manager/scheduler/team';

  public function __construct() {
    parent::__construct();
  }
  public function index() {
    redirect($this->pathindex, 'refresh');
  }
  public function team() {
    $this->load->model('ClientPeriod_model');
    $this->load->model(['Pack_model','User_model','Client_model']);
    $this->data['activesidebar'] = 'scheduler';
    $this->data['packages'] = $this->Pack_model->get_all(true);
    $this->data['users'] = $this->User_model->get_all();
    $this->data['clients'] = $this->Client_model->get_all();
    $this->data['filters'] = $this->ClientPeriod_model->get_filters();

    $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css'];
    $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js'];
    $this->javascript[] = ['url' =>'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/locale/es.js'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
    $this->javascript[] = $this->load->view($this->viewpath . '_js', $this->data, true);
    $this->_render($this->viewpath);
  }

  public function appointments(){
    $this->load->model('ClientPeriod_model');
    $param = $this->input->post();
    $this->data['appoints'] = $this->ClientPeriod_model->simpleview($param);
    echo json_encode($this->data['appoints']);
  }

  public function filtered() {
    $this->data['filters_applied'] = $this->input->post('filter');

    $this->load->model('ClientPeriod_model');
    $this->load->model(['Pack_model','User_model','Client_model']);
    $this->data['activesidebar'] = 'scheduler';
    $this->data['packages'] = $this->Pack_model->get_all(true);
    $this->data['users'] = $this->User_model->get_all();
    $this->data['clients'] = $this->Client_model->get_all();
    $this->data['filters'] = $this->ClientPeriod_model->get_filters();
    $this->css[] = ['url' => base_url() . MAN_CSS .'fullcalendar/fullcalendar.min.css'];
    $this->css[] = ['url' => base_url() . MAN_CSS .'libs/bootstrap-datetimepicker.min.css'];
    // $this->css[] = ['url' => base_url() . MAN_CSS .'fullcalendar/fullcalendar.print.min.css']; // PRINT STYLES
    $this->javascript[] = ['url' => base_url() . MAN_JS.'libs/fullcalendar/fullcalendar.min.js'];
    $this->javascript[] = ['url' => base_url() . MAN_JS.'libs/fullcalendar/locale/es.js'];
    $this->javascript[] = ['url' => base_url() . MAN_JS.'libs/bootstrap-datetimepicker.min.js'];
    $this->javascript[] = $this->load->view($this->viewpath . '_js', $this->data, true);
    $this->_render($this->viewpath);
  }

  // public function form($_id = false) {
  //   $this->load->model('Appointment_model');
  //   $this->load->model(['Pack_model','User_model','Client_model']);
  //   $post = $this->input->post();
  //   if (!empty($post)) { //saving user received
  //     $post['appoint_start'] = date('Y-m-d G:i:s', strtotime($post['appoint_start']));
  //     $post['appoint_end'] = date('Y-m-d G:i:s', strtotime($post['appoint_end']));
  //     if($this->session->userdata('office_loaded')){
  //       $post['appoint_office'] = $this->session->userdata('office_loaded')['office_id'];
  //     }
  //     $id = $this->Appointment_model->save($post);
  //     if(is_int($id)){
  //       echo json_encode(array('status'=>'success','id'=>$id));
  //     }else{
  //       echo json_encode(array('status'=>'error'));
  //     }
  //     die();
  //   } else { //render new user form or edit user
  //     if (!empty($_id)) {
  //       $this->load->model('ClientPack_model');
  //       $this->data['edit'] = $this->Appointment_model->get_by_id($_id);
  //       $this->data['clientpack'] = $this->ClientPack_model->get_by_id($this->data['edit']['appoint_clientpack']);
  //     }
  //   }
  //   $this->data['packages'] = $this->Pack_model->get_all(true);
  //   $this->data['users'] = $this->User_model->get_all();
  //   $this->data['clients'] = $this->Client_model->get_all();
  //
  //   $viewpath = $this->viewpath;
  //   $this->data['activesidebar'] = 'scheduler';
  //   $this->data['in_modal'] = false;
  //   $this->css[] = ['url' => base_url() . MAN_CSS .'libs/bootstrap-datetimepicker.min.css'];
  //   $this->javascript[] = ['url' => base_url() . MAN_JS.'libs/fullcalendar/fullcalendar.min.js'];
  //   $this->javascript[] = ['url' => base_url() . MAN_JS.'libs/fullcalendar/locale/es.js'];
  //   $this->javascript[] = ['url' => base_url() . MAN_JS.'libs/bootstrap-datetimepicker.min.js'];
  //   $this->javascript[] = $this->load->view('manager/scheduler/event_form_js', $this->data, true);
  //
  //   $this->_render('manager/scheduler/event_form');
  // }



  // public function appoint_used($id = false){
  //   if(empty($id)){
  //     redirect($this->pathindex, 'refresh');
  //   }
  //   $this->load->model('Appointment_model');
  //   $edit_appoint = $this->Appointment_model->get_by_id($id);
  //   if(!empty($edit_appoint)){
  //     $this->load->model('ClientPack_model');
  //     $edit_appoint['appoint_used'] = 1;
  //     $this->Appointment_model->save($edit_appoint);
  //     if(!empty($edit_appoint['appoint_client'])){
  //       $edit_pack = $this->ClientPack_model->get_by_id($edit_appoint['appoint_clientpack'], true);
  //       if(empty($edit_pack['pack_fullopen'])){
  //         $edit_pack['clientpack_sessions_left']--;
  //         $this->ClientPack_model->save($edit_pack);
  //       }
  //     }
  //     redirect('/scheduler/form/'.$id, 'refresh');
  //   }else{
  //     redirect($this->pathindex, 'refresh');
  //   }
  //
  // }

  // public function remove($_id) {
  //   $this->load->model('Appointment_model');
  //   $this->checkPermission('delete_appointment');
  //   $this->Appointment_model->delete($_id);
  //   redirect($this->pathindex, 'refresh');
  // }
}
