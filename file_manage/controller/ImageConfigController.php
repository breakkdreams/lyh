<?php 
namespace plugins\file_manage\controller;

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
 * 会员基本配置控制器
 */
class ImageConfigController extends PluginAdminBaseController
{
	//图片上传配置
	public function config()
	{
		$request = request();
		$module_info = getModuleConfig('file_manage','config','image_config.json');
		$module_info = json_decode($module_info,true);
		$data = $module_info;
		$this->assign('data',$data);
		return $this->fetch('/fileConfig/index');
	}

	public function editSetting(){
		$param = $this->request->param();
		$module_info = getModuleConfig('file_manage','config','image_config.json');
		$module_info = json_decode($module_info,true);
		foreach ($module_info['upload_type'] as $key=> $item) {
			if(isset($param['upload_type'][$key])){
				$module_info['upload_type'][$key]='true';
			}else{
				$module_info['upload_type'][$key]='false';
			}
		}
		$module_info['upload_size'] = $param['upload_size'];
		$module_info['upload_path'] = $param['upload_path'];
		$module_info['upload_file_pre_name'] = $param['upload_file_pre_name'];
		$module_info['is_multi'] = $param['is_multi'];
		$module_info['is_compress'] = $param['is_compress'];
		$module_info['is_watermark'] = $param['is_watermark'];

		if($module_info['is_watermark']=='true'){
			if(isset($param['watermark_type'])) {
				$module_info['watermark_type']=$param['watermark_type'];
			}
			if(isset($param['watermark_img'])) {
				$module_info['watermark_img'] = $param['watermark_img'];
			}
			if(isset($param['watermark_text'])) {
				$module_info['watermark_text'] = $param['watermark_text'];
			}
		}
		saveModuleConfigData('file_manage','config','image_config.json',$module_info);
		$this->success("修改成功");
	}

}