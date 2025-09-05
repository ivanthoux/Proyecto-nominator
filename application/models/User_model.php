<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    protected $table = 'users';
    protected $primary_key = 'user_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function table()
    {
        return $this->table;
    }

    public function primary_key()
    {
        return $this->primary_key;
    }

    public function save($tosave)
    {
        if ($this->input->post('user_id')) {
            $id = $this->input->post('user_id');
        }

        if (!empty($id)) {
            $this->db->where('user_id', $id);
            $this->db->update('users', $tosave);

            return $id;
        } else {
            $tosave['user_active'] = isset($tosave['user_active']) ? $tosave['user_active'] : 0;
            $tosave['user_created'] = date('Y-m-d G:i:s');
            $tosave['user_account'] = $this->session->userdata('user_account');

            $this->db->insert('users', $tosave);

            return $this->db->insert_id();
        }
    }

    public function create_if_not_exist($userdata, $role_key)
    {
        $role = $this->db->where('role_key', $role_key)->get('roles')->row_array();
        $user = $this->get_where(['user_doc' => $userdata['employee']]);

        if (empty($user['user_id'])) {
            $user['user_firstname'] = $userdata['user_firstname'];
            $user['user_lastname'] = $userdata['user_lastname'];
            $user['user_email'] = $userdata['user_email'];
            $user['user_phone'] = $userdata['user_phone'];
            $user['user_incomplete'] = 1;
            $user['user_active'] = 1;
            $user['user_role_id'] = !empty($role) ? $role['role_id'] : 1;

            $user['user_id'] = $this->save($user, 0);
        }

        return $user;
    }

    public function create_if_not_exist_by_employeer($userdata)
    {
        $user = $this->getUserById($userdata['employeer']);

        if (empty($user['user_id'])) {
            $user['user_firstname'] = $userdata['employeer'];
            $user['user_lastname'] = '';
            $user['user_email'] = '';
            $user['user_phone'] = '';
            $user['user_incomplete'] = 1;
            $user['user_role_id'] = 3;
            $user['user_active'] = 1;
            $user['user_id'] = $this->save($user, 0);
        }

        return $user;
    }

    public function load_session()
    {
        if ($this->session->userdata('user_id')) {
            $user = $this->get($this->session->userdata('user_id'));
            $this->start_session($user);
        }
    }

    public function start_session($user)
    {
        //user logged flag - for all users
        $user['user_logged'] = true;
        //admin logged flag - for manager backend access only
        $user['admin_logged'] = true;

        $this->session->set_userdata($user);
    }

    public function new_validation()
    {
        $config = array(
            array(
                'field' => 'firstname',
                'label' => 'nombre',
                'rules' => 'required',
            ),
            array(
                'field' => 'lastname',
                'label' => 'apellido',
                'rules' => 'required',
            ),
            array(
                'field' => 'email',
                'label' => 'correo',
                'rules' => 'trim|required|strtolower|callback_email_no_exist',
            ),
            array(
                'field' => 'password',
                'label' => 'contraseña',
                'rules' => 'trim|required|min_length[6]',
            ),
            array(
                'field' => 'password_repeat',
                'label' => 'repetir contraseña',
                'rules' => 'trim|required|matches[password]',
            ),
        );

        return $config;
    }

    public function login_validation()
    {
        $config = array(
            array(
                'field' => 'email',
                'label' => 'usuario',
                'rules' => 'required|callback_email_is_active',
            ),
            array(
                'field' => 'password',
                'label' => 'contraseña',
                'rules' => 'required|callback_password_check',
            ),
        );

        return $config;
    }

    public function employer_register_validation($checkPassword = true, $edit = false)
    {
        $config = array(
            array(
                'field' => 'user_email',
                'label' => 'Email',
                'rules' => 'trim|required|strtolower' . (!$edit ? '|callback_email_employer_no_exist' : ''),
            ),
            array(
                'field' => 'user_firstname',
                'label' => 'Nombre de la Empresa',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_dni_cuit',
                'label' => 'CUIT de la Empresa',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_manager_firstname',
                'label' => 'Nombre del encargado',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_manager_lastname',
                'label' => 'Apellido del encargado',
                'rules' => 'required',
            ),
        );
        if ($checkPassword) {
            $config[] = array(
                'field' => 'user_password',
                'label' => 'Contraseña',
                'rules' => 'trim|required|min_length[6]',
            );
            $config[] = array(
                'field' => 'user_password_repeat',
                'label' => 'Repetir Contraseña',
                'rules' => 'trim|required|matches[user_password]',
            );
        }

        return $config;
    }

    public function employee_register_validation($checkPassword = true, $edit = false)
    {
        $config = array(
            array(
                'field' => 'user_email',
                'label' => 'Email',
                'rules' => 'trim|required|strtolower' . (!$edit ? '|callback_email_employer_no_exist' : ''),
            ),
            array(
                'field' => 'user_firstname',
                'label' => 'Nombre',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_lastname',
                'label' => 'Nombre',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_dni_cuit',
                'label' => 'DNI',
                'rules' => 'required' . (!$edit ? '|callback_dni_user_no_exist' : ''),
            ),
        );
        if ($checkPassword) {
            $config[] = array(
                'field' => 'user_password',
                'label' => 'Contraseña',
                'rules' => 'trim|required|min_length[6]',
            );
            $config[] = array(
                'field' => 'user_password_repeat',
                'label' => 'Repetir Contraseña',
                'rules' => 'trim|required|matches[user_password]',
            );
        }

        return $config;
    }

    public function start_reset_password_validation()
    {
        $config = array(
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'trim|required|strtolower',
            ),
        );

        return $config;
    }

    public function reset_password_validation()
    {
        $config = array(
            array(
                'field' => 'password',
                'label' => 'password',
                'rules' => 'trim|required|min_length[6]',
            ),
            array(
                'field' => 'password_repeat',
                'label' => 'repeat password',
                'rules' => 'trim|required|matches[password]',
            ),
        );

        return $config;
    }

    public function manager_validation($profile = false)
    {
        $config = array(
            array(
                'field' => 'user_firstname',
                'label' => 'nombre',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_lastname',
                'label' => 'apellido',
                'rules' => 'required',
            ),
            array(
                'field' => 'user_password',
                'label' => 'contraseña',
                'rules' => 'trim|min_length[6]',
            ),
            array(
                'field' => 'user_password_repeat',
                'label' => 'repetir contraseña',
                'rules' => 'trim|matches[user_password]',
            ),
        );

        if (!$profile) {
            $config[] = array(
                'field' => 'user_email',
                'label' => 'correo',
                'rules' => 'trim|required|strtolower|callback_email_no_exist',
            );
        }

        return $config;
    }

    public function signup_validation($profile = false)
    {
        $config = array(
            array(
                'field' => 'firstname',
                'label' => 'nombre',
                'rules' => 'required',
            ),
            array(
                'field' => 'lastname',
                'label' => 'apellido',
                'rules' => 'required',
            ),
            array(
                'field' => 'password',
                'label' => 'contraseña',
                'rules' => 'trim|min_length[6]',
            ),
            array(
                'field' => 'password_repeat',
                'label' => 'repetir contraseña',
                'rules' => 'trim|matches[password]',
            ),
        );

        if (!$profile) {
            $config[] = array(
                'field' => 'email',
                'label' => 'correo',
                'rules' => 'trim|required|strtolower|callback_email_no_exist',
            );
        }

        return $config;
    }

    public function read_create_user_post()
    {
        return array(
            'user_email' => $this->security->xss_clean(strtolower($this->input->post('email'))),
            'user_password' => md5($this->input->post('password')),
            'user_firstname' => $this->security->xss_clean($this->input->post('firstname')),
            'user_lastname' => $this->security->xss_clean($this->input->post('lastname')),
            'user_created' => date('Y-m-d H:i:s', time()),
            'user_rol' => 'user',
            'user_verified' => 1,
            'user_activation_hash' => $this->create_activation_hash($this->input->post('email')),
        );
    }

    public function create_activation_hash($email)
    {
        return md5('activation' . $this->input->post('email'));
    }

    public function _read_manager_post()
    {
        $post = $this->input->post();
        $post['user_active'] = $this->input->post('user_active') ? 1 : 0;
        $post['user_incomplete'] = empty($this->input->post('user_incomplete')) ? 0 : 1;

        if ($this->input->post('user_email')) {
            $post['user_email'] = strtolower($this->input->post('user_email', true));
        }

        if (!empty($this->input->post('user_password')) && !empty($this->input->post('user_password_repeat')) && $this->input->post('user_password') == $this->input->post('user_password_repeat')) {
            $post['user_password'] = md5($this->input->post('user_password', true));
        } else {
            unset($post['user_password']);
        }
        unset($post['user_password_repeat']);

        return $post;
    }

    public function get_by_email($useremail, $userid = false)
    {
        $this->db->select('users.*, roles.role_key as user_rol, roles.role_name as user_rol_label, role_default_url ');
        $this->db->limit(1);
        $this->db->where(array('user_email' => $useremail));
        $this->db->join('roles', 'roles.role_id = users.user_role_id');

        if (!empty($userid)) {
            $this->db->where('user_id !=', $userid);
        }
        $user = $this->db->get('users')->row_array();

        if (!empty($user)) {
            $this->db->select('permissions.*');
            $this->db->where(array('role_id' => $user['user_role_id']));
            $this->db->join('permissions', 'permissions.permission_id = role_permission.permission_id');
            $permissions = $this->db->get('role_permission')->result_array();

            $this->db->select('permissions.*')
                ->from('user_permission')
                ->join('permissions', 'permissions.permission_id = user_permission.user_permission_permission')
                ->where(array('user_permission_user'=>$user['user_id']));
            $user_permissions = $this->db->get()->result_array();
            if(!empty($user_permissions)){
                $permissions = array_merge($permissions, $user_permissions);
            }
            $user['permissions'] = $this->get_permissions_array($permissions);

            return $this->load_user_data($user);
        } else {
            return false;
        }
    }

    public function get_permissions_array($permissions)
    {
        $arr = [];

        foreach ($permissions as $permission) {
            $arr[] = $permission['permission_key'];
        }

        return $arr;
    }

    public function get_by_id_activation_hash($user_id, $activation_hash)
    {
        $this->db->limit(1);
        $user = $this->db->get_where('users', array('user_id' => $user_id, 'user_activation_hash' => $activation_hash))->row_array();

        return $this->load_user_data($user);
    }

    public function get_by_id_reset_password_hash($user_id, $reset_hash)
    {
        $this->db->limit(1);
        $user = $this->db->get_where('users', array('user_id' => $user_id, 'user_password_reset_hash' => $reset_hash))->row_array();

        return $this->load_user_data($user);
    }

    public function get($id)
    {
        $this->db->limit(1);
        $this->db->select('users.*, roles.role_key as user_rol, roles.role_name as user_rol_label ');
        $this->db->join('roles', 'roles.role_id = users.user_role_id', 'LEFT');
        $user = $this->db->where(array('user_id' => $id))->get('users')->row_array();

        return $this->load_user_data($user);
    }

    public function getUserById($id)
    {
        $user = $this->User_model->get($id);

        if (!empty($user)) {
            $this->db->select('permissions.*');
            $this->db->where(array('role_id' => $user['user_role_id']));
            $this->db->join('permissions', 'permissions.permission_id = role_permission.permission_id');
            $permissions = $this->db->get('role_permission')->result_array();

            $user['permissions'] = $this->get_permissions_array($permissions);
        }
        return $user;
    }

    public function getSYSTEM()
    {
        $user = $this->db->select('users.*, roles.role_key as user_rol, roles.role_name as user_rol_label ')
            ->join('roles', 'roles.role_id = users.user_role_id', 'LEFT')
            ->where('user_password', 'SYSTEM')
            ->get($this->table)->row_array();

        if (!empty($user)) {
            $this->db->select('permissions.*');
            $this->db->where(array('role_id' => $user['user_role_id']));
            $this->db->join('permissions', 'permissions.permission_id = role_permission.permission_id');
            $permissions = $this->db->get('role_permission')->result_array();

            $user['permissions'] = $this->get_permissions_array($permissions);
        }
        return $user;
    }

    public function count_all()
    {
        $this->db->from('users');
        $this->db->join('roles', 'roles.role_id = users.user_role_id', 'left');
        $this->db->where('user_account', $this->session->userdata('user_account'));
        // if (!empty($params['search']) && !empty($params['search']['value'])) {
        //     $params['search'] = $params['search']['value'];
        //     $this->db->where("(`user_firstname` LIKE '%" . $params['search'] . "%' OR `user_lastname` LIKE '%" . $params['search'] . "%' OR `user_email` LIKE '%" . $params['search'] . "%')");
        // }
        $get = $this->input->get();
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }

        return $this->db->count_all_results();
    }

    public function get_all()
    {
        $params['search'] = $this->input->post('search', true);
        $params['order'] = $this->input->post('order', true);
        $params['length'] = $this->input->post('length', true);
        $params['start'] = $this->input->post('start', true);
        $this->db->select('users.*, roles.role_key as user_rol, roles.role_name as user_rol_label ');
        $this->db->join('roles', 'roles.role_id = users.user_role_id', 'LEFT');
        $this->db->where('user_account', $this->session->userdata('user_account'));
        //        $this->db->where('user_active', 1);
        //$this->db->where("(roles.role_key = 'super' OR roles.role_key = 'admin')");

        if (!empty($params['search']) && !empty($params['search']['value'])) {
            $params['search'] = $params['search']['value'];
            $this->db->where("(`user_firstname` LIKE '%" . $params['search'] . "%' OR `user_lastname` LIKE '%" . $params['search'] . "%' OR `user_email` LIKE '%" . $params['search'] . "%')");
        }

        $get = $this->input->get();
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter => $val) {
                if (!empty($val)) {
                    $this->db->where($filter, trim($val));
                }
            }
        }

        if (!empty($params['start']) && !empty($params['length'])) {
            $this->db->limit($params['length'], $params['start']);
        }
        if (!empty($params['length'])) {
            $this->db->limit($params['length']);
        }

        switch ($params['order'][0]['column']) {
            case 0:
                $this->db->order_by('user_firstname', $params['order'][0]['dir']);
                break;
            case 1:
                $this->db->order_by('user_email', $params['order'][0]['dir']);
                break;
            case 2:
                $this->db->order_by('user_rol', $params['order'][0]['dir']);
                break;
            case 3:
                $this->db->order_by('user_password_reset_time', $params['order'][0]['dir']);
                break;
        }

        $all = $this->db->get('users')->result_array();
        if (!empty($all)) {
            foreach ($all as $k => $user) {
                $all[$k] = $this->load_user_data($user);
            }
        }

        return $all;
    }

    public function get_filters()
    {
        $filters = array();
        $options = array();
        foreach ($filters as $field) {
            // if ($field == 'user_office') {
            //     $this->db->select('office_name, user_office')->group_by($field)->order_by('office_name', 'ASC');
            //     $this->db->join('offices', 'office_id = user_office', 'left');
            //     $temp = $this->db->get('users')->result_array();
            //     foreach ($temp as $op) {
            //         $options[$field][] = array('title' => $op['office_name'], 'value' => $op[$field]);
            //     }
            // }
        }
        //        echo '<pre>';
        //        print_r($options);
        //        die();
        return $options;
    }

    public function load_user_data($user)
    {
        return $user;
    }

    public function del($id)
    {
        return $this->db->where('user_id', $id)->update('users', array('user_active' => 0));
    }

    public function active($id)
    {
        return $this->db->where('user_id', $id)->update('users', array('user_active' => 1));
    }

    public function cookie_isvalid($cookie)
    {
        $cookie_duration = 60 * 60 * 24 * 30;

        $user = $this->db->get_where('users', array('user_cookie_hash' => $cookie))->row_array();
        if (!empty($user)) {
            if ($user['user_cookie_time'] + $cookie_duration > time()) {
                $this->db->where('user_id', $user['user_id']);
                $this->db->update('users', array('user_cookie_time' => time()));

                $tfcookie = array(
                    'name' => 'user_logged',
                    'value' => $cookie,
                    'expire' => 2592000,
                    'domain' => site_url(),
                    'path' => '/',
                );
                $this->input->set_cookie($tfcookie);

                return $user;
            }
        }

        return false;
    }

    public function check_cookie()
    {
        if ($this->session->userdata('user_logged')) {
            return true;
        } else {
            //checking cookie
            $sessioncookie = $this->input->cookie('user_logged', true);
            if ($sessioncookie) {
                $user = $this->cookie_isvalid($sessioncookie);
                if ($user) {
                    //cookie exists - create session
                    $this->start_session($user);

                    return true;
                }
            }
        }
    }

    public function update($userid, $data)
    {
        $this->db->where('user_id', $userid);
        $this->db->update('users', $data);
    }

    public function get_emails_by_role($role_key)
    {
        $this->db->from('users');
        $this->db->join('roles', 'roles.role_id = users.user_role_id');
        $this->db->where(array('role_key' => $role_key));
        $this->db->where(array('user_incomplete' => 0));
        $this->db->where(array('user_active' => 1));
        if ($this->session->userdata('user_account')) {
            $this->db->where(array('user_account' => $this->session->userdata('user_account')));
        }
        $emails = [];
        $rows = $this->db->get()->result_array();

        foreach ($rows as $row) {
            $emails[] = $row['user_email'];
        }

        return $emails;
    }

    public function count_all_users($params = false)
    {
        if (empty($params)) {
            $params['from'] = $this->input->get('from', true);
            $params['to'] = $this->input->get('to', true);
        }

        if (!empty($params['group_by'])) {
            $this->db->select($params['group_by'] . '(user_created) as label, count(*) as q');
        }
        $this->db->from('users');
        $this->db->where(array('user_active' => 1));
        $this->db->where(array('user_rol' => 'user'));
        $this->db->where('user_account', $this->session->userdata('user_account'));

        if (!empty($params['from'])) {
            $this->db->where(array('user_password_reset_time >' => date('Y-m-d', strtotime($params['from']))));
        }
        if (!empty($params['to'])) {
            $this->db->where(array('user_password_reset_time <' => date('Y-m-d', strtotime($params['to']))));
        }

        if (!empty($params['group_by'])) {
            $this->db->group_by($params['group_by'] . '(user_created)');

            return $this->db->get()->result_array();
        } else {
            return $this->db->count_all_results();
        }
    }

    protected function extend_datatable_query()
    {
        $this->db->where('user_account', $this->session->userdata('user_account'));
    }

    public function signup()
    {
        $tosave = $this->input->post();
        $account = array(
            'setting_account' => $tosave['account'],
            'setting_created' => date('Y-m-d G:i:s'),
            'setting_data' => json_encode(array('name' => $tosave['account'])),
        );
        $this->db->insert('settings', $account);
        $account_id = $this->db->insert_id();

        $data['user_firstname'] = $tosave['firstname'];
        $data['user_lastname'] = $tosave['lastname'];
        $data['user_email'] = $tosave['email'];
        $data['user_active'] = 1;
        $data['user_verified'] = 1;
        $data['user_role_id'] = 1;
        $data['user_password'] = md5($tosave['password']);
        $data['user_created'] = date('Y-m-d G:i:s');
        $data['user_account'] = $account_id;
        $this->db->insert('users', $data);

        $user_id = $this->db->insert_id();
        $user = $this->get($user_id);
        $this->start_session($user);
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['user_id' => $id])->row_array();
    }

    public function get_where($where = [], $columns = ['*'])
    {
        $this->db->select($columns);

        $result = $this->db->where($where)->get('users');
        if (!empty($result)) {
            return $result->result_array();
        }
        return false;
    }
}
