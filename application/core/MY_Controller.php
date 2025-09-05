<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{

  //Page info
  protected $data = array();
  protected $pageName = false;
  protected $template = "default";
  protected $hasNav = true;
  //Page contents
  //use this array to include special js plugins, only for "some" pages -
  //when should be incluede more general plugins, for more pages use the plugins.js file on assets
  protected $javascript = array();
  //use this array to include special css plugins, only for "some" pages -
  //when should be incluede more general plugins, for more pages use the plugins.css file on assets
  protected $css = array();
  protected $fonts = array();
  //Page Meta
  protected $title = false;
  protected $description = false;
  protected $keywords = false;
  protected $author = false;

  public function __construct()
  {
    parent::__construct();

    $this->load->model(['Settings_model']);
    $settin_model = new Settings_model();

    $setting = $settin_model->get_all();
    if (isset($setting[0]['setting_status'])) {
      $status = json_decode($setting[0]['setting_status']);
      if ($status->code !== 200)
        $this->dd($status->message);
    }

    $this->data["uri_segment_1"] = $this->uri->segment(1);
    $this->data["uri_segment_2"] = $this->uri->segment(2);
    $this->title = $this->config->item('site_title');
    $this->description = $this->config->item('site_description');
    $this->keywords = $this->config->item('site_keywords');
    $this->author = $this->config->item('site_author');
    $this->pageName = strtolower(get_class($this));

    //language loading
    $site_lang = $this->session->userdata('site_lang');
    if (empty($site_lang)) {
      $site_lang = 'spanish';
    }
    $this->load->helper('language');
    $this->lang->load('common', $site_lang);
    $this->load->model('User_model');
    $this->User_model->check_cookie();
    $this->User_model->load_session();
    date_default_timezone_set('America/Argentina/Buenos_Aires');
  }

  protected function _render($view, $renderData = "FULLPAGE")
  {
    switch ($renderData) {
      case "AJAX":
        $this->load->view($view, $this->data);
        break;
      case "JSON":
        echo json_encode($this->data);
        break;
      case "PRINT":
        //static
        $toTpl["javascript"] = $this->javascript;
        $toTpl["css"] = $this->css;
        $toTpl["fonts"] = $this->fonts;

        //meta
        $toTpl["title"] = $this->title;
        $toTpl["description"] = $this->description;
        $toTpl["keywords"] = $this->keywords;
        $toTpl["author"] = $this->author;

        //body class
        $toTpl["bodyclass"] = 'sidebar-collapse fixed skin-blue';

        $toTpl = array_merge($this->data, $toTpl);
        //data
        $toBody["content_body"] = $this->load->view($view, $toTpl, true);

        $toBody["sidebar"] = '';
        $toBody["header"] = '';
        $toBody["footer"] = '';

        //render view
        $this->load->view('templates/' . $this->template . "/skeleton", array_merge($toBody, $toTpl));
        break;
      default:
        //static
        $toTpl["javascript"] = $this->javascript;
        $toTpl["css"] = $this->css;
        $toTpl["fonts"] = $this->fonts;

        //meta
        $toTpl["title"] = $this->title;
        $toTpl["description"] = $this->description;
        $toTpl["keywords"] = $this->keywords;
        $toTpl["author"] = $this->author;

        //body class
        $toTpl["bodyclass"] = 'skin-green-light sidebar-mini ' . (($this->session->userdata('user_rol') != 'super') ? 'sidebar-collapse' : '');

        $toTpl = array_merge($this->data, $toTpl);
        //data
        $toBody["content_body"] = $this->load->view($view, $toTpl, true);

        $toBody["header"] = $this->load->view('templates/' . $this->template . "/header", $toTpl, true);

        if ($this->template == 'manager') {
          $toBody["sidebar"] = $this->load->view('templates/' . $this->template . "/sidebar", $toTpl, true);
        }
        $toBody["footer"] = $this->load->view('templates/' . $this->template . "/footer", $toTpl, true);

        //render view
        $this->load->view('templates/' . $this->template . "/skeleton", array_merge($toBody, $toTpl));
        break;
    }
  }

  public function email_employer_no_exist()
  {
    $useremail = strtolower($this->input->post('user_email'));

    $user = $this->User_model->get_by_email($useremail);

    if (empty($user)) {
      return true;
    } else {
      $this->form_validation->set_message('email_employer_no_exist', 'El email ya esta registrado');
      return false;
    }
  }

  public function dni_user_no_exist()
  {
    $dni = strtolower($this->input->post('employee_dni'));
    $this->load->model('Employees_model');
    $user = $this->Employees_model->get_where(["employee_dni" => $dni]);

    if (empty($user)) {
      return true;
    } else {
      $this->form_validation->set_message('dni_user_no_exist', 'El DNI ya está registrado');
      return false;
    }
  }

  public function email_employee_no_exist()
  {
    return $this->email_employer_no_exist();
  }

  public function email_no_exist()
  {
    $useremail = strtolower($this->input->post('email'));

    $user = $this->User_model->get_by_email($useremail);
    if (empty($user)) {
      return true;
    } else {
      $this->form_validation->set_message('email_no_exist', 'El email ya esta registrado');
      return false;
    }
  }

  public function datatables_assets()
  {
    $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.min.css'];
    $this->css[] = ['url' => 'https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js'];
    $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap4.min.js'];
    $this->javascript[] = ['url' => 'https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js'];
  }

  public function checkPermission($permission)
  {
    if (!can($permission)) {
      redirect('manager');
      die();
    }
  }

  public function logCreate($model, $object, $acction)
  {
    $this->load->model('UserActions_model');
    $data['useraction_user'] = $this->session->userdata('user_id');
    $data['useraction_date'] = date('Y-m-d G:i:s');
    $data['useraction_action'] = $acction;
    $data['useraction_detail'] = json_encode($object);
    $data['useraction_entity'] = $model->table();
    $data['useraction_entity_id'] = $object[$model->primary_key()];
    if (!isset($object[$model->primary_key()]) || $object[$model->primary_key()] === null || $object[$model->primary_key()] === '') {
      die('El id del objeto no puede ser vacio');
    }
    $this->UserActions_model->save($data);
  }

  public function getLastLog($model, $id, $acction = false)
  {
    $this->load->model('UserActions_model');
    return $this->UserActions_model->getByEntityId($model->table(), $id, $acction);
  }

  public function dd($object, $continue = false)
  {
    echo '<pre>' . print_r($object, true) . '</pre>';
    if (!$continue)
      die();
  }
}

class ManagerController extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();

    if (!$this->session->userdata('user_logged')) {
      redirect('/user/login', 'refresh');
    }
    $this->template = 'manager';
    $this->load->model(array('Settings_model'));
    $settings = $this->Settings_model->get_all();
    $this->data['settings'] = json_decode((count($settings) == 0 ? '[]' : $settings[0]['setting_data']), true);
    $this->session->set_userdata(['settings' => $this->data['settings']]);
  }
}
