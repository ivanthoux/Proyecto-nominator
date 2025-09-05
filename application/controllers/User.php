<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->data['pageName'] = 'user';
        $this->load->model(array('Settings_model'));
        $settings = $this->session->userdata('settings');
        $this->data['settings'] = $settings;
    }

    public function index() {
        redirect('user/login', 'refresh');
    }

    public function logout() {
        $tfcookie = array(
            'name' => 'user_logged',
            'value' => '',
            'expire' => 0,
        );
        $this->input->set_cookie($tfcookie);
        $this->session->sess_destroy();
        redirect('user/login', 'refresh');
    }

    public function email_is_active() {
        $useremail = strtolower($this->input->post('email'));
        $this->load->model('User_model');
        $user = $this->User_model->get_by_email($useremail);
        if (empty($user)) {
            $this->load->model('User_model');
            $user = $this->User_model->get_by_email($useremail);
            $user['user_active'] = 1;
        }
        if (!empty($user) && $user['user_active'] == 1) {
            return true;
        } else {
            $this->form_validation->set_message('email_is_active', 'La cuenta vinculada a ese email no está activada');

            return false;
        }
    }

    public function login() {
        if ($this->session->userdata('user_logged')) {
            redirect('/manager', 'refresh');
        }
        $this->javascript[] = $this->load->view('default/user/login_js', $this->data, true);
        $this->_render('default/user/login');
    }

    public function invite() {
        die('invite disabled');
        $this->_render('default/user/signup');
    }

    public function lost() {
        if ($this->session->userdata('user_logged')) {
            redirect($this->session->userdata('role_default_url'), 'refresh');
        }
        $this->_render('default/user/lost');
    }

    public function get_login() {
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $device = $this->input->post('device');

        //login anonymous
        if (!empty($device)) {
            $user = $this->User_model->get_by_device($this->input->post('device'));
            if (!empty($user)) {
                $this->User_model->start_session($user);
                log_message('debug', 'USER ANONYMOUS LOGGED ' . $this->session->userdata('user_id') . '');

                //creating cookie
                if ($this->input->post('createcookie') == 1) {
                    $cookie_hash = md5(rand(10000, 100000) . 'validate' . time());
                    $cookie = array(
                        'name' => 'user_logged',
                        'value' => $cookie_hash,
                        'expire' => 2592000,
                    );
                    $toupdate['user_cookie_hash'] = $cookie_hash;
                    $toupdate['user_cookie_time'] = time();
                    $this->input->set_cookie($cookie);

                    //save user cookie record
                    $this->User_model->update($user['user_id'], $toupdate);
                }

                if ($this->session->userdata('user_logged')) {
                    $logtry['status'] = 'success';
                    $logtry['user_id'] = $this->session->userdata('user_id');
                }
                echo json_encode($logtry);
            } else {
                $logtry['status'] = 'error';
                $logtry['errors'] = 'device no encontrado';
                echo json_encode($logtry);
            }
            die();
        }
        //normal login
        $this->form_validation->set_rules($this->User_model->login_validation());

        if ($this->form_validation->run() == true) {
            $user_email = strtolower($this->input->post('email'));
            $logtry = $this->makeLogin($user_email);
        } else {
            $logtry['errors'] = validation_errors();
        }
        echo json_encode($logtry);
        die();
    }

    public function makeLogin($user_email) {
        $this->load->model('User_model');
        $user = $this->User_model->get_by_email($user_email);
        if (empty($user)) {
            $this->load->model('User_model');
            $user = $this->User_model->get_by_email($user_email);
        }
        if (!empty($user)) {
            //load session
            $this->User_model->start_session($user);
            log_message('debug', 'USER LOGGED ' . $this->session->userdata('user_id') . '');

            //creating cookie
            if ($this->input->post('createcookie') == 1) {
                $cookie_hash = md5($user['user_email'] . 'validate' . time());
                $cookie = array(
                    'name' => 'user_logged',
                    'value' => $cookie_hash,
                    'expire' => 2592000,
                );
                $toupdate['user_cookie_hash'] = $cookie_hash;
                $toupdate['user_cookie_time'] = time();
                $this->input->set_cookie($cookie);

                //save user cookie record
                $this->User_model->update($user['user_id'], $toupdate);
            }

            if ($this->session->userdata('user_logged')) {
                $logtry['status'] = 'success';
                $logtry['user_id'] = $this->session->userdata('user_id');
            }
        } else {
            $logtry['status'] = 'error';
            $logtry['errors'] = 'usuario no encontrado';
        }

        return $logtry;
    }

    public function password_check() {
        $this->load->model('User_model');
        $user_email = strtolower($this->input->post('email'));
        $password = $this->input->post('password');

        $this->load->model('User_model');
        $user = $this->User_model->get_by_email($user_email);
        if (empty($user)) {
            $this->load->model('User_model');
            $user = $this->User_model->get_by_email($user_email);
        }
        if (empty($user)) {
            $this->form_validation->set_message('password_check', 'El email no existe');

            return false;
        }
        if (empty($password) && trim($password) == '') {
            $this->form_validation->set_message('password_check', 'El password es invalido');

            return false;
        }
        /* if ($user['user_active'] == 0) {
          $this->form_validation->set_message('password_check', 'Usuario no activo');
          return false;
          } */
        /* if ($user['user_rol'] == 'user') {
          $this->form_validation->set_message('password_check', 'Su tipo de usuario no puede ingresar');
          return false;
          } */
        if (empty($user['user_password']) || empty($user['user_password']) || ($user['user_password']) != md5($password)) {
            $this->form_validation->set_message('password_check', 'Password incorrecto');

            return false;
        } else {
            return true;
        }
    }

    public function switchlang($language = false) {
        $valid_lang = array('english', 'spanish');

        if (!empty($language) && in_array($language, $valid_lang)) {
            $this->session->set_userdata('site_lang', $language);
        }
        redirect(base_url());
    }

    public function create_user() {
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $user = $this->User_model->read_create_user_post();
        $this->form_validation->set_rules($this->User_model->new_validation());
        $user_id = 0;
        $logtry['status'] = 'error';
        if ($this->form_validation->run() == true) {
            $user_id = $this->User_model->save($user);
            $logtry['user_id'] = $user_id;
        } else {
            $logtry['errors'] = validation_errors();
        }
        log_message('debug', 'USER CREATED ' . $user['user_email'] . '');
        if ($user_id > 0) {
            $logtry['status'] = 'success';
            $user['user_id'] = $user_id;
            $this->send_activation_email($user);
        }
        echo json_encode($logtry);
        die();
    }

    /**
     * Method acceded from URL.
     *
     * @param type $user_id
     * @param type $activation_hash
     *
     * @return bool
     */
    public function activation($user_id, $activation_hash) {
        $this->load->model('User_model');
        $user = $this->User_model->get_by_id_activation_hash(
                $this->security->xss_clean($user_id), $this->security->xss_clean($activation_hash));
        if (empty($user)) {
            redirect(base_url());

            return false;
        } else {
            $this->User_model->update($user['user_id'], array('user_active' => '1'));
            $this->template = 'default';
            $this->title = lang('account.activation');
            $this->data['user_name'] = $user['user_firstname'];
            $this->_render('default/user/activation');

            return true;
        }
    }

    /**
     * Method called from home to get email and start reset password procedure.
     */
    public function get_password_reset() {
        $this->load->model('User_model');
        $this->load->library('form_validation');

        $this->form_validation->set_rules($this->User_model->start_reset_password_validation());
        $logtry['status'] = 'error';

        if ($this->form_validation->run() == true) {
            $user_email = strtolower($this->security->xss_clean($this->input->post('email')));
            $user = $this->User_model->get_by_email($user_email);
            if (!empty($user)) {
                $time = date('Y-m-d H:i:s', time());
                $hash = md5($time . 'hash' . $user_email);
                $data = array('user_password_reset_hash' => $hash,
                    'user_password_reset_time' => $time,);
                $this->User_model->update($user['user_id'], $data);
                $user['user_password_reset_hash'] = $hash;
                $this->send_reset_password_email($user);
                $logtry['status'] = 'success';
            } else {
                $logtry['errors'] = 'El email no existe';
            }
        } else {
            $logtry['errors'] = validation_errors();
        }
        echo json_encode($logtry);
    }

    /**
     * Method acceded from url, to draw reset password form.
     *
     * @param type $user_id
     * @param type $reset_hash
     *
     * @return bool
     */
    public function reset_password($user_id, $reset_hash) {
        $this->load->model('User_model');
        $user = $this->User_model->get_by_id_reset_password_hash(
                $this->security->xss_clean($user_id), $this->security->xss_clean($reset_hash));
        $this->data['user_id'] = $user_id;
        $this->data['reset_hash'] = $reset_hash;

        if (empty($user)) {
            redirect(base_url());

            return false;
        } else {
            $phpdate = strtotime($user['user_password_reset_time']);
            if ((time() - $phpdate) < 24 * 60 * 60) {
                $this->template = 'default';
                $this->title = 'Resetear contraseña';
                $this->_render('default/user/reset_password');
            } else {
                $data['heading'] = 'Reseteo de Contraseña';
                $data['message'] = 'Tenés 24 horas para usar el link enviado, vuelve a empezar el proceso';
                $this->load->view('errors/html/error_general', $data);
            }

            return true;
        }
    }

    /**
     * Method called from reset password form, to finish reset password procedure.
     */
    public function set_password() {
        $this->load->model('User_model');
        $this->load->library('form_validation');

        $this->form_validation->set_rules($this->User_model->reset_password_validation());
        $logtry['status'] = 'error';
        if ($this->form_validation->run() == true) {
            $user_id = $this->security->xss_clean($this->input->post('user_id'));
            $reset_hash = $this->security->xss_clean($this->input->post('reset_hash'));
            $password = $this->security->xss_clean($this->input->post('password'));
            $user = $this->User_model->get($user_id);
            $phpdate = strtotime($user['user_password_reset_time']);
            if (!empty($user) && $user['user_password_reset_hash'] == $reset_hash && (time() - $phpdate) < 24 * 60 * 60) {
                $data = array('user_password' => md5($password),
                    'user_password_reset_hash' => '',);
                $this->User_model->update($user_id, $data);
                $logtry['status'] = 'success';
            } else {
                $logtry['errors'] = lang('Hubo un error al cambiar la contraseña, vuelve a generar el enlace');
            }
        } else {
            $logtry['errors'] = validation_errors();
        }
        echo json_encode($logtry);
    }

    // Email functions

    public function send_activation_email($user) {
        $this->send_activation_email_without_tasker($user);
    }

    public function send_activation_email_without_tasker($user) {
        $this->data['user_name'] = $user['user_firstname'];
        $this->data['button_link'] = base_url() . 'user/activation/' . $user['user_id'] . '/' . $user['user_activation_hash'];
        $body = $this->load->view('mails/activation', $this->data, true);

        send_email([
            'to' => $user['user_email'],
            'subject' => 'Referencias Comerciales - Activar cuenta',
            'message' => $body,
        ]);
    }

    public function resend_activation_email() {
        $return['status'] = 'error';
        if ($this->session->userdata('user_logged')) {
            $user['user_id'] = $this->session->userdata('user_id');
            $user['user_firstname'] = $this->session->userdata('user_firstname');
            $user['user_email'] = $this->session->userdata('user_email');
            if ($this->session->userdata('user_activation_hash') !== '') {
                $user['user_activation_hash'] = $this->session->userdata('user_activation_hash');
            } else {
                $user['user_activation_hash'] = $this->User_model->create_activation_hash($user['user_email']);
                $this->User_model->update($user['user_id'], array('user_activation_hash' => $user['user_activation_hash']));
            }

            if ($this->send_activation_email($user)) {
                $return['status'] = 'success';
            }
        }
        echo json_encode($return);
    }

    public function send_reset_password_email($user) {
        $this->data['top_right_link'] = base_url();
        $this->data['user_name'] = $user['user_firstname'];
        $this->data['button_link'] = base_url() . 'user/reset_password/' . $user['user_id'] . '/' . $user['user_password_reset_hash'];
        $body = $this->load->view('mails/reset_password', $this->data, true);
        
        send_email([
                'to' => $user['user_email'],
                'subject' => 'Resetear contraseña',
                'message' => $body,
            ]);
    }

    public function keep_alive() {
        $return['status'] = 'error';
        if ($this->session->userdata('user_logged')) {
            $return['status'] = 'alive';
        }
        echo json_encode($return);
    }

    public function account_verified() {
        $return['status'] = 'error';
        if ($this->session->userdata('user_verified')) {
            $return['status'] = 'verified';
        }
        echo json_encode($return);
    }

    public function del($id) {
        $this->db->delete('users', array('user_id' => $id));
    }

    public function contact_send() {
        $contact = array(
            'name' => $this->security->xss_clean($this->input->post('name')),
            'email' => $this->security->xss_clean($this->input->post('email')),
            'dates' => $this->security->xss_clean($this->input->post('date_in_out')),
            'people' => $this->security->xss_clean($this->input->post('people')),
            'message' => $this->security->xss_clean($this->input->post('message')),
            'roomtype' => $this->security->xss_clean($this->input->post('roomtype')),
        );

        $this->load->library('form_validation');

        $this->form_validation->set_rules($this->new_contact());
        $this->form_validation->set_data($contact);
        $logtry['status'] = 'error';
        if ($this->form_validation->run() == true) {
            $logtry['status'] = 'success';

            $body = $this->load->view('mails/contact', $this->data, true);

            send_email([
                'to' => $this->data['settings']['email'],
                'subject' => 'Nuevo Contacto en Cabañas Oberá',
                'message' => $body,
            ]);
        } else {
            $logtry['errors'] = validation_errors();
        }
    }

    public function new_contact() {
        $config = array(
            array(
                'field' => 'name',
                'label' => 'nombre',
                'rules' => 'required',
            ),
            array(
                'field' => 'email',
                'label' => 'correo',
                'rules' => 'trim|required|strtolower',
            ),
        );

        return $config;
    }

    public function email_no_exist() {
        $useremail = strtolower($this->input->post('email', true));

        $user = $this->User_model->get_by_email($useremail);
        if (empty($user)) {
            return true;
        } else {
            $this->form_validation->set_message('email_no_exist', 'El email ya esta registrado');

            return false;
        }
    }

    public function get_signed() {
        die('invite disabled');
        $this->load->model('User_model');
        $this->load->library('form_validation');

        $this->form_validation->set_rules($this->User_model->signup_validation());

        if ($this->form_validation->run() == true) {
            $this->User_model->signup();

            if ($this->session->userdata('user_id')) {
                $this->data['status'] = 'success';
            } else {
                $this->data['errors'] = 'Algo salio mal';
            }
        } else {
            $this->data['edit'] = $this->input->post();
            $this->data['errors'] = validation_errors();
        }
        $this->_render('', 'JSON');
    }

    public function testemail() {
        $email = send_email(array(
            'to' => 'torresrodrigoe@gmail.com',
            'subject' => "probando",
            'message' => 'Working baby'
        ));
    }

}
