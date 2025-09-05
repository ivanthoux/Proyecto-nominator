<?php

class Vouchers extends ManagerController
{
    protected $pathindex = '/vouchers/all';
    protected $viewpath = 'manager/vouchers/voucher';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect($this->pathindex, 'refresh');
    }

    public function all()
    {
        $viewpath = $this->viewpath . '_list';
        $this->datatables_assets();
        $this->data['activesidebar'] = 'vouchers';
        $this->data['user'] = $this->session->userdata('user_id');
        $this->javascript[] = $this->load->view($viewpath . '_js', $this->data, true);
        $this->_render($viewpath);
    }

    public function form($_id = false)
    {
        $this->load->model(array('Vouchers_model'));
        $post = $this->input->post();
        if (!empty($post)) { //saving user received
            $this->Vouchers_model->save($post);
            redirect($this->pathindex, 'refresh');
        } else { //render new user form or edit user
            if (!empty($_id)) {
                $this->data['edit'] = $this->Vouchers_model->get_by_id($_id);
            }
        }
        $this->data['activesidebar'] = 'closings';
        $this->css[] = array('url' => 'libs/bootstrap-colorpicker.min.css');
        $this->javascript[] = ['url' => base_url() . MAN_JS . 'libs/bootstrap-colorpicker.min.js'];
        $this->javascript[] = $this->load->view('manager/closings/closing_form_js', $this->data, true);
        $this->_render($this->viewpath . '_form');
    }

    public function datatables()
    {
        $this->load->model('Vouchers_model');
        echo json_encode($this->Vouchers_model->datatables_ajax_list());
    }

    public function ivaBook()
    {
        $this->load->model('Vouchers_model');
        $this->load->library(['zip', 'excel']);
        $this->load->helper(['file']);
        $vouchers_model = new Vouchers_model();
        $get = $this->input->get();
        $dates = explode('/', $get['period']);
        $start = date('Ymd', strtotime(trim($dates[0])));
        $end = date('Ymd', strtotime(trim($dates[1])));

        $vouchers = $vouchers_model->getByPeriod($get['period']);
        $this->_headerAfip($vouchers, $end);
        $this->_detailAfip($vouchers, $end);
        $this->_citiventasAlicuota($vouchers);
        $this->_citiventasCbtes($vouchers);

        $ivabook = $vouchers_model->getIvaBook($get['period']);
        $this->_ivaBook($ivabook, $start, $end);

        $this->zip->download('CompraVenta_' . $start . '_' . $end . '.zip');
    }

    private function _headerAfip($vouchers, $end)
    {
        $file = "Cabecera_" . $end . ".txt";
        $line = '';
        $voucher_total = 0;
        $voucher_taxed = 0;
        $voucher_iva = 0;
        foreach ($vouchers as $voucher) {
            $line .= '1'
                //02
                . date('Ymd', strtotime($voucher['voucher_date']))
                //03
                . str_pad($voucher['voucher_type'], 2, '0', STR_PAD_LEFT)
                //04
                . str_pad('', 1, ' ')
                //05
                . str_pad($voucher['voucher_sellpoint'], 4, '0', STR_PAD_LEFT)
                //06
                . str_pad($voucher['voucher_number'], 8, '0', STR_PAD_LEFT)
                //07
                . str_pad($voucher['voucher_number'], 8, '0', STR_PAD_LEFT)
                //08
                . '001'
                //09
                . str_pad($voucher['voucher_doctype'], 2, '0', STR_PAD_LEFT)
                //10
                . (in_array($voucher['voucher_doctype'], ['99', '96']) ? ($voucher['voucher_doctype'] == '96' ? str_pad($voucher['client_doc'], 11, '0', STR_PAD_LEFT) : str_pad(0, 11, '0', STR_PAD_LEFT)) : str_pad($voucher['client_cuil'], 11, '0', STR_PAD_LEFT))
                //11
                . str_pad('CONSUMIDOR FINAL', 30, ' ')
                //12
                . str_pad(round(($voucher['voucher_total'] * 100), 0), 15, '0', STR_PAD_LEFT)
                //13
                . str_pad(0, 15, '0')
                //14
                . str_pad(round(($voucher['voucher_taxed'] * 100), 0), 15, '0', STR_PAD_LEFT)
                //15
                . str_pad(round(($voucher['voucher_iva'] * 100), 0), 15, '0', STR_PAD_LEFT)
                //16
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //17
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //18
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //19
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //20
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //21
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //22
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                //23
                . str_pad($voucher['vouchertax_type'], 2, '0', STR_PAD_LEFT)
                //24
                . 'PES'
                //25
                . '0001000000'
                //26
                . '1'
                //27
                . str_pad('', 1, ' ')
                //28
                . str_pad($voucher['voucher_cae'], 14, '0', STR_PAD_LEFT)
                //29
                . date('Ymd', strtotime($voucher['voucher_caevto']))
                //30
                . str_pad('', 8, ' ')
                //31
                . "0\n";
            $voucher_total += round($voucher['voucher_total'], 2);
            $voucher_taxed += round($voucher['voucher_taxed'], 2);
            $voucher_iva += round($voucher['voucher_iva'], 2);
        }
        $line .= '2'
            //02
            . substr($end, 0, -2)
            //03
            . str_pad('', 13, ' ')
            //04
            . str_pad(count($vouchers), 8, '0', STR_PAD_LEFT)
            //05
            . str_pad('', 17, ' ')
            //06
            . str_pad($this->session->userdata('settings')['cuit'], 11, '0', STR_PAD_LEFT)
            //07
            . str_pad('', 22, ' ')
            //08
            . str_pad(round(($voucher_total * 100), 0), 15, '0', STR_PAD_LEFT)
            //09
            . str_pad(0, 15, '0')
            //10
            . str_pad(round(($voucher_taxed * 100), 0), 15, '0', STR_PAD_LEFT)
            //11
            . str_pad(round(($voucher_iva * 100), 0), 15, '0', STR_PAD_LEFT)
            //12
            . str_pad(0, 15, '0', STR_PAD_LEFT)
            //13
            . str_pad(0, 15, '0', STR_PAD_LEFT)
            //14
            . str_pad(0, 15, '0', STR_PAD_LEFT)
            //15
            . str_pad(0, 15, '0', STR_PAD_LEFT)
            //16
            . str_pad(0, 15, '0', STR_PAD_LEFT)
            //17
            . str_pad(0, 15, '0', STR_PAD_LEFT)
            //18
            . str_pad('', 62, ' ')
            //19
            . "0\n";

            $this->zip->add_data($file, $line);
    }

    private function _detailAfip($vouchers, $end)
    {
        $file = "Detalle_" . $end . ".txt";
        $line = '';
        foreach ($vouchers as $voucher) {
            $line .= str_pad($voucher['voucher_type'], 2, '0', STR_PAD_LEFT)
                //02
                . str_pad('', 1, ' ')
                //03
                . date('Ymd', strtotime($voucher['voucher_date']))
                //04
                . str_pad($voucher['voucher_sellpoint'], 4, '0', STR_PAD_LEFT)
                //05
                . str_pad($voucher['voucher_number'], 8, '0', STR_PAD_LEFT)
                //06
                . str_pad($voucher['voucher_number'], 8, '0', STR_PAD_LEFT)
                //07
                . '000000100000'
                //08
                . '07'
                //09
                . str_pad(round(($voucher['voucher_total'] * 100), 0), 15, '0', STR_PAD_LEFT)
                //10
                . str_pad(0, 16, '0')
                //11
                . str_pad(0, 16, '0')
                //12
                . str_pad(round(($voucher['voucher_total'] * 100), 0), 15, '0', STR_PAD_LEFT)
                //13
                . '02100'
                //14
                . 'G'
                //15
                . str_pad('', 1, ' ')
                //16
                . str_pad('INTERESES C4', 75, ' ')
                . "\n";
        }
        $this->zip->add_data($file, $line);
    }

    private function _citiventasAlicuota($vouchers)
    {
        $file = "REGINFO_CV_VENTAS_ALICUOTA.txt";
        $line = '';
        foreach ($vouchers as $voucher) {
            $line .= str_pad($voucher['voucher_type'], 3, '0', STR_PAD_LEFT)
                . str_pad($voucher['voucher_sellpoint'], 5, '0', STR_PAD_LEFT)
                . str_pad($voucher['voucher_number'], 20, '0', STR_PAD_LEFT)
                . str_pad(round(($voucher['voucher_taxed'] * 100), 0), 20, '0', STR_PAD_LEFT)
                . str_pad($voucher['vouchertax_type'], 5, '0', STR_PAD_LEFT)
                . str_pad(round(($voucher['voucher_iva'] * 100), 0), 15, '0', STR_PAD_LEFT)
                . "\n";
        }
        $this->zip->add_data($file, $line);
    }

    private function _citiventasCbtes($vouchers)
    {
        $file = "REGINFO_CV_VENTAS_CBTES.txt";
        $line = '';
        foreach ($vouchers as $voucher) {
            $line .= date('Ymd', strtotime($voucher['voucher_date']))
                . str_pad($voucher['voucher_type'], 3, '0', STR_PAD_LEFT)
                . str_pad($voucher['voucher_sellpoint'], 5, '0', STR_PAD_LEFT)
                . str_pad($voucher['voucher_number'], 20, '0', STR_PAD_LEFT)
                . str_pad($voucher['voucher_number'], 20, '0', STR_PAD_LEFT)
                . str_pad($voucher['voucher_doctype'], 2, '0', STR_PAD_LEFT)
                . (in_array($voucher['voucher_doctype'], ['99', '96']) ? ($voucher['voucher_doctype'] == '96' ? str_pad($voucher['client_doc'], 20, '0', STR_PAD_LEFT) : str_pad(0, 20, '0', STR_PAD_LEFT)) : str_pad($voucher['client_cuil'], 20, '0', STR_PAD_LEFT))
                . str_pad(substr(strtoupper($voucher['client_lastname'] . ' ' . $voucher['client_firstname']), 0, 30), 30, ' ')
                . str_pad(round(($voucher['voucher_total'] * 100), 0), 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . 'PES'
                . '0001000000'
                . '1'
                . '0'
                . str_pad(0, 15, '0', STR_PAD_LEFT)
                . date('Ymd', strtotime($voucher['voucher_date']))
                . "\n";
        }
        $this->zip->add_data($file, $line);
    }

    private function _ivaBook($ivabook, $start, $end)
    {
        $path = dirname(dirname(dirname(__FILE__))) . "/resources/vouchers/";

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Fecha Factura');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Fecha Cobro');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Oficina');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Crédito');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Comprobante');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Número');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Cliente');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'CUIL/CUIT');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Neto');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'EXENTO / NO GRAVADO / NO ALCANZADO');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Alicuota');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'IVA');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Total');
        // set Row
        $rowCount = 2;
        foreach ($ivabook as $list) {
            // $this->dd($list);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $list['voucher_date']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $list['pay_date']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list['office_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list['clientpack_id']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list['voucher_type']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $list['voucher']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $list['client']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $list['client_cuil']);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $list['voucher_taxed']);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $list['voucher_nottaxed']);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $list['iva']);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $list['vouchertax_value']);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $list['voucher_total']);
            $rowCount++;
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

        $objWriter->save($path . 'LibroIva_' . $start . '_' . $end . '.xlsx');
        $this->zip->read_file($path . 'LibroIva_' . $start . '_' . $end . '.xlsx');
        unlink($path . 'LibroIva_' . $start . '_' . $end . '.xlsx');
    }

    public function consulta()
    {
        $this->load->library(['Afip']);

        $afip = new Afip();

        try {
            $afip->checkServer();
            $this->dd($afip->getDocType());
        } catch (Exception $ex) {
            echo json_encode(['status' => 'fail', 'msg' => $ex->getMessage()]);
        }
    }
}
