<?php

namespace postoor\HCTLogistics;

use GUMP;

class EDIWebService
{
    protected $url;

    protected $company;

    protected $password;

    protected $maxRow;

    public function __construct(
        $company,
        $password,
        $url = 'https://hctrt.hct.com.tw/EDI_WebService2/Service1.asmx?wsdl',
        $maxRow = 5
    ) {
        $this->company = $company;

        $this->password = $password;

        $this->url = $url;

        $this->maxRow = $maxRow;

        if ($maxRow > 30 || $maxRow <= 0) {
            throw new \Exception('maxRow should between 1 to 30');
        }
    }

    /**
     * Upload Shipping info.
     *
     * @return array
     */
    public function uploadTransData(array $data, array &$errorMessages = [])
    {
        if (!$data) {
            throw new \Exception('Not Input Data');
        }

        $data = isset($data[0]) ? $data : [$data];

        if (count($data) > $this->maxRow) {
            throw new \Exception('Maximum limit exceeded: '.$this->maxRow.' row(s)');
        }

        foreach ($data as $info) {
            $validate = $this->validateTransData($info);

            if ($validate !== true) {
                $errorMessages = $validate;
                throw new \Exception('Validate Failed');
            }
        }

        $soap = new \SoapClient($this->url, ['soap_version' => SOAP_1_2]);
        $response = $soap->TransData_Json([
            'company' => $this->company,
            'password' => $this->password,
            'json' => json_encode($data),
        ]);

        if (!$response->TransData_JsonResult) {
            $errorMessages[] = 'Not Response';
            throw new \Exception('Not Response');
        }

        return json_decode($response->TransData_JsonResult, true);
    }

    /**
     * Validate Trans Data.
     *
     * @return mix bool/array
     */
    private function validateTransData(array $data)
    {
        return GUMP::is_valid($data, [
            'epino' => 'required|max_len,30',
            'ercsig' => 'required|max_len,40',
            'ertel1' => 'required|max_len,15',
            'ertel2' => 'max_len,15',
            'eraddr' => 'required|max_len,100',
            'ejamt' => 'required|integer|min_numeric,1',
            'eqamt' => 'required|integer|min_numeric,9',
            'esdate' => 'date,Ymd',
            'escsno' => 'max_len,11',
            'esstno' => 'max_len,4',
            'edelno' => 'max_len,10',
            'etcsig' => 'max_len,40',
            'ettel1' => 'max_len,15',
            'ettel2' => 'max_len,15',
            'etaddr' => 'max_len,100',
            'eddate' => 'date,Ymd',
            'eqmny' => 'integer|min_numeric,0',
            'eprdct' => 'contains_list,11,21,31',
            'emark' => 'max_len,100',
            'eprdcl2' => 'contains_list,001,003,008',
        ]);
    }

    /**
     * Update Shipping Info.
     *
     * @return array
     */
    public function updateData(array $data, array &$errorMessages = [])
    {
        if (!$data) {
            throw new \Exception('Not Input Data');
        }

        $data = isset($data[0]) ? $data : [$data];

        if (count($data) > $this->maxRow) {
            throw new \Exception('Maximum limit exceeded: '.$this->maxRow.' row(s)');
        }

        foreach ($data as $info) {
            $validate = $this->validateUpdateData($info);

            if ($validate !== true) {
                $errorMessages = $validate;
                throw new \Exception('Validate Failed');
            }
        }

        $soap = new \SoapClient($this->url, ['soap_version' => SOAP_1_2]);
        $response = $soap->UpdData_Json([
            'company' => $this->company,
            'password' => $this->password,
            'json' => json_encode($data),
        ]);

        if (!$response->UpdData_JsonResult) {
            $errorMessages[] = 'Not Response';
            throw new \Exception('Not Response');
        }

        return json_decode($response->UpdData_JsonResult, true);
    }

    /**
     * Validate Update Data.
     *
     * @return min bool/array
     */
    public function validateUpdateData(array $data)
    {
        return GUMP::is_valid($data, [
            'epino' => 'required|max_len,30',
            'edelno' => 'required|max_len,10',
            'eqamt' => 'required|min_numeric,9',
        ]);
    }

    /**
     * Confirm Shipment.
     *
     * @return array
     */
    public function transReport(array $data, array &$errorMessages = [])
    {
        if (!$data) {
            throw new \Exception('Not Input Data');
        }

        $data = isset($data[0]) ? $data : [$data];

        if (count($data) > $this->maxRow) {
            throw new \Exception('Maximum limit exceeded: '.$this->maxRow.' row(s)');
        }

        foreach ($data as $info) {
            $validate = $this->validateTransReport($info);

            if ($validate !== true) {
                $errorMessages = $validate;
                throw new \Exception('Validate Failed');
            }
        }

        $soap = new \SoapClient($this->url, ['soap_version' => SOAP_1_2]);
        $response = $soap->TransReport_Json([
            'sCompany' => $this->company,
            'sPassword' => $this->password,
            'dsCusJson' => json_encode($data),
        ]);

        if (!$response->TransReport_JsonResult) {
            $errorMessages[] = 'Not Response';
            throw new \Exception('Not Response');
        }

        return json_decode($response->TransReport_JsonResult, true);
    }

    /**
     * Validate Confirm Data.
     *
     * @return mix bool/array
     */
    public function validateTransReport(array $data)
    {
        return GUMP::is_valid($data, [
            'epino' => 'required|max_len,30',
            'edelno' => 'required|between_len,10;10',
        ]);
    }
}
