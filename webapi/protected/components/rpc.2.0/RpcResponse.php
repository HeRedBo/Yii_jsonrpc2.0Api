<?php
/**
 * rpcresponse 返回类
 *
 * 返回数据格式：
 * {{"head":{"code":0,"message":"Success","time":1400205498,"jsonrpc" : "2.0"},"body":""}
 */
class RpcResponse
{
    const VERSION = 2.0;
    
    private $_requestBody;
    private $_id;

    private $responseObject;

    public function __construct($resultBody, $requestId = null)
    {
        $this->_requestBody = $resultBody;
        $this->_id = $requestId;
    }

    public function getResponseOject()
    {
        $this->_buildResponseOject();
        return $this->responseObject;
    }

    public function buildResponseOject()
    {
        $this->responseObject =  new stdClass();
        $this->_setResponseHeader();
        $this->_setResponseBody();
        $this->_setResponseId();
    }

    private function _setResponseHeader()
    {
        $headerObj = new stdClass();
        $this->_setResponseVersion();
        if($this->_requestBody instanceof RpcError)
        {
            $errorObject = $this->_requestBody->getErrorObject();
            $headerObj->code = $errorObject->code;
            $headerObj->message = $errorObject->message;
        }
        else
        {
            $headerObj->code = 0;
            $headerObj->message ='Success';
        }
        $headerObj->time = time();
        $this->responseObject->head = $headerObj;
    }

    private function _setResponseBody()
    {
        if($this->_requestBody instanceof RpcError)
        {
            $errorObject = $this->_requestBody->getErrorObject();
            if($errorObject->data)
                $this->responseObject->body = base64_encode(json_encode($errorObject->data));
        }
        else
        {
            $this->responseObject->body = base64_encode($this->_setResponseBody);
        }
    }

    private function _setResponseVersion()
    {
        $this->responseObject->jsonrpc = self::VERSION;
    }

    private function _setResponseId()
    {
        $this->responseObject->id = $this->_id;
    }
}