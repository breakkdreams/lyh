<?php
namespace plugins\statistics\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginBaseController;
use think\Db;
use plugins\statistics\controller\Haikang as haikang;


class StatisticsBeforeController extends PluginBaseController
{
    function index()
    {

        return $this->fetch("foreHtml/index");
    }
    function from()
    {
        $schoolInfo = getModuleConfig("statistics", "config", "schoolInfo.json");
        $this->uploadPath = ZY_APP_PATH."uploadFile/";
        $this->assign("uploadPath",  $this->uploadPath);
        $this->assign("schoolInfo",  $schoolInfo);
        return $this->fetch("foreHtml/from");
    }
    function cs()
    {
        $s = new haikang();
        $d = $s->cs_1();
        echo json_encode($d, JSON_UNESCAPED_UNICODE);
    }
}