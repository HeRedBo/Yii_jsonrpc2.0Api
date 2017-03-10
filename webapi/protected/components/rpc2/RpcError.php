<?php 
/**
 * rpc错误类
 */
class RpcError
{
    private $_code;
    private $_message;
    private $_data;

    private $errorObject;


    public function __construct($code, $message ,$data = [])
    {
        $this->_code    = $code;
        $this->_message = $message;
        $this->_data    = $data;
    }

    public function getErrorObject()
    {
        $this->buildErrorObject();
        return $this->errorObject;    
    }

    protected function buildErrorObject()
    {
        $this->errorObject = new stdClass();
        $this->_setErrorCode();
        $this->_setErrorMsg();
        $this->_setErrorData();
    }

    private function _setErrorCode()
    {
        $this->errorObject->code = $this->_code;
    }

    private function _setErrorMsg()
    {
        $this->errorObject->message = $this->_message;
    }

    private function _setErrorData()
    {
        if($this->_data)
        {
            $this->errorObject->data = $this->_data;
        }   
    } 
}
