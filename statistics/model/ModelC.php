<?php


namespace plugins\statistics\model;
use plugins\statistics\model\BaseModel as BM;
use think\Db;

class ModelC extends BM
{
    function __construct($tableName)
    {
        parent::__construct($tableName);
    }
    public function showColumns()
    {
        $s = $this->DB->getTableFields();
        $i = 1;
    }
    public function showC()
    {
        $sql = "select * from information_schema.columns where table_name=".$this->tableName;
        $s = "select * from information_schema.columns where table_name= ?";
        $s = $this->DB->query($s, [$this->tableName]);
        $i = 1;
    }





    //获取model的字段
    public static function  getTableField($checkTable = [])
    {
        $data = [];

        $tablePrefix = config('database.prefix');
        $databaseName = config('database.database');
        foreach ($checkTable as $k=>$v)
        {
            $num = 0;
            $tableAllName = $tablePrefix.$k;
            $Field =  DB::table("information_schema.columns")->where("table_name", $tableAllName)->select()->all();
            $exceptField = $v;
            $type = is_array($exceptField)?true:false;
            if(!$type)
            {
                $exceptField = str_replace(" ", "", $exceptField);
                 $exceptField = explode(",", $exceptField);
            }
            foreach ($Field as $key=>$value)
            {
                if($value["TABLE_SCHEMA"]!= $databaseName)
                    continue;
                if($type)
                {
                    if(!in_array($value["COLUMN_NAME"], $exceptField) && !in_array("*", $exceptField))
                        continue;
                }
                else
                {
                    if(in_array($value["COLUMN_NAME"], $exceptField))
                        continue;
                }
                $data[$k][$num]["key"] = $value["COLUMN_NAME"];

                if(empty($value["COLUMN_COMMENT"]))
                    $data[$k][$num]["title"] = $value["COLUMN_NAME"];
                else
                {
                    $temporaryData = explode("|", $value["COLUMN_COMMENT"]);
                    $data[$k][$num]["title"] = $temporaryData[0];
                }
                $num++;
            }
        }

        return $data;
    }
}