<?php

class Manager extends ManagerController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect($this->session->userdata('role_default_url'), 'refresh');
    }

    public function keep_alive()
    {
        $return['status'] = 'error';
        if ($this->session->userdata('admin_logged')) {
            $return['status'] = 'alive';
        }
        echo json_encode($return);
    }

    public function user($user_id = false)
    {
        $this->load->model('Roles_model');
        $this->data['roles'] = $this->Roles_model->get_all();
        $this->data['is_current_user'] = $this->session->userdata('user_id') == $user_id;
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            // $this->dd($post);
            $this->load->library('form_validation');
            $_readedpost = $this->User_model->_read_manager_post();
            $this->form_validation->set_rules($this->User_model->manager_validation());

            if ($this->form_validation->run() == true) {
                $_id = $this->User_model->save($_readedpost);
                if (!empty($_readedpost['user_id'])) {
                    $this->logCreate($this->User_model, $_readedpost, 'u');
                } else {
                    $_readedpost['user_id'] = $_id;
                    $this->logCreate($this->User_model, $_readedpost, 'c');
                }
                redirect('/manager/users', 'refresh');
            } else {
                $this->data['edit'] = $_readedpost;
                $this->data['errors'] = validation_errors();
            }
        } else { //render new user form or edit user
            if (!empty($user_id)) {
                if ($user_id != $this->session->userdata('user_id')) {
                    $this->checkPermission('edit_user');
                    $this->data['activesidebar'] = 'users';
                } else {
                    $this->data['activesidebar'] = 'account';
                }
                $this->data['edit'] = $this->User_model->get($user_id);
            } else {
                $this->checkPermission('create_user');
                $this->data['activesidebar'] = 'users';
            }
        }
        $this->javascript[] = $this->load->view('manager/user/user_form_js', $this->data, true);
        $this->_render('manager/user/user_form');
    }

    public function holiday_remove($holiday_id)
    {
        $this->load->model('Holiday_model');
        $this->Holiday_model->delete($holiday_id);
        redirect('/manager/holidays', 'refresh');
    }

    public function user_remove($user_id)
    {
        $this->load->model('User_model');
        $this->logCreate($this->User_model, $this->User_model->get_by_id($user_id), 'd');
        $this->User_model->del($user_id);
        redirect('/manager/users', 'refresh');
    }

    public function user_active($user_id)
    {
        $this->load->model('User_model');
        $this->User_model->active($user_id);
        $this->logCreate($this->User_model, $this->User_model->get_by_id($user_id), 'u');
        redirect('/manager/users', 'refresh');
    }

    public function users()
    {
        $this->load->model('User_model');

        if ($this->input->get('dt')) {
            $count = $this->User_model->count_all();
            $this->db->where('user_password <>', 'SYSTEM');
            $list = $this->User_model->get_all();

            $this->load->helper('datatables_helper');
            $final_list = user_datatable($list);

            echo json_encode([
                'draw' => $this->input->post('draw', true),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $final_list,
            ]);
            die();
        } else {
            $this->data['filters'] = $this->User_model->get_filters();
            $this->data['users_total'] = $this->User_model->count_all();

            $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.min.css'];
            $this->css[] = ['url' => 'https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css'];
            $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js'];
            $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap4.min.js'];
            $this->javascript[] = ['url' => 'https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js'];

            $this->javascript[] = $this->load->view('manager/user/user_list_js', $this->data, true);
            $this->data['activesidebar'] = 'users';
            $this->_render('manager/user/user_list');
        }
    }

    public function additionals()
    {
        $this->load->model('Additionals_model');
        $view_path = 'manager/additionals/additional_list';
        $this->datatables_assets();
        $this->javascript[] = $this->load->view($view_path . '_js', $this->data, true);
        $this->data['activesidebar'] = 'additionals';
        $this->_render($view_path);
    }

    public function holidays()
    {
        $this->load->model('Holiday_model');
        $view_path = 'manager/holidays/holiday_list';
        $this->datatables_assets();
        $this->javascript[] = $this->load->view($view_path . '_js', $this->data, true);
        $this->data['activesidebar'] = 'holidays';
        $this->_render($view_path);
    }

    public function roles()
    {
        $this->load->model('Roles_model');
        $view_path = 'manager/roles/role_list';
        $this->datatables_assets();
        $this->javascript[] = $this->load->view($view_path . '_js', $this->data, true);
        $this->data['activesidebar'] = 'roles';
        $this->_render($view_path);
    }

    public function additionals_datatables()
    {
        $this->load->model('Additionals_model');
        echo json_encode($this->Additionals_model->datatables_ajax_list());
    }
    
    public function holidays_datatables()
    {
        $this->load->model('Holiday_model');
        echo json_encode($this->Holiday_model->datatables_ajax_list());
    }

    public function roles_datatables()
    {
        $this->load->model('Roles_model');
        echo json_encode($this->Roles_model->datatables_ajax_list());
    }

    public function role_remove($_id)
    {
        $this->checkPermission('delete_role');
        $this->load->model('Roles_model');
        $this->Roles_model->del($_id);
        redirect('/manager/roles', 'refresh');
    }

    public function role($role_id = false)
    {
        $this->load->model('Roles_model');
        $this->load->model('Permissions_model');
        $this->data['permissions'] = $this->Permissions_model->get_all_grouped();
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            $this->load->library('form_validation');
            $_readedpost = $this->input->post();
            $permissions = !empty($_readedpost['permissions']) ? $_readedpost['permissions'] : array();
            unset($_readedpost['permissions']);
            $this->form_validation->set_rules($this->Roles_model->manager_validation());

            if ($this->form_validation->run() == TRUE) {
                $id = $this->Roles_model->save($_readedpost);
                $this->Permissions_model->sync_role($id, $permissions);
                redirect('/manager/roles', 'refresh');
            } else {
                $this->data['edit'] = $_readedpost;
                $this->data['errors'] = validation_errors();
            }
        } else { //render new user form or edit user
            if (!empty($role_id)) {
                $this->data['edit'] = $this->Roles_model->get_by_id($role_id);
                $this->data['permissions_role'] = $this->Permissions_model->get_by_role_array($role_id);
            }
        }
        $this->data['activesidebar'] = 'role';
        //        $this->javascript[] = $this->load->view('manager/roles/role_form_js', $this->data, true);
        $this->_render('manager/roles/role_form');
    }

    public function additional($additionalId = false)
    {
        $viewpath = "manager/additionals/additional_form";
        $this->load->model('Additionals_model');
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            $post = $this->input->post();
            $validated = true;
            if ($validated) {
                $id = $this->Additionals_model->save($post);
                redirect('/manager/additionals', 'refresh');
            } else {
                $this->data['edit'] = $post;
                $this->data['errors'] = validation_errors();
            }
        } else { //render new user form or edit user
            if (!empty($additionalId)) {
                $this->data['edit'] = $this->Additionals_model->get_by_id($additionalId);
            }
        }
        $this->data['activesidebar'] = 'holiday';
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
        $this->_render($viewpath);
    }
    public function holiday($holidayId = false)
    {
        $viewpath = "manager/holidays/holiday_form";
        $this->load->model('Holiday_model');
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            $post = $this->input->post();
            $holidayDateObject = DateTime::createFromFormat('d-m-Y', $post['holiday_date']);
            $post['holiday_date'] = $holidayDateObject->format('Y-m-d');
            $validated = true;
            if ($validated) {
                $id = $this->Holiday_model->save($post);
                redirect('/manager/holidays', 'refresh');
            } else {
                $this->data['edit'] = $post;
                $this->data['errors'] = validation_errors();
            }
        } else { //render new user form or edit user
            if (!empty($holidayId)) {
                $this->data['edit'] = $this->Holiday_model->get_by_id($holidayId);
            }
        }
        $this->data['activesidebar'] = 'holiday';
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
        $this->_render('manager/holidays/holiday_form');
    }

    public function settings()
    {
        $this->load->model('Settings_model');
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            // $this->dd($post);
            $edit = $this->Settings_model->get_all();
            $this->settings_save($post);
        } else { //render new user form or edit user
            $edit = $this->Settings_model->get_all();
            $this->data['edit'] = empty($edit) ? ['setting_id' => '', 'setting_data' => '[]'] : $edit[0];
            $this->data['edit']['setting_data'] = json_decode($this->data['edit']['setting_data'], true);
        }
        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap3-wysihtml5.min.css'];
        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/fileinput.min.css'];
        $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/bootstrap3-wysihtml5.all.min.js'];
        $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/fileinput_bootstrap.min.js'];
        $this->javascript[] = $this->load->view('manager/settings/setting_form_js', $this->data, true);
        $this->data['activesidebar'] = 'settings';
        $this->_render('manager/settings/setting_form');
    }

    public function settings_save($post)
    {
        $this->load->model('Settings_model');
        $post = $this->input->post('setting_data');

        $settings = $this->Settings_model->get_all();
        $data = count($settings) == 0 ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
        $data['setting_data'] = json_decode($data['setting_data'], true);
        $data['setting_data'] = array_merge((array) $data['setting_data'], $post);
        $data['setting_data'] = json_encode($data['setting_data']);
        $this->_save($data);
        redirect('/manager/settings', 'refresh');
    }

    private function _save($data)
    {
        if ($data['setting_id'] == '') {
            $data['setting_id'] = $this->Settings_model->save($data);
            $this->logCreate($this->Settings_model, $data, 'c');
        } else {
            $this->Settings_model->save($data);
            $this->logCreate($this->Settings_model, $data, 'u');
        }
    }

    public function image_delete($type = false, $id = false, $pageid = false)
    {
        if ($type == false) {
            log_message('error', 'Upload error - type not found');
            echo json_encode(array('message' => 'Upload error - type not found'));
            die();
        }

        if ($type == 'logo') {
            unlink(dirname(dirname(dirname(__FILE__))) . '/resources/' . $this->data['settings']['logo']);
            $settings = $this->Settings_model->get_all();
            $data = empty($settings) ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
            $setting_data = json_decode($data['setting_data'], true);
            $setting_data['logo'] = '';
            $data['setting_data'] = json_encode($setting_data);
            $this->_save($data);
        } else if ($type == 'bg') {
            unlink(dirname(dirname(dirname(__FILE__))) . '/resources/' . $this->data['settings']['bg']);
            $settings = $this->Settings_model->get_all();
            $data = empty($settings) ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
            $setting_data = json_decode($data['setting_data'], true);
            $setting_data['bg'] = '';
            $data['setting_data'] = json_encode($setting_data);
            $this->_save($data);
        } else {
            unlink(dirname(dirname(dirname(__FILE__))) . '/resources/' . $this->data['settings'][$type]);
            $settings = $this->Settings_model->get_all();
            $data = empty($settings) ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
            $setting_data = json_decode($data['setting_data'], true);
            $setting_data[$type] = '';
            $data['setting_data'] = json_encode($setting_data);
            $this->_save($data);
        }

        echo json_encode(array());
    }

    public function image_upload($type = false, $id = false)
    {
        if ($type == false) {
            log_message('error', 'Upload error - type not found');
            die(json_encode(array('message' => 'Upload error - type not found')));
        }

        $upload = array_shift($_FILES);
        if (!empty($upload)) {
            $this->load->helper('upload_helper');
            $upload = upload_worker($upload, RESOURCES_FOLDER, $type);

            if (!empty($upload['error'])) {
                log_message('error', 'API - ' . $upload['error']);
                die(json_encode(array('message' => $upload['error'])));
            }

            if (!empty($upload['filename'])) {

                if ($type == 'logo') {
                    if (!empty($this->data['settings']['logo'])) {
                        unlink(dirname(dirname(dirname(__FILE__))) . '/resources/' . $this->data['settings']['logo']);
                    }

                    $settings = $this->Settings_model->get_all();
                    $data = empty($settings) ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
                    $setting_data = json_decode($data['setting_data'], true);
                    $setting_data['logo'] = $upload['filename'];
                    $data['setting_data'] = json_encode($setting_data);

                    $this->_save($data);
                } else if ($type == 'bg') {

                    if (!empty($this->data['settings']['bg'])) {
                        unlink(dirname(dirname(dirname(__FILE__))) . '/resources/' . $this->data['settings']['bg']);
                    }

                    $settings = $this->Settings_model->get_all();
                    $data = empty($settings) ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
                    $setting_data = json_decode($data['setting_data'], true);
                    $setting_data['bg'] = $upload['filename'];
                    $data['setting_data'] = json_encode($setting_data);

                    $this->_save($data);
                } else {
                    if (!empty($this->data['settings'][$type])) {
                        unlink(dirname(dirname(dirname(__FILE__))) . '/resources/' . $this->data['settings'][$type]);
                    }
                    $settings = $this->Settings_model->get_all();
                    $data = empty($settings) ? ['setting_id' => '', 'setting_data' => '[]'] : $settings[0];
                    $setting_data = json_decode($data['setting_data'], true);
                    $setting_data[$type] = $upload['filename'];
                    $data['setting_data'] = json_encode($setting_data);

                    $this->_save($data);
                }

                echo json_encode(array('filename' => $upload['filename'], 'settings' => $this->data['settings']));
            }
        }
    }

    public function dashboard($from = false, $to = false, $charge = false)
    {
        $this->load->model(['Pack_model', 'ClientPack_model', 'Client_model', 'Movement_model', 'ClientPeriod_model']);
        $movement_model = new Movement_model();
        $pack_model = new Pack_model();
        $clientPack_model = new ClientPack_model();
        $client_model = new Client_model();
        $clientPeriod_model = new ClientPeriod_model();

        if (empty($from) || empty($to)) {
            $start = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $end = date('Y-m-d 23:59:00');
        } else {
            $start = date('Y-m-d 00:00:00', strtotime(trim($from)));
            $end = date('Y-m-d 23:59:00', strtotime(trim($to)));
        }
        $this->data['start'] = $start;
        $this->data['end'] = $end;

        $this->data['charge'] = $charge;
        if ($charge) {
            $this->data['balances'] = $movement_model->balances($start, $end);
            $this->data['packs'] = $pack_model->dashboard($start, $end);
            $this->data['clientpacks'] = $clientPack_model->dashboard($start, $end);
            $this->data['clients'] = $client_model->dashboard($start, $end);
            $this->data['periods'] = $clientPeriod_model->balances($start, $end);

            $this->data['balances_global'] = $movement_model->balances();
            $this->data['clientpacks_global'] = $clientPack_model->dashboard();
            $this->data['periods_global'] = $clientPeriod_model->balances();
        }

        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
        $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/jquery.knob.min.js'];
        $this->javascript[] = $this->load->view('manager/home_js', $this->data, true);
        $this->_render('manager/home');
    }

    public function dashboard_datatables()
    {
        $this->load->model('ClientPack_model');
        echo json_encode($this->ClientPack_model->datatables_ajax_list());
    }

    public function sessionchange($userid)
    {
        if (empty($userid)) {
            die('User empty');
        }
        $allowed = array(
            1, //torresrodrigoe@gmail.com
            5  //silveiradeandrade.carlos@gmail.com
        );
        $this->load->model('User_model');
        if (in_array($this->session->userdata('user_id'), $allowed) || in_array($this->session->userdata('user_original'), $allowed)) {

            $user = $this->User_model->getUserById($userid);
            if (empty($user)) {
                die('User not found');
            }
            $user['user_original'] = $this->session->userdata('user_original') ? $this->session->userdata('user_original') : $this->session->userdata('user_id');
            $this->User_model->start_session($user);
            redirect('persons/all', 'refresh');
        } else {
            die('User not allowed ' . $this->session->userdata('user_id'));
        }
    }
}
