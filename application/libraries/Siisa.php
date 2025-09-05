<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Connection APIs for Siisa
 *
 * @author harlyman
 */
class Siisa
{

    private $siisa = null;

    /*
     * URL Siisa
     */
    protected $URL = 'http://www.siisa.com.ar/soap/CustomerInfo/wsdl/';
    /*
     * Reserved for SIISA. They must send a 1
     */
    protected $IDCLIENTE = 1;
    /*
     * Credentials
     */
    protected $IDENTIDAD = 362;
    protected $IDPIN = 3336;
    protected $CLAVE = 3337;

    public function __construct()
    {
    }

    private function _getParameters($dni, $name, $sex)
    {
        return array(array(
            'IdCliente' => $this->IDCLIENTE,
            'IdEntidad' => $this->IDENTIDAD,
            'IdPin' => $this->IDPIN,
            'Clave' => $this->CLAVE,
            'NroDoc' => $dni,
            'CUIL' => 0,
            'ApellidoNombre' => $name,
            'Sexo' => $sex,
            'Sueldo' => 0,
            'CUIT' => 0,
            'FechaIngreso' => date('Y-m-d\TH:i:s', time()),
            'TipoOperacionId' => 0,
            'CantCuotas' => 0,
            'MontoCuota' => 0,
            'MontoTotal' => 0,
            'IP' => '',
            'Reserv' => '',
        ));
    }

    private function _loadInstance($dni, $name, $sex)
    {
        try {
            //init SOAP client
            $client = new SoapClient($this->URL, array('connection_timeout' => 5, 'exceptions' => true));
            //the GetSiisa function is called with parameters.
            //            echo '<pre>Client: '.print_r($client, true).'</pre>';
            $output = $client->__soapCall("GetSiisa", $this->_getParameters($dni, $name, $sex));
            //            echo '<pre>Code: '.print_r($output, true).'</pre>';
            $xml = str_ireplace(['encoding="windows-1252"'], 'encoding="UTF-8"', $output->GetSiisaResult);
            $this->siisa = new SimpleXMLElement($xml);
            //            echo '<pre>Siisa: '.print_r($this->siisa, true).'</pre>';
            if (!isset($this->siisa->DatosSalida->Scorings) && !isset($this->siisa->DatosSalida->Scoring)) {
                $this->siisa = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mensaje></mensaje>');
                $this->siisa->addChild('DatosSalida');
                $this->siisa->DatosSalida->addChild('CodigoError', '-1');
                $this->siisa->DatosSalida->addChild('MensajeError', 'Error en el Sistema Siisa');
            }
        } catch (SoapFault $e) {
            $this->siisa = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mensaje></mensaje>');
            $this->siisa->addChild('DatosSalida');
            $this->siisa->DatosSalida->addChild('CodigoError', '-2');
            $this->siisa->DatosSalida->addChild('MensajeError', $e->getMessage());
        } catch (Exception $e) {
            $this->siisa = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><mensaje></mensaje>');
            $this->siisa->addChild('DatosSalida');
            $this->siisa->DatosSalida->addChild('CodigoError', '-3');
            $this->siisa->DatosSalida->addChild('MensajeError', $e->getMessage());
        }
    }

    private function _getInfo($colection, $exclud = array())
    {
        $info = '';
        foreach ($colection as $object) {
            $data = (empty($info) ? '' : '<hr>') . '';
            foreach ($object->children() as $child) {
                if (!in_array($child->getName(), $exclud)) {
                    $data .= ($data === '<hr>' ? "" : "<br>") . '<i>' . $child->getName() . ': </i>' . $child->__toString();
                }
            }
            $info .= $data;
        }
        return $info;
    }

    private function _getJson($colection, $exclud = array())
    {
        $info = [];
        foreach ($colection as $object) {
            // $data = (empty($info) ? '' : '<hr>') . '';
            foreach ($object->children() as $child) {
                if (!in_array($child->getName(), $exclud)) {
                    // $data .= ($data === '<hr>' ? "" : "<br>") . '<i>' . $child->getName() . ': </i>' . $child->__toString();
                    $info[$child->getName()] = $child->__toString();
                }
            }
            // $info .= $data;
        }
        return $info;
    }

    /**
     * Return information of Person returned by the Siisa service
     * 
     * @param type $dni Document of the person to be evaluated
     * @param type $name Full name of the person to be evaluated, last name and first name
     * @param type $sex Sex of the person to be evaluated, female F, male M
     * @return Array
     */
    public function getPerson($dni = '', $name = '', $sex = '')
    {
        if ($this->siisa == null) {
            $this->_loadInstance($dni, $name, $sex);
        }
        //        echo '<pre>Veraz: '.print_r($this->siisa, true);
        if ($this->siisa->DatosSalida->CodigoError->__toString() === '') {
            return $this->_getJson($this->siisa->DatosSalida->Personas->Persona, []);
        } else {
            return $this->siisa->DatosSalida->CodigoError->__toString();
        }
    }

