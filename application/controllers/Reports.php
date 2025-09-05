<?php

class Reports extends ManagerController
{
    protected $pathindex = '/reports/all';
    protected $viewpath = 'manager/reports/report';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect($this->pathindex, 'refresh');
    }

    private function _get_packs()
    {
        $pack_model = new Pack_model();

        $options = array();
        $temp = $pack_model->get_all(true, true);
        foreach ($temp as $op) {
            // $this->dd(($op));
            $options[] = array('title' => $op['pack_name'], 'value' => $op['pack_id']);
        }
        return $options;
    }

    public function all()
    {
        $this->load->model(['Pack_model']);

        $viewpath = $this->viewpath . '_list';
        $this->data['activesidebar'] = 'reports';

        $this->data['packs'] = $this->_get_packs();
        
        $this->css[] = ['url' => base_url() . MAN_CSS . 'libs/bootstrap-datepicker3.min.css'];
        $this->css[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css'];
        $this->javascript[] = ['url' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js'];
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        
        $this->_render($viewpath);
    }

    public function excel($report)
    {
        $this->load->model([
            'ClientPack_model',
            'ClientPeriod_model',
            'Office_model',
            'Pack_model',
            'Payment_model',
            'Office_model',
            'User_model'
        ]);
        $this->load->library(['excel']);

        switch ($report) {
            // case 'vintage':
            //     $this->_vintageReport();
            //     break;
            // case 'reca':
            //     $this->_recaReport();
            //     break;
            // case 'ctas_cobradas':
            //     $this->_ctasCobradasReport();
            //     break;
            // case 'deuda_acumulada':
            //     $this->_dudaAcumuladaReport();
            //     break;
            // case 'clientes_mora':
            //     $this->_clientesMoraReport();
            //     break;
        }
    }
}
