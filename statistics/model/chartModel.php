<?php
/**
 * Created by PhpStorm.
 * User: 徐强
 * Date: 2019/12/20
 * Time: 10:29
 */

namespace plugins\statistics\model;
use plugins\statistics\model\BaseModel as BM;
use think\Db;
use think\Error;
use think\Exception;

class chartModel
{
    private $chartType;
    private $data;
    public function __construct($chartType, $data)
    {
        $this->chartType = $chartType;
        $this->data = $data;
    }
    public function handOut()
    {
        $data = ["type" => $this->chartType ,"data"=>[]];
        if(!array_key_exists("chickTable", $this->data) || count($this->data["chickTable"])<=0)
            throw new Exception("请先配置文件");
        switch ($this->chartType)
        {
            case "chartpie":
                $this->chartpieChart($data["data"]);break;
            case "chartcard":
                $this->chartcardChart($data["data"]);break;
            case "chartbar":
                $this->chartbarChart($data["data"]);break;
            case "chartline":
                $this->chartlineChart($data);break;
        }
        return $data;
    }
    public function checkField($info)
    {
        $field = [];
        switch ($info["selectType"])
        {
            case "数量": $field[] = "count('*') as value";break;
            case "求和": $field[] = "sum(`".$info["selectField"]."`) as value";break;
            default: throw new Exception("filed字段类型错误");break;
        }
        return $field;
    }
    public function checkWhere($info)
    {
        if(!isset($info["where"]) || count($info["where"]) <= 0)
            throw new Exception("有内容未选择筛选条件");
    }

    public function chartpieChart(&$data)
    {
        $num = 0;
        foreach ($this->data["chickTable"] as $key=>$value)
        {
            $this->checkWhere($value);
            $model = new BM($value["tableName"]);
            $field = $this->checkField($value["field"]);
            $data[$num] = $model->get_one($value["where"], $field);
            $data[$num]["name"] = $value["name"];
            $num ++;
        }
    }
    public function chartcardChart(&$data)
    {
        $num = 0;
        foreach ($this->data["chickTable"] as $key=>$value)
        {
            $this->checkWhere($value);
            $model = new BM($value["tableName"]);
            $field = $this->checkField($value["field"]);
            $data[$num] = $model->get_one($value["where"], $field);
            $data[$num]["name"] = $value["name"];
            $data[$num]["iconfont"] = isset($value["iconfont"])?$value["iconfont"]:"icon-xihuan1";
            $data[$num]["color"] = isset($value["color"])?$value["color"]:"#2db7f5";
            if(isset($value["unit"]))
                $data[$num]["unit"] = $value["unit"];
            $num ++;
        }
    }
    public function chartbarChart(&$data)
    {
        $num = 0;
        foreach ($this->data["chickTable"] as $key=>$value)
        {
            $this->checkWhere($value);
            $model = new BM($value["tableName"]);
            $field = $this->checkField($value["field"]);
            if(isset($this->data["timeCycle"]) && !empty($this->data["timeCycle"]))
            {
                if($num != 0)
                    throw new Exception("柱状图的周期模式只能有一种数据格式");
                if(empty($value["field"]["timeField"]))
                    throw new Exception("请选择时间字段");
                foreach(getTime($this->data["timeCycle"]) as $x=>$y)
                {
                    $f[$value["field"]["timeField"]] = $y["where"];
                    $where = array_merge($value["where"], $f);
                    $temporaryData = $model->get_one($where, $field);
                    $data[$y[$this->data["showTimeCycleType"]]] = $temporaryData["value"];
                }
                break;
            }
            else
            {
                $temporaryData = $model->get_one($value["where"], $field);
                $data[$value["name"]] = $temporaryData["value"];
            }
            $num ++;
        }
    }
    public function chartlineChart(&$data)
    {
        $num = 0;
        $data["data"] = [];
        $data["xData"] = [];
        if(isset($this->data["timeCycle"]) && !empty($this->data["timeCycle"]))
        {
            foreach ($this->data["chickTable"] as $key=>$value)
            {
                $this->checkWhere($value);
                $model = new BM($value["tableName"]);
                $field = $this->checkField($value["field"]);
                if(empty($value["field"]["timeField"]))
                    throw new Exception("请选择时间字段");
                $data["data"][] = ["name"=> $value["name"], "type"=>"line", "data"=>[]];
                $f = [];
                foreach(getTime($this->data["timeCycle"]) as $x=>$y)
                {
                    $f[$value["field"]["timeField"]] = $y["where"];
                    $where = array_merge($value["where"], $f);
                    $temporaryData = $model->get_one($where, $field);
                    $data["data"][$num]["data"][] = $temporaryData["value"];
                    if($num == 0)
                        $data["xData"][] = $y[$this->data["showTimeCycleType"]];
                }
                $num ++;

            }
        }
        else
            throw new Exception("请选择时间模式");
    }
}