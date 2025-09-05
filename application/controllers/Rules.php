<?php

class Rules extends ManagerController
{

    protected $pathindex = '/rules/all';
    protected $viewpath = 'manager/rules/rule';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect($this->pathindex, 'refresh');
    }

    public function all($rule = false)
    {
        $this->load->model('Rules_model');
        $viewpath = $this->viewpath . '_list';
        $this->datatables_assets();
        $this->data['filters'] = $this->Rules_model->get_filters();
        $this->data['activesidebar'] = 'rules';
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->_render($viewpath);
    }

    public function datatables()
    {
        $this->load->model('Rules_model');
        echo json_encode($this->Rules_model->datatables_ajax_list());
    }

    public function form($_id = false)
    {
        $this->load->model('Rules_model');
        $this->data['types'] = [
            [
                "id" => 1,
                "name" => "Valor M&iacute;nimo"
            ], [
                "id" => 2,
                "name" => "Valor M&aacute;ximo"
            ], [
                "id" => 3,
                "name" => "SI/NO"
            ]
        ];
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            // $this->dd($post);
            $post['rule_name'] = trim(ucfirst($post['rule_name']));
            $this->_checkConstraint($post);
            if (empty($this->data['errors'])) {
                $id = $this->Rules_model->save($post);
                if (empty($post['rule_id'])) {
                    $post['rule_id'] = $id;
                    $this->logCreate($this->Rules_model, $post, 'c');
                } else {
                    $this->logCreate($this->Rules_model, $post, 'u');
                }
                redirect('/rules/all', 'refresh');
            } else {
                $this->data['edit'] = $post;
                if (!empty($_id)) {
                    $active =  $this->Rules_model->get_by_id($_id);
                    $this->data['edit']['rule_active'] = $active['rule_active'];
                } else {
                    $this->data['edit']['rule_active'] = 1;
                }
            }
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $this->Rules_model->get_by_id($_id);
            }
        }
        $this->data['activesidebar'] = 'rules';
        $this->_render($this->viewpath . '_form');
    }

    public function remove($_id = false)
    {
        $this->load->model('Rules_model');
        if (!empty($_id)) {
            $this->_checkRemove($_id);
            if (empty($this->data['errors'])) {
                $remove = $this->Rules_model->get_by_id($_id);
                $this->Rules_model->delete($_id);
                $this->logCreate($this->Rules_model, $remove, 'd');
            } else {
                echo json_encode($this->data);
                die();
            }
        }
        echo json_encode([]);
    }

    public function desactive($_id)
    {
        $this->load->model('Rules_model');
        $data = $this->Rules_model->get_by_id($_id);
        $data['rule_active'] = 0;
        $this->Rules_model->save($data);
        $this->logCreate($this->Rules_model, $data, 'u');
        redirect($this->pathindex, 'refresh');
    }

    public function active($_id)
    {
        $this->load->model('Rules_model');
        $data = $this->Rules_model->get_by_id($_id);
        $data['rule_active'] = 1;
        $this->Rules_model->save($data);
        $this->logCreate($this->Rules_model, $data, 'u');
        redirect($this->pathindex, 'refresh');
    }

    private function _checkConstraint($rule)
    {
        $this->_checkUnique($rule);
    }

    private function _checkUnique($rule)
    {
        $pr = $this->Rules_model->get_by_name_type($rule['rule_name'], $rule['rule_type']);
        if (!empty($pr)) {
            if ($pr['rule_id'] != $rule['rule_id']) {
                $this->data['errors'][] = '<p>Ya existe una regla con este nombre y tipo</p>';
            }
        }
    }

    private function _checkRemove($_id)
    {
        $this->load->model('Pack_model');
        $this->load->model('PackRules_model');
        $packRule = $this->PackRules_model->get_by_rule($_id);
        if (!empty($packRule)) {
            $msg = '<p>No es posible eliminar esta regla, se encuentra asignada al/los prducto/s:</p>';
            foreach ($packRule as $pr) {
                $pack = $this->Pack_model->get_by_id($pr['packrule_pack']);
                $msg .= '<ul>'
                    . '<li>' . $pack['pack_name'] . ' (' . $pack['pack_sessions'] . ')</li>'
                    . '</ul>';
            }
            $this->data['errors'][] = $msg;
        }
    }
}
