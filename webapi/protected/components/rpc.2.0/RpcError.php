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
        $this->errorObject->code = $this->_code;
        $this->errorObject->message = $this->_message;
        if(!empty($this->_data) && is_array($this->_data))
            $this->errorObject-> $this->_data;
    }



    public function setErrorCode($code)
    {
        $this->_code = $code;
    }

    public function setErrorMsg($msg)
    {
        $this->_message = $msg;
    }

    public function setErrorData($data)
    {
        $this->_data = $data;
    }
 
}
