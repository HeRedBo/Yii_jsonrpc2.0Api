<?php

class handler extends CFormModel
{
    protected $context; # 上下文

    private $requestObj;

    protected $responseObj;
    protected $responseCode = 0;
    protected $responseMsg = '';
    protected $responseData = [];

    public function __construct($requestObj)
    {
        $this->requestObj = CJSON::decode($requestObj);
    }

    public function __set($property, $value)
    {
        $this->property = $value;
    }

    private function getAttributes($names = null)
    {
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($attributes,array($this->context));

        $values = [];
        if(!empty($name) && !empty($attributes))
        {
            foreach ($names as $key => $name) 
            {
                $values[$name] = $attributes[$name] ?: null;
            }
        }
        return $values;
    }
}