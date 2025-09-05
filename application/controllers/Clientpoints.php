<?php

class Clientpoints extends ManagerController
{

    protected $pathindex = '/clients';
    protected $viewpath = 'manager/clients/points/clientpoint';
    private $last;

    public function __construct()
    {
        parent::__construct();
    }

    private function _setVeraz(&$points, $empty)
    {
        $point = $empty ? '0' : $this->veraz->getQualification(
            $this->data['client']['client_doc'],
            $this->data['client']['client_lastname'] . ' ' . $this->data['client']['client_firstname'],
            $this->data['client']['client_sex']
        );
        $points['clientpoint_verazpoint'] = $point != null ? $point : '0';

        $info = $empty ? 'Sin Informaci&oacute;n' : $this->veraz->getInformation();
        $points['clientpoint_verazinfo'] = $info != null ? $info : 'Error en API';

        $xml = $this->veraz->getXML();
        $points['clientpoint_verazxml'] = $xml;

        if (!$empty && strpos($point, "0") === false) {
            $this->load->model('Point_model');
            $this->Point_model->save(array(
                'point_type' => 1,
                'point_client' => $points['clientpoint_client'],
                'point_user' => $this->session->userdata('user_id'),
                'point_xml' => $xml
            ));
        }
    }

    private function _setSiisa(&$points, $empty)
    {
        $point = $empty ? '0' : $this->siisa->getQualification(
            $this->data['client']['client_doc'],
            $this->data['client']['client_lastname'] . ' ' . $this->data['client']['client_firstname'],
            $this->data['client']['client_sex']
        );
        $points['clientpoint_siisapoint'] = $point != null ? $point : '0';

        $info = $empty ? 'Sin Informaci&oacute;n' : $this->siisa->getInformation();
        $points['clientpoint_siisainfo'] = $info != null ? $info : 'Error en API';

        $xml = $this->siisa->getXML();
        $points['clientpoint_siisaxml'] = $xml;

        if (!$empty && strpos($point, "0") === false) {
            $this->load->model('Point_model');
            $this->Point_model->save(array(
                'point_type' => 2,
                'point_client' => $points['clientpoint_client'],
                'point_user' => $this->session->userdata('user_id'),
                'point_xml' => $xml
            ));
        }
    }

    private function _setData(&$points, $client, $empty, $veras_siisa = false)
    {
        $this->last = $empty ? '' : date('d/m/Y G:i:s');
        $points['clientpoint_client'] = $client;
        $points['clientpoint_updated'] = date('Y-m-d G:i:s');
        if (empty($veras_siisa) || $veras_siisa == 1) {
            $this->_setVeraz($points, $empty);
        }
        if (empty($veras_siisa) || $veras_siisa == 2) {
            $this->_setSiisa($points, $empty);
        }
    }

    private function _setDataIf(&$points, $client, $update, $updated, $veras_siisa)
    {
        $hour = $updated->diff(new DateTime(date('Y-m-d\TG:i:s')))->format('%h');
        $hours = intval($hour) + intval($updated->diff(new DateTime(date('Y-m-d\TG:i:s')))->format('%d')) * 24;
        if ($hours >= $this->ClientPoint_model->hours) {
            if ($update || ($this->ClientPoint_model->autoUpdate && $this->data['client']['client_active'])) {
                $this->_setData($points, $client, false, $veras_siisa);
                $this->ClientPoint_model->save($points);
                $this->logCreate($this->ClientPoint_model, $points, 'u');
            }
        } else {
            if ($update) {
                $this->_setData($points, $client, false, $veras_siisa);
                $this->ClientPoint_model->save($points);
                $this->logCreate($this->ClientPoint_model, $points, 'u');
            }
        }
    }

    public function index()
    {
        redirect($this->pathindex, 'refresh');
    }

    public function status($client = false)
    {
        if (empty($client)) {
            echo json_encode(['update' => false]);
        } else {
            $this->load->model('Client_model');
            $this->load->model('ClientPoint_model');
            $points = $this->ClientPoint_model->get_by_client($client);
            if ($points != null) {
                $log = $this->getLastLog($this->ClientPoint_model, $points[$this->ClientPoint_model->primary_key()]);
                $updated = (empty($log) ? new DateTime('1970-01-01T00:00:00') : new DateTime(date('Y-m-d\TG:i:s', strtotime($log['useraction_date']))));
                $hour = $updated->diff(new DateTime(date('Y-m-d\TG:i:s')))->format('%h');
                $hours = intval($hour) + intval($updated->diff(new DateTime(date('Y-m-d\TG:i:s')))->format('%d')) * 24;
                if ($hours >= $this->ClientPoint_model->hours) {
                    echo json_encode(['update' => $this->ClientPoint_model->autoUpdate]);
                } else {
                    echo json_encode(['update' => false]);
                }
            } else {
                echo json_encode(['update' => false]);
            }
        }
    }

