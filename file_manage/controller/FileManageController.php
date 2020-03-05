<?php 
namespace plugins\file_manage\controller;

use cmf\controller\PluginBaseController;//引入此类
use plugins\file_manage\model\PluginFileModel;


/**
 * 会员管理控制器
 */
class FileManageController extends PluginBaseController
{
	public function getConfig(){
		$pfm=new PluginFileModel();
		$module_info = getModuleConfig('file_manage','config','file_config.json');
		$module_info = json_decode($module_info,true);
		$arr=[];
		$arr2=[];
		foreach ($module_info['upload_type'] as $key=> $item) {
			if($item=='true'){
				$arr[] = $pfm->get_mime_type($key);
				$arr2[]=$key;
			}
		}
		$module_info['upload_type']=$arr;
		$module_info['upload_data_type']=$arr2;
		$module_info['upload_path']='upload/'.$module_info['upload_path'];
		return $module_info;
	}

	public function index()
	{
		$where = [
			'filetype'=>9
		];
		if(isset($_POST['start_time'])){
			$where['start_time']=$_POST['start_time'];
		}
		if(isset($_POST['end_time'])){
			$where['end_time']=$_POST['end_time'];
		}
		$list  = PluginFileModel::getall($where);
		$this->assign('list',$list['list']);
		$this->assign('page',$list['page']);
		return $this->fetch('/file/file_index');
	}

	/**
	 * PHP上传文件
	 * $files, $path = "./upload", $imagesExt = ['jpg', 'png', 'jpeg', 'gif', 'mp4']
	 */

	function upload_file($isModule=false)
	{
		$config=$this->getConfig();
		if(!$_FILES){
			zy_array(false,"请上传文件","",-99,$isModule);
		}
		$files=$_FILES['file'];
		//print_r($files);
		// 判断错误号
		if (is_array(@$files['tmp_name'])) {
			zy_array(false,"暂不支持多文件上传","",-100,$isModule);
			if($config['is_multi']!='true'){
			}
		}
		if (@$files['error'] == 00) {
			// 判断文件类型
			$ext = strtolower(pathinfo(@$files['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, $config['upload_data_type'])) {
				zy_array(false,"该文件类型不支持","",-2,$isModule);
			}
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, @$files['tmp_name']);
			$type=strchr($mimetype,";",true);
			//print_r($type."\n");
			if (!in_array($type, $config['upload_type'])) {
				zy_array(false,"非法文件类型".$type,"",-1,$isModule);
			}
			if(@$files["size"] > $config['upload_size']){//判断是否大于规定大小
				zy_array(false,'文件大小超过限制',"",-3,$isModule);
			}

			$time = date('Ymd',time());
			$path=$config['upload_path'].$time.'/';
			// 判断是否存在上传到的目录
			if (!is_dir($path)) {
				mkdir($path, 0777, true);
			}

			// 生成唯一的文件名
			//$fileName = md5(uniqid(microtime(true), true)) . '.' . $ext;
			$fileName = $config['upload_file_pre_name'].substr(md5(time()),0,10).mt_rand(1,10000) . '.' . $ext;

			// 将文件名拼接到指定的目录下
			$destName = $path . $fileName;

			// 进行文件移动
			if (!move_uploaded_file($files['tmp_name'], $destName)) {
				zy_array(false,"文件上传失败","",-200,$isModule);
			}else{
				$res=$this->add_file($fileName,$destName,9);
				zy_array(true,"文件上传成功",$res,200,$isModule);
			}
		} else {
			// 根据错误号返回提示信息
			zy_array(true,"系统错误","",-199,$isModule);
			switch (@$files['error']) {
				case 1:
					echo "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
					break;
				case 2:
					echo "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
					break;
				case 3:
					echo "文件只有部分被上传";
					break;
				case 4:
					echo "没有文件被上传";
					break;
				case 6:
				case 7:
					echo "系统错误";
					break;
			}
		}

	}

	/**
	 * desription 添加到数据库
	 */
	function add_file($filename,$path,$filetype,$des="文件"){
		$path2=substr(strchr($path, "upload/"), 7);
		$data=[
			'filename'=>$filename,
			'filetype'=>$filetype,
			'filepath'=>$path,
			'fileurl'=>cmf_get_image_url($path2),
			'filedes'=>$des,
			'status'=>'1',
			'addtime'=>date("Y-m-d H:i:s",time())
		];
		PluginFileModel::set($data);
		return $data;
	}
}