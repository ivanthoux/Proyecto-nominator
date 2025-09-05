<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Connection APIs for Veraz
 *
 * @author harlyman
 */
class Veraz
{

    private $veraz = null;

    /*
     * URL Veraz
     */
    protected $URL = 'https://online.org.veraz.com.ar/pls/consulta817/wserv';
    /*
     * Credentials
     */
    protected $MATRIX = 'C26677';
    protected $USER = 'XML';
    protected $PASSWORD = '62298023324571851306207005623';
    /*
     * The medium by which the query is sent (by default HTML)
     */
    protected $MEANS = 'HTML';
    /*
     * Indicates the format with which you want to receive the report, the possible values are T 
     * (the report in text) or H (the report is sent encoded in HTML)
     */
    protected $REPORT_FORMAT = 'T';
    /*
     * Experto: If you only want to obtain the result of Expert
     * RISC:Experto: If you want to obtain the report RISC + the Experto
     */
    protected $PRODUCT = 'Experto';
    /*
     * The sector corresponding to the product to be consulted must be configured 
     * (detailed in the mail next to the sending of authentication credentials)
     */
    protected $SECTOR = '01';
    /*
     * Identify the cost center for billing (default is 0)
     */
    protected $BRANCH = '0';
    /*
     * It is a free field of 32 characters, can be used for example if you want to identify the 
     * consult with a number of file or request of the client to evaluate. Can not travel empty
     */
    protected $CLIENT = 'CONSULTA DE ESTADO';

    public function __construct()
    {
    }

    private function _getXML($dni, $name, $sex)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mensaje></mensaje>');
        $xml->addChild('identificador');
        $xml->identificador->addChild('userlogon');
        $xml->identificador->userlogon->addChild('matriz', $this->MATRIX);
        $xml->identificador->userlogon->addChild('usuario', $this->USER);
        $xml->identificador->userlogon->addChild('password', $this->PASSWORD);
        $xml->identificador->addChild('medio', $this->MEANS);
        $xml->identificador->addChild('formatoInforme', $this->REPORT_FORMAT);
        $xml->identificador->addChild('reenvio');
        $xml->identificador->addChild('producto', $this->PRODUCT);
        $xml->identificador->addChild('lote');
        $xml->identificador->lote->addChild('sectorVeraz', $this->SECTOR);
        $xml->identificador->lote->addChild('sucursalVeraz', $this->BRANCH);
        $xml->identificador->lote->addChild('cliente', $this->CLIENT);
        $dt = new DateTime('NOW');
        $xml->identificador->lote->addChild('fechaHora', $dt->format('Y-m-d\TH:i:s.v'));
        $xml->addChild('consulta');
        $xml->consulta->addChild('integrantes', 1);
        $xml->consulta->addChild('integrante');
        $xml->consulta->integrante->addAttribute('valor', 1);
        $xml->consulta->integrante->addChild('nombre', $name);
        $xml->consulta->integrante->addChild('sexo', $sex);
        $xml->consulta->integrante->addChild('documento', $dni);
        return $xml->saveXML();
    }

    private function _loadInstance($dni, $name, $sex)
    {
        //get XML format by standart VERAZ
        $postData = 'par_xml=' . $this->_getXML($dni, $name, $sex); //$this->input->post('name'), $this->input->post('dni'), $this->input->post('sex'));
        // init conection POST
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($ch, CURLOPT_POST, count((array) $postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_errno > 0) {
            $this->veraz = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mensaje></mensaje>');
            $this->veraz->addChild("estado");
            $this->veraz->estado->addChild('codigoError', '-' . $curl_errno);
            $this->veraz->estado->addChild('mensajeError', $curl_error);
        } else if (strpos($output, '<?xml version="1.0"') === FALSE) {
            $this->veraz = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mensaje></mensaje>');
            $this->veraz->addChild("estado");
            $this->veraz->estado->addChild('codigoError', '-1');
            $this->veraz->estado->addChild('mensajeError', 'Sistema no disponible');
        } else {
            $this->veraz = new SimpleXMLElement($output);
        }
    }

    private function _getValueVariable($variables, $name)
    {
        foreach ($variables as $variable) {
            if ($variable->nombre->__toString() === $name) {
                return $variable->valor->__toString();
            }
        }
    }

    private function _getInfo($colection, $exclud = array())
    {
        $info = '';
        foreach ($colection as $node) {
            if (!in_array($node->nombre->__toString(), $exclud)) {
                $info .= (empty($info) ? "" : "<br>") . "<i>" . $node->nombre->__toString() . ": " . "</i>" . $node->valor->__toString();
            }
        }
        return $info;
    }

    /**
     * Return values score returned by the Veraz service, if have error returned message
     * 
     * @param type $dni Document of the person to be evaluated
     * @param type $name Full name of the person to be evaluated, last name and first name
     * @param type $sex Sex of the person to be evaluated, female F, male M
     * @return String
     */
    public function getQualification($dni = '', $name = '', $sex = '')
    {
        if ($this->veraz == NULL) {
            $this->_loadInstance($dni, $name, $sex);
        }

        if (strpos($this->veraz->estado->codigoError, '-') === false) {
            $name = $this->_getValueVariable($this->veraz->respuesta->integrante->variables->variable, 'explicacion_validacion');
            $score = $this->_getValueVariable($this->veraz->respuesta->integrante->variables->variable, 'score_veraz');
            // die('<pre>Veraz: ' . print_r([$score, gettype($score)], true));
            if (strpos($name, 'VALIDADO') !== FALSE || !empty($score)) {
                return $this->_getValueVariable($this->veraz->respuesta->integrante->variables->variable, 'score_veraz');
            }
            return '-999';
        } else {
            return $this->veraz->estado->codigoError;
        }
    }

    /**
     * Return aditional informacition returned by the Veraz service, if have error returned message
     * 
     * @param type $dni Document of the person to be evaluated
     * @param type $name Full name of the person to be evaluated, last name and first name
     * @param type $sex Sex of the person to be evaluated, female F, male M
     * @return String
     */
    public function getInformation($dni = '', $name = '', $sex = '')
    {
        if ($this->veraz == NULL) {
            $this->_loadInstance($dni, $name, $sex);
        }
        if (strpos($this->veraz->estado->codigoError, '-') === false) {
            $name = $this->_getValueVariable($this->veraz->respuesta->integrante->variables->variable, 'explicacion_validacion');
            if (strpos($name, 'VALIDADO') !== FALSE) {
                return $this->_getInfo($this->veraz->respuesta->integrante->variables->variable, array('explicacion_validacion', 'score_veraz'));
            }
            return $name;
        } else {
            return $this->veraz->estado->mensajeError;
        }
    }

    public function getXML($dni = '', $name = '', $sex = '')
    {
        if ($this->veraz == null) {
            $this->_loadInstance($dni, $name, $sex);
        }

        return $this->veraz->asXML();
    }
}
