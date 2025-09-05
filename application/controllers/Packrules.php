<?php

class Packrules extends ManagerController
{

    protected $pathindex = '/packs/all';
    protected $viewpath = 'manager/packs/rules/packrule';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect($this->pathindex, 'refresh');
    }

    public function all($pack = false)
    {
        if (!empty($pack)) {
            $this->load->model('PackRules_model');
            $this->load->model(array('Pack_model'));
            $viewpath = $this->viewpath . '_list';
            $this->datatables_assets();
            $this->data['pack_id'] = $pack;
            $this->data['filters'] = []; //$this->PackRules_model->get_filters();
            if (!empty($pack)) {
                $this->data['pack'] = $this->Pack_model->get_by_id($pack);
            }

            $this->data['activesidebar'] = 'packs';
            $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
            $this->_render($viewpath);
        } else {
            redirect($this->pathindex, 'refresh');
        }
    }

    public function datatables()
    {
        $this->load->model('PackRules_model');
        echo json_encode($this->PackRules_model->datatables_ajax_list());
    }

    public function form($pack = false, $_id = false)
    {
        $this->load->model('PackRules_model');
        $this->load->model('Rules_model');
        $this->load->model('Pack_model');

        $viewpath = $this->viewpath . '_form';
        $this->data['pack_id'] = $pack;
        $this->data['pack'] = $this->Pack_model->get_by_id($pack);

        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            //            $this->dd($post);
            if (empty($post['packrule_id'])) {
                $this->_checkUnique($post);
            }
            if (empty($this->data['errors'])) {
                unset($post['rule_type']);
                $id = $this->PackRules_model->save($post, $this->data['pack']);
                if (empty($post['packrule_id'])) {
                    $post['packrule_id'] = $id;
                    $this->logCreate($this->PackRules_model, $post, 'c');
                } else {
                    $this->logCreate($this->PackRules_model, $post, 'u');
                }
                redirect('/packrules/all/' . $pack, 'refresh');
            } else {
                $this->data['edit'] = $post;
            }
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $this->PackRules_model->get_by_id($_id);
                //                $this->dd($this->data['edit']);
            }
        }

        $this->data['activesidebar'] = 'packs';
        $this->data['rules'] = $this->Rules_model->get_all();
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->_render($this->viewpath . '_form');
    }

    public function remove($_id = false)
    {
        $this->load->model('Pack_model');
        $this->load->model('PackRules_model');
        if (!empty($_id)) {
            $packrule = $this->PackRules_model->get_by_id($_id);
            $this->logCreate($this->PackRules_model, $packrule, 'd');
            $this->PackRules_model->delete($_id);
        }
        redirect('/packrules/all/' . $packrule['packrule_pack'], 'refresh');
    }

    private function _checkUnique($packrule)
    {
        $pr = $this->PackRules_model->get_by_pack_rule($packrule['packrule_pack'], $packrule['packrule_rule']);
        if (!empty($pr)) {
            $this->data['errors'][] = '<p>Ya existe esta regla</p>';
        }
    }
}
