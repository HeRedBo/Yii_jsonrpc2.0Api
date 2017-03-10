<?php

class ApiController extends CRPCController
{


    public function actionRequestTest($request)
    {
        $data = ['code'=>1,'message'=> 'success'];
        return $data;
    }
}