<?php

class Clientfiles extends ManagerController
{

    protected $pathindex = '/clients';
    protected $viewpath = 'manager/clients/files/clientfile';
    private $state;
    private $documents = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect($this->pathindex, 'refresh');
    }

    public function datatables()
    {
        $this->load->model('ClientFile_model');
        echo json_encode($this->ClientFile_model->datatables_ajax_list());
    }

    public function all($client = false)
    {
        $this->load->model(['Client_model', 'ClientFile_model', 'ClientPack_model', 'PackRules_model', 'Rules_model']);
        $client_model = new Client_model();

        $viewpath = $this->viewpath . '_list';

        $this->data['client_id'] = $client;
        if (!empty($client)) {
            $this->data['client'] = $client_model->get_by_id($client);
            if (!empty($this->data['client']['client_parent'])) {
                $this->data['parent'] = $client_model->get_by_id($this->data['client']['client_parent']);
            }
        } else {
            redirect('clients/all', 'refresh');
        }

        $this->_checkDocuments($client, false);
        $this->data['documents'] = $this->documents;

        $this->data['activesidebar'] = 'clients';
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

        $this->datatables_assets();
        $this->_render($viewpath);
    }

    public function form($client = false, $_id = false)
    {
        $this->load->model(['Client_model', 'ClientFile_model', 'ClientPack_model', 'PackRules_model', 'Rules_model']);
        $client_model = new Client_model();
        $rules_model = new Rules_model();
        $clientFile_model = new ClientFile_model();

        $this->data['client_id'] = $client;
        $this->data['client'] = $client_model->get_by_id($client);
        $this->data['types'] = $rules_model->get_where(['rule_type_doc_require' => 1]);

        $this->data['activesidebar'] = 'clients';
        $viewpath = $this->viewpath . '_form';

        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            $upload = array_shift($_FILES);

            $this->load->helper('upload_helper');
            $upload = upload_worker($upload, dirname(dirname(dirname(__FILE__))) . '/resources/clients/', 'doc', true);
            
            if (!empty($upload)) {
                if ($upload['error_code'] != 4) {
                    if (!empty($upload['error'])) {
                        log_message('error', 'API - ' . $upload['error']);
                        $this->data['errors'][] = $upload['error'];
                    } else {
                        if (!empty($upload['filename'])) {
                            if (!empty($post['clientfile_id'])) {
                                $clientfile = $clientFile_model->get_by_id($post['clientfile_id']);
                                if (!empty($clientfile['clientfile_file'])) {
                                    unlink(dirname(dirname(dirname(__FILE__))) . '/resources/clients/' . $clientfile['clientfile_file']);
                                }
                            }
                            $post['clientfile_file'] = $upload['filename'];
                        }
                    }
                } else if (empty($post['clientfile_id'])) {
                    $this->data['errors'][] = 'Debe seleccionar un archivo';
                } else if ($post['clientfile_type'] != 2) {
                    $post['clientfile_ocr'] = '';
                } else {
                    $clientfile = $clientFile_model->get_by_id($post['clientfile_id']);
                    $post['clientfile_file'] = $clientfile['clientfile_file'];
                }
            }
            if (empty($this->data['errors'])) {
                if (empty($this->data['errors'])) {
                    $id = $clientFile_model->save($post);

                    $this->_checkDocuments($post['clientfile_client']);

                    if (empty($post['clientfile_id'])) {
                        $post['clientfile_id'] = $id;
                        $this->logCreate($this->ClientFile_model, $post, 'c');
                    } else {
                        $this->logCreate($this->ClientFile_model, $post, 'u');
                    }
                    
                    redirect('clientfiles/all/' . $client . '/' . $post['clientfile_id'], 'refresh');
                } else {
                    $this->data['edit'] = $post;
                }
            } else {
                $this->data['edit'] = $post;
            }
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $clientFile_model->get_by_id($_id);
            }
        }
        
        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/fileinput.min.css'];
        $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/fileinput_bootstrap.min.js'];
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->_render($this->viewpath . '_form');
    }

    public function remove($client = false, $id = false)
    {
        $this->load->model('ClientFile_model');

        if (!empty($id)) {
            $clientfile = $this->ClientFile_model->get_by_id($id);
            if (!empty($clientfile['clientfile_file'])) {
                unlink(dirname(dirname(dirname(__FILE__))) . '/resources/clients/' . $clientfile['clientfile_file']);
            }
            $this->ClientFile_model->delete($id);
        }
        redirect('clientfiles/all/' . $client);
    }

    private function _checkDocuments($clientpack_client, $save = true)
    {
        $clientpack_model = new ClientPack_model();
        $packrules_model = new PackRules_model();
        $clientFile_model = new ClientFile_model();
        $packRules_model = new PackRules_model();
        $rules_model = new Rules_model();

        $packs = $clientpack_model->get_where([
            'clientpack_client' => $clientpack_client,
            'clientpack_state >=' => 5,
        ]);

        foreach ($packs as $pack) {
            $rules = $packrules_model->get_where([
                'packrule_pack' => $pack['clientpack_package']
            ]);

            if ($pack['clientpack_state'] >= 5) {
                $this->state = intval($pack['clientpack_state']) - 4;

                $files = $clientFile_model->get_files_by_client($clientpack_client);
                $types = [];
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $types[] = $file['clientfile_type'];
                    }
                }
                // $this->dd($this->state);
                foreach ($rules as $packrule) {
                    $pr = $packRules_model->get_by_id($packrule['packrule_id']);
                    $r = $rules_model->get_by_id($pr['packrule_rule'], ['rule_active', true]);
                    if (!in_array($r['rule_id'], $types) && $r['rule_type_doc_require']) {
                        $this->state += 4;

                        if (!in_array($r['rule_name'], $this->documents)) {
                            $this->documents[] = $r['rule_name'];
                        }

                        if ($save) {
                            break;
                        }
                    }
                }

                if ($this->state != $pack['clientpack_state'] && $save) {
                    $pack['clientpack_state'] = $this->state;
                    $clientpack_model->save($pack);
                }
            }
        }
    }
}
