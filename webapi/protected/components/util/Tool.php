<?php
/**
 * php 工具组件
 */
class Tool
{

    /**
     * jsoneode 转化 为兼容php 低版本
     */
    public static function jsonEncodeFormat($json)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return json_encode($json, JSON_UNESCAPED_UNICODE);
        } 
        else 
        {
            $str = json_encode($json);
            return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $str); 
        }
    }
}