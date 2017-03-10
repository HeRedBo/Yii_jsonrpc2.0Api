<?php

class registerHandler extends handler
{
    public function rules()
    {
        return [
            ['name, password, email', 'required', 'message' => '{attribute} 不能为空'],
            ['sex,age','numerical', 'integerOnly' => true, 'message' => '{attribute} 必须为整数'],
            ['phone,age,sex', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'     => '用户名称',
            'password' => '密码',
            'email'    => '邮箱',
            'phone'    => '手机号码',
            'sex'      => '性别',
            'age'      => '用户年龄',
        ];
    }

    public function run()
    {
        if($this->validate())
        {

        }
        else
        {
            $this->responseCode = -1;
            $this->responseMsg = rest($this->getError())[0];
        }
    }
}