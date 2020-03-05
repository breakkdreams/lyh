<?php

namespace plugins\file_manage\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */

use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\file_manage\controller\ImageController;
use plugins\file_manage\controller\VideoController;
use plugins\file_manage\model\PluginFileModel;
use think\Db;

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    protected $apiMode = null;

    protected function _initialize()
    {

    }

    public function index($isModule = false)//index(命名规范)
    {
        $param = $this->request->post();
        $param = zy_decodeData($param, $isModule);

        return zy_array(true, '连入成功', $param, 200, $isModule);
    }

    public function get_img($id, $field = "*", $isModule = false)//index(命名规范)
    {
        $data = PluginFileModel::get_one(['filetype' => 1, 'status' => 1, 'id' => $id], $field);
        if ($data) {
            return zy_array(true, '操作成功', $data, 200, $isModule);
        } else {
            return zy_array(false, '操作失败', $data, 404, $isModule);
        }
    }


    public function get_imgs($id, $field = "*", $isModule = false)//index(命名规范)
    {
        $id = preg_replace('#,{2,}#', ',', trim($id, ","));
        $data = PluginFileModel::where('filetype', 1)->where('status', 1)->where('id', 'in', $id)->field($field)->select()->toArray();
        if ($data) {
            return zy_array(true, '操作成功', $data, 200, $isModule);
        } else {
            return zy_array(false, '操作失败', $data, 404, $isModule);
        }
    }


    public function get_video($id, $field = "*", $isModule = false)//index(命名规范)
    {
        $data = PluginFileModel::get_one(['filetype' => 2, 'status' => 1, 'id' => $id], $field);
        if ($data) {
            return zy_array(true, '操作成功', $data, 200, $isModule);
        } else {
            return zy_array(false, '操作失败', $data, 404, $isModule);
        }
    }


    public function get_videos($id, $field = "*", $isModule = false)//index(命名规范)
    {
        $id = preg_replace('#,{2,}#', ',', trim($id, ","));
        $data = PluginFileModel::where('filetype', 2)->where('status', 1)->where('id', 'in', $id)->field($field)->select()->toArray();
        if ($data) {
            return zy_array(true, '操作成功', $data, 200, $isModule);
        } else {
            return zy_array(false, '操作失败', $data, 404, $isModule);
        }
    }


    public function get_file($id, $field = "*", $isModule = false)//index(命名规范)
    {
        $data = PluginFileModel::get_one(['filetype' => 9, 'status' => 1, 'id' => $id], $field);
        if ($data) {
            return zy_array(true, '操作成功', $data, 200, $isModule);
        } else {
            return zy_array(false, '操作失败', $data, 404, $isModule);
        }
    }

    public function get_files($id, $field = "*", $isModule = false)//index(命名规范)
    {
        $id = preg_replace('#,{2,}#', ',', trim($id, ","));
        $data = PluginFileModel::where('filetype', 9)->where('status', 1)->where('id', 'in', $id)->field($field)->select()->toArray();
        if ($data) {
            return zy_array(true, '操作成功', $data, 200, $isModule);
        } else {
            return zy_array(false, '操作失败', $data, 404, $isModule);
        }
    }


    public function upload_base64($img = "", $isModule = false)
    {
        $Image = new ImageController();
        $Image->upload_base64($img, $isModule);
    }


    public function upload_img($file = "", $isModule = false)
    {
        $Image = new ImageController();
        $Image->upload_img($isModule);
    }

    public function upload_video($file = "", $isModule = false)
    {
        $Video = new VideoController();
        $Video->upload_video();
    }


    public function upload_file($file = "", $isModule = false)
    {
        $File = new FileManageController();
        $File->upload_file($isModule);
    }

    /**
     * 获取全部数据
     */
    public function fileData($filetype = "", $isModule = true)
    {

        if (!$filetype) {
            $where = [];
        } else {
            $where = ['filetype' => $filetype];
        }

        $data = Db::name('file')->where($where)->select()->toArray();
        return zy_array(true, '操作成功', $data, 200, $isModule);
    }

    /**
     * 添加附件数据
     */
    public function addData($data)
    {
        $id = Db::name("file")->insertGetId($data);
        return $id;
    }

    /**
     * 获取附件数据
     */
    public function getData($id)
    {
        $data = Db::name("file")->where("id", $id)->find();
        return $data;
    }
}