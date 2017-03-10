<?php
/**
 * jsonrpc 实现
 */
class CRPCAction extends CAction
{
    /**
     * json 解析错误
     * 服务端接收到无效的json。该错误发送于服务器尝试解析json文本
     * @var int 
     */
    const PARSE_ERROR    = 32700;

    /**
     * 无效请求 发送的json不是一个有效的请求对象。
     * @var int
     */
    const INVALID_REQUEST  = 32600;

    /**
     * 找不到方法   该方法不存在或无效
     * @var int 
     */
    
    const Method_NOT_FOUND  = 32601;

    /**
     * 无效的参数 无效的方法参数。
     * @var int 
     */
    const INVALID_PARAMS  = 32602;

    /**
     * 内部错误  JSON-RPC内部错误。
     * @var int 
     */
    const INTERNAL_ERROR  = 32603;

    /**
     * 签名错误
     * @var int 
     */
    const SIGN_ERROR = 32604; 

    /**
     * 加密接口秘钥
     * @var string
     */
    const API_KEY = '4b111cc14a33b88e37e2e2934f493458';

    /**
     * 请求的json字符串
     * @var string
     */
    private $requestText;

    /**
     * 解析json后的对象
     * @var object
     */
    private $requestObject;

    /**
     * 存放响应的对象数组
     * @var array
     */
    private $responseBatchArray = [];

    public function beforeRun()
    {
        try 
        {
            #ApiLog::getInstance('request')->info('请求地址：'.Yii::app()->request->url);
            //$input = json_decode(file_get_contents("php://input")); #获取请求数据
            $inputStr = '{"jsonrpc":2,"time":1489074266,"sign":"8b07c48acfe68983394b843185f4293d","method":"requestTest","id":1,"params":"eyJhZ2UiOjI0LCJzZXgiOiJtYW4iLCJuYW1lIjoieGlhb2JvIn0"}';
            $this->requestText = $inputStr;
            $input = json_decode($inputStr);
            // $input->body = json_decode(base64_decode($input->body));
            ApiLog::getInstance('request')->info('请求参数：'.json_encode($input));
        } 
        catch (Exception $e) 
        {
            throw new Exception(500);
            exit;
        }
        return true;
    }

    public function run()
    {
        if(!$this->requestText)
            $this->requestText = file_get_contents("php://input");
        $this->processsingRequest();
        Yii::app()->end();
    }

    /**
     * 处理rpc
     * @return [type] [description]
     */
    protected function processsingRequest()
    {
        try 
        {
            $this->_parseRequestJson();
            $this->_performRequestTask();
        } catch (Exception $e) 
        {
            $responseBody = new RpcError($e->getCode(),$e->getMessage());
            $this->doResponse($responseBody->getResponseOject());
        }
    }

    /**
     * 根据传入的json对象的数量进行不同处理
     * @return [type] [description]
     */
    private function _performRequestTask()
    {
        if($this->_isBatchRequestAndNoEmpty())
        {
            $this->_performBatchCall();
        }
        else
        {
            $this->_performSingleCall();
        }
    }

    /**
     * 解析请求的json
     * @return 
     */
    private function _parseRequestJson()
    {
        if(!empty($this->requestText) )
            $this->requestObject = json_decode($this->requestText);
        else
        {
            throw new Exception("Parse error", self::PARSE_ERROR);
        }
    }