    public function info($client = false)
    {
        $this->load->model('Client_model');
        $this->load->model('ClientPoint_model');
        $this->load->library('Veraz', null, 'veraz');
        $this->load->library('Siisa', null, 'siisa');

        $viewpath = $this->viewpath . '_info';
        $this->datatables_assets();
        $this->data['client_id'] = $client;
        if (!empty($client)) {
            $this->data['client'] = $this->Client_model->get_by_id($client);
            $points = $this->ClientPoint_model->get_by_client($client);
            if ($points != null) {
                $log = $this->getLastLog($this->ClientPoint_model, $points[$this->ClientPoint_model->primary_key()]);
                $this->last = (empty($log) ? date('d/m/Y G:i:s') : date('d/m/Y G:i:s', strtotime($log['useraction_date'])));
            } else {
                $this->_setData($points, $client, true);
            }
            $points['last'] = $this->last;
            $this->data['points'] = $points;
        } else {
            redirect($this->pathindex, 'refresh');
        }
        $this->data['activesidebar'] = 'clients';
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->_render($viewpath);
    }

    public function update($client = false, $update = false, $veras_siisa = false)
    {
        $this->load->model('Client_model');
        $this->load->model('ClientPoint_model');
        $this->load->library('Veraz', null, 'veraz');
        $this->load->library('Siisa', null, 'siisa');

        $this->datatables_assets();
        $this->data['client_id'] = $client;
        if (!empty($client)) {
            $this->data['client'] = $this->Client_model->get_by_id($client);
            $points = $this->ClientPoint_model->get_by_client($client);
            if ($points != null) {
                $log = $this->getLastLog($this->ClientPoint_model, $points[$this->ClientPoint_model->primary_key()]);
                $updated = (empty($log) ? new DateTime('1970-01-01T00:00:00') : new DateTime(date('Y-m-d\TG:i:s', strtotime($log['useraction_date']))));

                $this->last = (empty($log) ? date('d/m/Y G:i:s') : date('d/m/Y G:i:s', strtotime($log['useraction_date'])));
                $this->_setDataIf($points, $client, $update, $updated, $veras_siisa);
            } else {
                if ($update) {
                    $this->_setData($points, $client, false, $veras_siisa);
                    $points[$this->ClientPoint_model->primary_key()] = $this->ClientPoint_model->save($points);
                    $this->logCreate($this->ClientPoint_model, $points, 'c');
                } else {
                    $this->_setData($points, $client, true, $veras_siisa);
                }
            }
        }
        echo json_encode([]);
    }

    public function xmlVeraz($client = false)
    {
        $this->load->model(['Client_model', 'Point_model', 'ClientPoint_model']);
        $this->load->library('Veraz', null, 'veraz');

        if (!empty($client)) {
            $this->data['client'] = $this->Client_model->get_by_id($client);
            $points = $this->ClientPoint_model->get_by_client($client);
            if ($points != null) {
                $output = $points['clientpoint_verazxml'];
            } else {
                $output = $this->veraz->getXML(
                    $this->data['client']['client_doc'],
                    $this->data['client']['client_lastname'] . ' ' . $this->data['client']['client_firstname'],
                    $this->data['client']['client_sex']
                );

                $this->Point_model->save(array(
                    'point_type' => 1,
                    'point_client' => $this->data['client']['client_id'],
                    'point_user' => $this->session->userdata('user_id')
                ));
            }
            //        $this->dd($output);
            $this->output
                ->set_content_type('application/xhtml+xml')
                ->set_output($output);
        }
    }

    public function xmlSiisa($client = false, $human = false)
    {
        $this->load->model(['Client_model', 'Point_model', 'ClientPoint_model']);
        $this->load->library('Siisa', null, 'siisa');

        if (!empty($client)) {
            $this->data['client'] = $this->Client_model->get_by_id($client);
            $points = $this->ClientPoint_model->get_by_client($client);
            if ($points != null) {
                $output = $points['clientpoint_siisaxml'];
            } else {
                $output = $this->siisa->getXML(
                    $this->data['client']['client_doc'],
                    $this->data['client']['client_lastname'] . ' ' . $this->data['client']['client_firstname'],
                    $this->data['client']['client_sex']
                );

                $this->Point_model->save(array(
                    'point_type' => 2,
                    'point_client' => $this->data['client']['client_id'],
                    'point_user' => $this->session->userdata('user_id')
                ));
            }
            if (!$human)
                $this->output
                    ->set_content_type('application/xhtml+xml')
                    ->set_output($output);
            else {
                if ($output)
                    $output = $this->siisa->getHuman($output);
                echo "<h3>Datos SIISA</h3>" . $output;
            }
        }
    }
}
