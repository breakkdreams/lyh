<?php 
namespace plugins\file_manage\controller;

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
 * 会员基本配置控制器
 */
class FileConfigController extends PluginAdminBaseController
{
	//图片上传配置
	public function config()
	{
		$request = request();
		$module_info = getModuleConfig('file_manage','config','file_config.json');
		$module_info = json_decode($module_info,true);
		$data = $module_info;
		$this->assign('data',$data);
		return $this->fetch('/fileConfig/file_index');
	}

	public function editSetting(){
		$param = $this->request->param();
		$module_info = getModuleConfig('file_manage','config','file_config.json');
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


		saveModuleConfigData('file_manage','config','file_config.json',$module_info);
		$this->success("修改成功");
	}


}