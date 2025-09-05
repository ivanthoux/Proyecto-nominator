<?php

class Personfiles extends ManagerController
{

    protected $pathindex = '/persons';
    protected $viewpath = 'manager/persons/files/personfile';
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
        $this->load->model('PersonFile_model');
        echo json_encode($this->PersonFile_model->datatables_ajax_list());
    }

    public function all($person = false)
    {
        $this->load->model(['Persons_model', 'PersonFile_model']);
        $person_model = new Persons_model();

        $viewpath = $this->viewpath . '_list';

        $this->data['person_id'] = $person;
        if (!empty($person)) {
            $this->data['person'] = $person_model->get_by_id($person);
            if (!empty($this->data['person']['person_parent'])) {
                $this->data['parent'] = $person_model->get_by_id($this->data['person']['person_parent']);
            }
        } else {
            redirect('persons/all', 'refresh');
        }

        $this->data['documents'] = $this->documents;

        $this->data['activesidebar'] = 'persons';
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);

        $this->datatables_assets();
        $this->_render($viewpath);
    }

    public function form($person = false, $_id = false)
    {
        $this->load->model(['Persons_model', 'PersonFile_model', 'ClientPack_model']);
        $person_model = new Persons_model();
        $personFile_model = new PersonFile_model();

        $this->data['person_id'] = $person;
        $this->data['person'] = $person_model->get_by_id($person);

        $this->data['activesidebar'] = 'persons';
        $viewpath = $this->viewpath . '_form';

        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            $upload = array_shift($_FILES);
            $this->load->helper('upload_helper');
            $upload = upload_worker($upload, dirname(dirname(dirname(__FILE__))) . '/resources/persons/', 'doc', true);
            // $this->dd($upload);

            if (!empty($upload)) {
                if (!isset($upload['error_code']) || (isset($upload['error_code']) && $upload['error_code'] != 4)) {
                    if (!empty($upload['error'])) {
                        log_message('error', 'API - ' . $upload['error']);
                        $this->data['errors'][] = $upload['error'];
                    } else {
                        if (!empty($upload['filename'])) {
                            if (!empty($post['personfile_id'])) {
                                $personfile = $personFile_model->get_by_id($post['personfile_id']);
                                if (!empty($personfile['personfile_file'])) {
                                    unlink(dirname(dirname(dirname(__FILE__))) . '/resources/persons/' . $personfile['personfile_file']);
                                }
                            }
                            $post['personfile_file'] = $upload['filename'];
                        }
                    }
                } else {
                    $personfile = $personFile_model->get_by_id($post['personfile_id']);
                    $post['personfile_file'] = $personfile['personfile_file'];
                }
            }
            if (empty($this->data['errors'])) {
                if (empty($this->data['errors'])) {
                    $id = $personFile_model->save($post);

                    if (empty($post['personfile_id'])) {
                        $post['personfile_id'] = $id;
                    }

                    redirect('Personfiles/all/' . $person . '/' . $post['personfile_id'], 'refresh');
                } else {
                    $this->data['edit'] = $post;
                }
            } else {
                $this->data['edit'] = $post;
            }
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $personFile_model->get_by_id($_id);
            }
        }

        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/fileinput.min.css'];
        $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/fileinput_bootstrap.min.js'];
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->_render($this->viewpath . '_form');
    }

    public function remove($person = false, $id = false)
    {
        $this->load->model('PersonFile_model');

        if (!empty($id)) {
            $personfile = $this->PersonFile_model->get_by_id($id);
            if (!empty($personfile['personfile_file'])) {
                unlink(dirname(dirname(dirname(__FILE__))) . '/resources/persons/' . $personfile['personfile_file']);
            }
            $this->PersonFile_model->delete($id);
        }
        redirect('Personfiles/all/' . $person);
    }

}