    /**
     * 判断是否批量请求且请求数据不能为空
     * @return boolean
     */
    private function _isBatchRequestAndNoEmpty()
    {
        if(is_array($this->requestObject))
        {
            if(empty($this->requestObject))
            {
                throw new Exception("valid Request", self::INVALID_REQUEST);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 处理单个数据请求
     * @return 
     */
    private function _performSingleCall()
    {
        $responseObject = $this->_getResponseOject($this->requestObject);
        // var_dump($responseObject);exit;
        $obj = $responseObject->getRpcResponseObject();
        if(!$this->_isNotification($this->requestObject))
        {
            $this->doResponse($obj);
        }
    }

    private function _performBatchCall()
    {
        foreach ($this->requestObject as $request) 
        {
            $requestObject = $this->_getResponseOject($request);
            if(!$this->_isNotification($request))
            {
                array_push($this->responseBatchArray,$request);
            }
        }
        $this->doResponse($this->responseBatchArray);
    }

    private function _isNotification($requestObject)
    {
        if(is_object($requestObject) && is_null($requestObject->id))
        {
            return true;
        }
        return false;
    }

    private function _getResponseOject($requestObject)
    {
        try 
        {
            $this->_validateRequest($requestObject);
            $requestObject->method = 'action'.ucfirst($requestObject->method);
            $methodOwnerService = $this->_isMethodAvailable($requestObject);
            $responseObject = $this->_buildResponseOject($requestObject,$methodOwnerService);
        } 
        catch (Exception $e) 
        {
            $responseObject = $this->_buildResponseOject($e);
            $responseObject->setResponseObjectId($requestObject->id);
        }
        return $responseObject;
    }

    private function _validateRequest($request)
    {
        if(!$this->_isValidRequestObject($request))
        {
            throw new Exception("Invalid Request", self::INVALID_REQUEST);
        }

        return $this->_checkSign($request);
        // TODO checkrequestTime
    }

    private function _isValidRequestObject($requestObject)
    {
        return isset($requestObject->head) && isset($requestObject->head->jsonrpc) 
        && isset($requestObject->head->method) && $this->isValidRequestObjectMethod($requestObject->head->method);
    }

    private function isValidRequestObjectMethod($requestMethod)
    {
        return (!is_null($requestMethod) && is_string($requestMethod));
    }

    /**
     * 签名检查
     * @param  object $requestObject 请求的对象
     * @return bool
     */
    private  function _checkSign($requestObject)
    {
        return true;
        if($requestObject->sign == $this->_makeSign($requestObject))
            return true;
        return false;
    }

    private function _makeSign($requestObject)
    {
        $buff = "";
        foreach ($requestObject as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return strtoupper(md5($buff . self::API_KEY)); 
    }

    private function _isMethodAvailable($requestObject)
    {
        if(array_key_exists($requestObject->method, $this->_getCallableMethodNames($this->getController())))
        {
            return $this->getController();
        }
        throw new Exception("Method not found", self::Method_NOT_FOUND);
    }

    private function _getCallableMethodNames($controller)
    {
        $methodNames = [];
        $reflection = new ReflectionClass($controller);
        $methods = $reflection->getMethods();
        foreach ($methods as $method) 
        {
            $methodNames[$method->name] = $method->getParameters();// 参数对象数组
        }
        return $methodNames;
    }

    private function _buildResponseOject($requestOrExceptionObject,$service = null)
    {
        if(is_null($service))
        {
            $responseBody   = new RpcError($requestOrExceptionObject->getCode(), $requestOrExceptionObject->getMessage());
            $responseObject = new RpcResponse($responseBody);
        }
        else
        {
            $callbackResult = $this->_call($service,$requestOrExceptionObject);
            $responseObject = new RpcResponse($callbackResult,$requestOrExceptionObject->head->id);
        }
        return $responseObject;
    }

    private function _call($methodService, $requestObject)
    {
        try 
        {
            $method = new ReflectionMethod ($methodService,$requestObject->head->method);
            $params =  (array) $this->_getRPCPartners($requestObject);
            return $method->invokeArgs($methodService,$params);
        } 
        catch (Exception $e) 
        {
            ApiLog::getInstance('exception')->error($e->__toString());
            throw new Exception("Invalid Params", self::INVALID_PARAMS);
        }
    }

    private function _getRPCPartners($requestObject)
    {
        $body = [];
        if(isset($requestObject->body))
        {
            $body = base64_decode($requestObject->body);
        }
        return $body;
    }

    private function doResponse($responseObj)
    {
        if(!empty($responseObj))
        {
            ApiLog::getInstance('response')->info('返回结果：'.json_encode($responseObj));
            header('Content-type: application/json');
            echo json_encode($responseObj);
        }
    }
}