    /**
     * Return value of score returned by the Siisa service
     * 
     * @param type $dni Document of the person to be evaluated
     * @param type $name Full name of the person to be evaluated, last name and first name
     * @param type $sex Sex of the person to be evaluated, female F, male M
     * @return String
     */
    public function getQualification($dni = '', $name = '', $sex = '')
    {
        if ($this->siisa == null) {
            $this->_loadInstance($dni, $name, $sex);
        } else {
            if ($this->siisa->DatosSalida->CodigoError->__toString() === '') {
                $name = $this->siisa->DatosEntrada->ApellidoNombre->__toString();
            }
        }
        if ($this->siisa->DatosSalida->CodigoError->__toString() === '') {
            $person = $this->getPerson($dni, $name, $sex);
            if (gettype(strpos(strtoupper($name), strtoupper($person['Apellido']))) == 'integer') {
                return $this->siisa->DatosSalida->Scorings->Scoring->valor->__toString();
            } else {
                return '-999';
            }
        } else {
            return $this->siisa->DatosSalida->CodigoError->__toString();
        }
    }

    /**
     * Return value of aditional information returned by the Siisa service
     * 
     * @param type $dni Document of the person to be evaluated
     * @param type $name Full name of the person to be evaluated, last name and first name
     * @param type $sex Sex of the person to be evaluated, female F, male M
     * @return String
     */
    public function getInformation($dni = '', $name = '', $sex = '')
    {
        if ($this->siisa == null) {
            $this->_loadInstance($dni, $name, $sex);
        } else {
            if ($this->siisa->DatosSalida->CodigoError->__toString() === '') {
                $name = $this->siisa->DatosEntrada->ApellidoNombre->__toString();
            }
        }
        if ($this->siisa->DatosSalida->CodigoError->__toString() === '') {
            $person = $this->getPerson($dni, $name, $sex);
            // die('<pre>'.print_r([strtoupper($name), strtoupper($person['Apellido']), gettype(strpos(strtoupper($name), strtoupper($person['Apellido'])))], true));
            if (gettype(strpos(strtoupper($name), strtoupper($person['Apellido']))) == 'integer') {
                // die('<pre>'.print_r('??'));
                $info = '<b>Morosidades BCRA:</b>'
                    . $this->_getInfo($this->siisa->DatosSalida->MorosidadesBCRA->MorosidadBCRA, array('Origen', 'IDSituacion', 'IDEntidadBCRA'))
                    . '<br><br><b>Morosidades BCRA Historicas:</b>'
                    . $this->_getInfo($this->siisa->DatosSalida->MorosidadesBCRAHistoricas->MorosidadBCRAHistorica, array('IDEntidadBCRA'))
                    . '<br><br><b>Morosidades:</b>'
                    . $this->_getInfo($this->siisa->DatosSalida->Morosidades->Morosidad, array('IdCategoria'))
                    . '<br><br><b>Morosidades Historicas:</b>'
                    . $this->_getInfo($this->siisa->DatosSalida->MorosidadesHistoricas->MorosidadHistorica, array('IdCategoria'))
                    . '<br><br><b>Familiares:</b>'
                    . $this->_getInfo($this->siisa->DatosSalida->Familiares->Familiar, array('IdTipoDoc'));
                return $info;
            } else {
                return 'El D.N.I no concuerda con la persona buscada';
            }
        } else {
            return $this->siisa->DatosSalida->MensajeError->__toString();
        }
    }

    public function getXML($dni = '', $name = '', $sex = '')
    {
        if ($this->siisa == null) {
            $this->_loadInstance($dni, $name, $sex);
        }

        return $this->siisa->asXML();
    }

    public function getHuman($xml)
    {
        $this->siisa = new SimpleXMLElement($xml);
        $info = '<b>Personas:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Personas->Persona, array())
            . '<br><br><b>Domicilios:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Domicilios->Domicilio, array())
            . '<br><br><b>Telefonos:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Telefonos->Telefono, array())
            . '<br><br><b>Morosidades BCRA:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->MorosidadesBCRA->MorosidadBCRA, array('Origen', 'IDSituacion', 'IDEntidadBCRA'))
            . '<br><br><b>Morosidades BCRA Historicas:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->MorosidadesBCRAHistoricas->MorosidadBCRAHistorica, array('IDEntidadBCRA'))
            . '<br><br><b>Morosidades:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Morosidades->Morosidad, array('IdCategoria'))
            . '<br><br><b>Morosidades Historicas:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->MorosidadesHistoricas->MorosidadHistorica, array('IdCategoria'))
            . '<br><br><b>Familiares:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Familiares->Familiar, array('IdTipoDoc'))
            . '<br><br><b>Laborales:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Laborales->Laboral, array())
            . '<br><br><b>Autonomos:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->Autonomos->Autonomo, array())
            . '<br><br><b>Variables Sumarizadas:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->VariablesSumarizadas->VariableSumarizada, array())
            . '<br><br><b>Modelos SES:</b>'
            . $this->_getInfo($this->siisa->DatosSalida->modelosSES->modeloSES, array());
        return $info;
    }
}
