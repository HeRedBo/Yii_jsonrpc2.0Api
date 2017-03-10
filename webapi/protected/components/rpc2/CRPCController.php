<?php
Yii::import('application.components.rpc2.CRPCAction'); 
class CRPCController extends Controller
{
    protected $head;
    //'{"head":{"jsonrpc":2,"time":1489071213,"sign":"8b07c48acfe68983394b843185f4293d","method":"requestTest","id":1},"body":"eyJhZ2UiOjI0LCJzZXgiOiJtYW4iLCJuYW1lIjoieGlhb2JvIn0"}';

    public function accessRules()
    {
        return [];
    }

    public function actions()
    {
        return [
            'index' => ['class' => 'CRPCAction'], // 使用actionIndex实现调整
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