<?php

class Service extends CFormModel
{

    protected $resultCode = 0;
    protected $resultMsg = '';
    protected $resultData = null;

    /**
     * 设置返回码
     * @param interger $code
     */
    public function setResultCode($code)
    {
        $this->resultCode = $code;
    }

    /**
     * 设置返回信息
     * @param string $msg 信息
     */
    public function setResultMsg($msg)
    {
        $this->resultMsg = $msg;
    }

    /**
     * 设置返回的数据
     * @param mixed $data 设置返回的数据
     */
    public function setResultData($data)
    {
        $this->resultData = $data;
    }
    
    public function getResultCode()
    {
        return $this->resultCode;
    }

    public function getResultMsg()
    {
        return $this->resultMsg;
    }

    public function getResultData()
    {
        return $this->resultData;
    }
}