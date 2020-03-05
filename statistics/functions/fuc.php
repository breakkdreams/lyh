<?php

if(!function_exists('Error'))
{
    function Error($info)
    {
        throw new Exception($info);
    }
}

if(!function_exists("checkArg"))
{
    function checkArg($neadData, $data)
    {
        $info = [];
        $soleData = [];
        foreach($neadData as $key=>$value)
        {

            if(!isset($data[$key])&&$value[0])
            {
                if (isset($value[2]))
                    zy_json_echo(false, $value[2], null, "-1");
                else {
                    zy_json_echo(false, "请传入" . $key, null, "-1");
                }
            }
            if(!empty($data[$key])|| $data[$key] == "0")
            {
                $str = strtolower($value[1]);
                switch($str)
                {
                    case "number":$str = "1";break;
                    case "strtotime":$str = "2";break;
                    case "sole":$str = "3";break;
                    case "c=":$str = "4";break;
                    case "array":$str = "5";break;
                    case "+=":$str ="6";break;
                    case "-=":$str = "7";break;
                }
                if(!is_numeric($str)) Error("后台传入参数错误");
                $getData = is_array($data[$key])?$data[$key]:trim($data[$key]);
                switch($str)
                {
                    case "1":
                        if(!is_numeric($getData))
                            zy_json_echo(false, $key."字段请传入数字格式", null, "-1");
                        $info[$key] = $getData;
                        break;
                    case "2":
                        $info[$key] = strtotime($getData);
                        break;
                    case "3":
                        $info[$key] = $soleData[$key] = $getData;
                        break;
                    case "4":
                        $info[$key] = ["C=", $getData];
                        break;
                    case "5":
                        if(!is_array($getData))
                            zy_json_echo(false, $key."字段请传入数组格式", null, "-1");
                        $info[$key] = $getData;
                        break;
                    case "6":
                        if(!is_numeric($getData))
                            zy_json_echo(false, $key."字段请传入数字格式", null, "-1");
                        $info[$key] = ["+=", $getData];
                        break;
                    case "7":
                        if(!is_numeric($getData))
                            zy_json_echo(false, $key."字段请传入数字格式", null, "-1");
                        $info[$key] = ["-=", $getData];
                        break;
                    case "0":
                        $info[$key] = $getData;
                        break;
                    default:
                        Error("后台传入参数错误");
                }
            }
            else
            {
                if($value[0])
                {
                    if (isset($value[2]))
                        zy_json_echo(false, $value[2], null, "-1");
                    else {
                        zy_json_echo(false, "请传入" . $key, null, "-1");
                    }
                }
            }
        }
        if(count($soleData)>1)
        {
            $message = '';
            foreach ($soleData as $key=>$value)
                $message .= $key.' ';
            zy_json_echo(false, $message.'只能传入其中一个', null, "-1");
        }
        return $info;
    }
}
if(!function_exists("getTime"))
{
    function getTime($data)
    {
        $retData = [];
        $format='m-d';
        for ($i=0; $i< $data; $i++){
            $retData[$i]['strtotime'] = $strtotime = strtotime(date('Y-m-d').'-' .$i.' days') + 86399;
            $retData[$i]['date'] = date($format ,$strtotime);
            $retData[$i]['week'] = "周" . mb_substr( "日一二三四五六",date("w" ,$strtotime),1,"utf-8" );
            $retData[$i]["where"] = ["between", [$strtotime-86399, $strtotime]];
        }
        sort($retData);
        return $retData;
    }
}
if(!function_exists("checkWhere"))
{
    function checkWhere($neadData, $data)
    {
        $info = [];
        $soleData = [];
        foreach($neadData as $key=>$value)
        {

            if(!isset($data[$key])&&$value[0])
            {
                if (isset($value[2]))
                    zy_json_echo(false, $value[2], null, "-1");
                else {
                    zy_json_echo(false, "请传入" . $key, null, "-1");
                }
            }
            if(!empty($data[$key])|| $data[$key] == "0")
            {
                $type = isset($value[3])?$value[3]:"=";
                $whereType = isset($value[4])?strtolower($value[4]):"and";
                $str = strtolower($value[1]);
                switch($str)
                {
                    case "number":$str = "1";break;
                    case "strtotime":$str = "2";break;
                    case "sole":$str = "3";break;
                    case "array":$str = "5";break;
                }
                if(!is_numeric($str)) Error("后台传入参数错误");
                $getData = trim($data[$key]);
                switch($str)
                {
                    case "1":
                        if(!is_numeric($getData))
                            zy_json_echo(false, $key."字段请传入数字格式", null, "-1");
                        $info[$key] = [$type, $getData];
                        break;
                    case "2":
                        $info[$key] = [$type, strtotime($getData)];
                        break;
                    case "3":
                        $soleData[$key] = $getData;
                        $info[$key]  = [$type, $getData];
                        break;
                    case "5":
                        if(!is_array($getData))
                            zy_json_echo(false, $key."字段请传入数组格式", null, "-1");
                        $info[$key] = [$type, $getData];
                        break;
                    case "0":
                        $info[$key] = [$type, $getData];
                        break;
                    default:
                        Error("后台传入参数错误");
                }
                $info[$key][] = $whereType;
            }
            else
            {
                if($value[0])
                {
                    if (isset($value[2]))
                        zy_json_echo(false, $value[2], null, "-1");
                    else {
                        zy_json_echo(false, "请传入" . $key, null, "-1");
                    }
                }
            }
        }
        if(count($soleData)>1)
        {
            $message = '';
            foreach ($soleData as $key=>$value)
                $message .= $key.' ';
            zy_json_echo(false, $message.'只能传入其中一个', null, "-1");
        }
        return $info;
    }
}
if(!function_exists('getPage'))
{
    function getPage($page, $pageSize,$arrayCount)
    {
        $pagenums = $pageCount = ($arrayCount%$pageSize) == 0? ($arrayCount/$pageSize): (int)($arrayCount/$pageSize)+1;//总页数
        if($page > $pagenums)
            $page = 1;
        if($pagenums < 10)
        {
            $pageStart = 1;
        }
        elseif($page <5)
        {
            $pageStart = 1;
            $pagenums = 9;
        }
        elseif($pagenums-$page >=5)
        {
            $pageStart = $page-4;
            $pagenums = $page+4;
        }
        else
        {
            $pageStart = $pagenums - 8;
        }
        return array($page,$pagenums, $pageStart, $pageCount);
    }
}
if(!function_exists("getPageLimit")) {
    function getPageLimit($page, $pagesize)
    {
        if($page <= 0 )
            $page = 1;
        if($pagesize <= 0)
            $pagesize = 10;
       return ((string)($page-1)*$pagesize).",".$pagesize;
    }
}

