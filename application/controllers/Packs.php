<?php

class Packs extends ManagerController
{

    protected $pathindex = '/packs/all';
    protected $viewpath = 'manager/packs/pack';

    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        $this->load->model('Pack_model');
        $pack_model = new Pack_model();
        
        $viewpath = $this->viewpath . '_list';
        $this->data['activesidebar'] = 'packs';
        
        $this->data['filters'] = $pack_model->get_filters();
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        
        $this->datatables_assets();
        $this->_render($viewpath);
    }
    
    public function form($_id = false)
    {
        $this->load->model(['Pack_model', 'PackRules_model', 'PackPoints_model', 'PackDiscounts_model']);
        $pack_model = new Pack_model();
        $packpoint_model = new PackPoints_model();
        $packdiscount_model = new PackDiscounts_model();

        $this->data['activesidebar'] = 'packs';
        
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            // $this->dd($post);
            $this->_checkConstraint($post);
            if (empty($this->data['errors'])) {
                $post['pack_name'] = trim(ucfirst($post['pack_name']));

                $discounts = [];
                if (isset($post['packdiscounts'])) {
                    $discounts = $post['packdiscounts'];
                    unset($post['packdiscounts']);
                }

                $id = $pack_model->save($post);
                if (empty($post['pack_id'])) {
                    $post['pack_id'] = $id;
                    $this->logCreate($pack_model, $post, 'c');
                } else {
                    $this->logCreate($pack_model, $post, 'u');
                }

                $exist = $packdiscount_model->getActives($post['pack_id']);
                foreach ($exist as $row) {
                    $found = false;
                    foreach ($discounts as $discount) {
                        if ($discount['packdiscount_id'] === $row['packdiscount_id']) {
                            $found = true;
                        }
                    }
                    if (!$found) {
                        $this->db->set('packdiscount_to', "ADDTIME(CURRENT_TIMESTAMP, '-1')", false);
                        $packdiscount_model->save([
                            'packdiscount_id' => $row['packdiscount_id']
                        ]);
                    }
                }

                // redirect('/packrules/all/' . $id, 'refresh');
                redirect('/packs/all', 'refresh');
            }
            $this->data['edit'] = $post;
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $pack_model->get_by_id($_id);
                
                $this->data['edit']['packpoints'] = $packpoint_model->get_where(['packpoint_pack' => $_id]);
                $this->data['edit']['packdiscounts'] = $packdiscount_model->getActives($_id);
            }
        }
        
        $this->javascript[] = $this->load->view('manager/packs/pack_form_js', $this->data, true);
        
        $this->_render($this->viewpath . '_form');
    }

    public function datatables()
    {
        $this->load->model('Pack_model');
        $pack_model = new Pack_model();
        echo json_encode($pack_model->datatables_ajax_list());
    }

    public function desactive($_id)
    {
        $this->load->model('Pack_model');
        $this->checkPermission('desactive_pack');
        $data = $this->Pack_model->get_by_id($_id);
        $data['pack_active'] = 0;
        $this->Pack_model->save($data);
        $this->logCreate($this->Pack_model, $data, 'u');
        redirect($this->pathindex, 'refresh');
    }

    public function active($_id)
    {
        $this->load->model('Pack_model');
        $this->checkPermission('active_pack');
        $data = $this->Pack_model->get_by_id($_id);
        $data['pack_active'] = 1;
        $this->Pack_model->save($data);
        $this->logCreate($this->Pack_model, $data, '');
        redirect($this->pathindex, 'refresh');
    }

    private function _checkConstraint($post)
    {
        if (is_nan(floatval($post['pack_price'])) || floatval($post['pack_price']) <= 0) {
            $this->data['errors'][] = '<p>El valor del <b>Precio</b> no es <b>v&aacute;lido</b></p>';
        }
        if (is_nan(floatval($post['pack_commision'])) || floatval($post['pack_commision']) < 0) {
            $this->data['errors'][] = '<p>El valor del <b>Interes</b> no es <b>v&aacute;lido</b></p>';
        }
    }
}
