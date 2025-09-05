<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . "/third_party/Afip_sdk.php";

/**
 * Connection APIs for AFIP
 *
 * @author harlyman
 */
class Afip extends Afip_sdk
{

    /**
     * AFIP
     */
    /*
     * The CUIT to use
     */
    protected $_CUIT = '20320627215';
    /*
     * Environment
     */
    protected $_PRODUCTION = false;
    /*
     * The passphrase (if any) to sign
     */
    protected $_PASSPHRASE = 'torresrodrigoe';
    /*
     * File name for the X.509 certificate in PEM format
     */
    protected $_CERT = '';
    /*
     * File name for the private key correspoding to CERT (PEM)
     */
    protected $_PRIVATEKEY = '';
    /*
     * Afip resources folder
     */
    protected $_RES_FOLDER = '';
    /*
     * Afip ta folder
     */
    protected $_TA_FOLDER = '';

    public function __construct()
    {
        parent::__construct($this->_getParametersAFIP());
    }

    private function _getParametersAFIP()
    {
        $this->_CUIT = (ENVIRONMENT == 'production' ? '30715679198' : '20320627215');
        $this->_PRODUCTION = (ENVIRONMENT == 'production' ? true : false);
        $this->_CERT = (ENVIRONMENT == 'production' ? 'certs_prod/770472303819637a.crt' : '') . $this->_CERT;
        $this->_PRIVATEKEY = (ENVIRONMENT == 'production' ? 'certs_prod/nominator.key' : '') . $this->_PRIVATEKEY;
        $params = array(
            'CUIT' => $this->_CUIT,
            'passphrase' => $this->_PASSPHRASE,
            'production' => $this->_PRODUCTION
        );
        if (!empty($this->_CERT)) {
            $params['cert'] = $this->_CERT;
        }
        if (!empty($this->_PRIVATEKEY)) {
            $params['key'] = $this->_PRIVATEKEY;
        }
        if (!empty($this->_RES_FOLDER)) {
            $params['res_folder'] = $this->_RES_FOLDER;
        }
        if (!empty($this->_TA_FOLDER)) {
            $params['ta_folder'] = $this->_TA_FOLDER;
        }
        // die("<pre>" . print_r(ENVIRONMENT, true) . "</pre>");
        // die("<pre>" . print_r(json_encode($params), true) . "</pre>");
        return $params;
    }

    public function checkServer()
    {
        // get status server
        $afip = $this->ElectronicBilling->ExecuteRequest('FEDummy');
        if ($afip->AppServer !== "OK" || $afip->DbServer !== "OK" || $afip->AuthServer !== "OK") {
            throw new Exception("El sevidor de AIFP no se encuentra funcional");
        }
    }

    public function getAliQuot()
    {
        return $this->ElectronicBilling->GetAliquotTypes();
    }

    public function getDocType()
    {
        return $this->ElectronicBilling->GetDocumentTypes();;
    }

    public function getCbtTipo()
    {
        return $this->ElectronicBilling->GetVoucherTypes();
    }

    /**
     * create a new AFIP voucher
     * 
     * @return \AFIPResponse
     */
    public function getNewVoucher($data)
    {
        $data = array_merge($data, [
            'CantReg' => 1, // Number of vouchers to register
            'PtoVta' => (ENVIRONMENT == 'production' ? 3 : 1), // Point of sale
            'Concepto' => 1, // Voucher Concept: (1) Products, (2) Services, (3) Products and Services
            'MonId' => 'PES', // Type of currency used in the voucher (see available types) ('PES' for Argentine pesos)
            'MonCotiz' => 1, // Quotation of the currency used (1 for Argentine pesos)
        ]);

        // die("<pre>" . print_r($data, true) . "</pre>");s
        $CbteNro = $this->ElectronicBilling->ExecuteRequest('FECompUltimoAutorizado', $data);
        $nro = $CbteNro->CbteNro + 1;
        $data = array_merge($data, [
            'CbteDesde' => $nro, // Number of voucher or number of the first voucher if more than one
            'CbteHasta' => $nro, // Number of voucher or number of the last voucher if more than one
        ]);

        // die("<pre>" . print_r($data, true) . "</pre>");

        $voucher = $this->ElectronicBilling->CreateVoucher($data);
        return ['data' => $data, 'voucher' => $voucher];
        // return $this->ElectronicBilling->GetVoucherInfo($nro, 1, 6);
    }
}
