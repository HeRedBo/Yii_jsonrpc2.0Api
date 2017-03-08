<?php
Yii::import('application.components.rpc.2.0.CRPCAction'); 
class CRPCController extends Controller
{
    protected $head;
    // { "head" : {"jsonrpc":"2.0", "time": "1462954705", "sign":8b07c48acfe68983394b843185f4293d", "method": "subtract","params":"", "id": 1}}
    // params = base64_encode(array(['name' => 12,age=23,password => 23]))
    
    public function accessRules()
    {
        return [];
    }

    public function actions()
    {
        return [
            'index' => ['class' => 'CPRCAction'], // 使用actionIndex实现调整
        ];
    }

    public function beforeAction($action)
    {
        if(method_exists($action,'beforeRun'))
            $action->beforeRun();
        return true;
    }

    public function afterAction($action){
        if(method_exists($action, 'afterRun')){
            $action->afterRun();
        }
        return true;
    }

}