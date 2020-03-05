<?php
/**
 * Created by PhpStorm.
 * User: WHL
 * Date: 2019/11/12
 * Time: 14:37
 */

namespace plugins\file_manage\controller;

use cmf\controller\PluginBaseController;
use plugins\file_manage\model\PluginFileModel;

class ImageController extends PluginBaseController
{
    private $APP_PATH="";
    protected function _initialize()
    {
        $this->APP_PATH=strstr($this->request->Url(true),$this->request->path(),true);
    }
    public function getConfig(){
        $module_info = getModuleConfig('file_manage','config','image_config.json');
        $module_info = json_decode($module_info,true);
        $arr=[];
        $arr2=[];
        foreach ($module_info['upload_type'] as $key=> $item) {
            if($item=='true'){
                $arr[]='image/'.$key;
                $arr2[]=$key;
            }
        }
        $module_info['upload_type']=$arr;
        $module_info['upload_data_type']=$arr2;
        $module_info['upload_path']='uploadFile/'.$module_info['upload_path'];
        return $module_info;
    }

    //图片管理
    public function index()
    {
        $where = [
            'filetype'=>1,
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
        return $this->fetch('/file/index');
    }

    /**
     * 文字水印
     */
    public function watertext($dst_path,$path='',$msg="WHL@卓远网络",$x=-1,$y=-1,$size=20,$angle=0,$rgba=[0,0,0,50]){

        /*给图片加文字水印的方法*/
        //$dst_path = 'http://f4.topitme.com/4/15/11/1166351597fe111154l.jpg';
        if(empty($path)){
            $path=$dst_path;
        }
        list($dst_w,$dst_h,$dst_type) = getimagesize($dst_path);
        /*list(mixed $varname[,mixed $......])--把数组中的值赋给一些变量
        像array()一样，这不是真正的函数，而是语言结构，List()用一步操作给一组变量进行赋值*/
        /*getimagesize()能获取到什么信息？
        getimagesize函数会返回图像的所有信息，包括大小，类型等等*/

        $dst=imagecreatetruecolor($dst_w, $dst_h);
//        $dst = imagecreatefromstring(file_get_contents($dst_path));
        /*imagecreatefromstring()--从字符串中的图像流新建一个图像，返回一个图像标示符，其表达了从给定字符串得来的图像
        图像格式将自动监测，只要php支持jpeg,png,gif,wbmp,gd2.*/
        $font = 'plugins/file_manage/font/simsun.ttc';

        $black = imagecolorallocatealpha($dst,$rgba[0],$rgba[1],$rgba[2],$rgba[3]); // 实现透明
        /* --- 用以处理缩放gif和png图透明背景变黑色问题 开始 --- */
        $color = imagecolorallocate($dst,255,255,255);
        imagecolortransparent($dst,$color);
        imagefill($dst,0,0,$color);
        /* --- 用以处理缩放gif和png图透明背景变黑色问题 结束 --- */

        if($dst_h<500){
            $size = $dst_h / 20;   //组合之后logo的宽度(占二维码的1/5)
        }else{
            $size = $dst_h / 30;   //组合之后logo的宽度(占二维码的1/5)
        }

        //处理位置
        if($x<0){
            $x=$dst_w+$x-mb_strlen($msg, 'utf-8')*($size+3)-$size;
        }else{
            $x=$x+$size;
        }
        if($y<0){
            $y=$dst_h+$y-10;
        }else{
            $y=$y+$size+10;
        }

        /*imagefttext($img,$size,$angle,$x,$y,$color,$fontfile,$text)
        $img由图像创建函数返回的图像资源
        size要使用的水印的字体大小
        angle（角度）文字的倾斜角度，如果是0度代表文字从左往右，如果是90度代表从上往下
        x,y水印文字的第一个文字的起始位置
        color是水印文字的颜色
        fontfile，你希望使用truetype字体的路径*/

        switch($dst_type){
            case 1://GIF
                //header("content-type:image/gif");
                $image = imagecreatefromgif($dst_path);
                imagecopyresampled($dst, $image, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);

                imagefttext($dst, $size, $angle, $x, $y,$black, $font,$msg);
                imagegif($dst,$path);
                imagedestroy($image);
                break;
            case 2://JPG
                //header("content-type:image/jpeg");
                $image = imagecreatefromjpeg($dst_path);
                imagecopyresampled($dst, $image, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);

                imagefttext($dst, $size, $angle, $x, $y,$black, $font, $msg);
                imagejpeg($dst,$path);
                imagedestroy($image);
                break;
            case 3://PNG
                //header("content-type:image/png");
//                imagepng($dst,$path);
                $image = imagecreatefrompng($dst_path);
                imagecopyresampled($dst, $image, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);

                imagefttext($dst, $size, $angle, $x, $y,$black, $font, $msg);
                imagepng($dst,$path);
                imagedestroy($image);
                break;
            default:
                break;
            /*imagepng--以PNG格式将图像输出到浏览器或文件
            imagepng()将GD图像流(image)以png格式输出到标注输出（通常为浏览器），或者如果用filename给出了文件名则将其输出到文件*/
        }
        imagedestroy($dst);
        return $path;
    }

    //图片水印
    public function waterimg($dst_path,$path='',$src_path,$x=-1,$y=-1)
    {
        if(empty($path)){
            $path=$dst_path;
        }
        //$src_path=$this->upfile_dir($this->strget($src_path));
        //获取处理图片的宽高
        list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);
        //获取水印图片的宽高
        list($src_w, $src_h,$src_type) = getimagesize($src_path);

        //创建图片的实例
        $dst = imagecreatefromstring(file_get_contents($dst_path));
        $src = imagecreatefromstring(file_get_contents($src_path));

        //判断是否水印图片的宽高是否大于处理图片的宽高
        if ($src_w > $dst_w || $src_h > $dst_h) {
            return false;
        }
        if($dst_w<300||$dst_h<300){
            $rate=3;
        }
        elseif($dst_w<1000||$dst_h<1000){
            $rate=4;
        }elseif($dst_w<3000||$dst_h<3000){
            $rate=8;
        }else{
            $rate=ceil($dst_w/1000);
        }
        $logo_qr_width = $dst_w / $rate;   //组合之后logo的宽度(占二维码的1/5)
        $scale = $src_w/$logo_qr_width;  //logo的宽度缩放比(本身宽度/组合后的宽度)
        $logo_qr_height = $src_h/$scale; //组合之后logo的高度

        //处理位置
        if($x<0){
            $x=$dst_w+$x-$logo_qr_width;
        }
        if($y<0){
            $y=$dst_h+$y-$logo_qr_height;
        }


        //输出图片
        switch ($dst_type) {
            case 1://GIF
                $image_wp = imagecreatetruecolor($dst_w, $dst_h);

                /* --- 用以处理缩放gif和png图透明背景变黑色问题 开始 --- */
                $color = imagecolorallocate($image_wp,255,255,255);
                imagecolortransparent($image_wp,$color);
                imagefill($image_wp,0,0,$color);
                /* --- 用以处理缩放gif和png图透明背景变黑色问题 结束 --- */

                $image = imagecreatefromgif($dst_path);
                imagecopyresampled($image_wp,$image, 0, 0, 0, 0, $dst_w, $dst_h,  $dst_w, $dst_h);
                imagedestroy($image);

                switch ($src_type) {
                    case 2://JPG
                        //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
                        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, 50);
                        break;
                    case 3://PNG
                        $image = imagecreatefrompng($src_path);
                        imagecopyresampled($image_wp,$image, $x, $y, 0, 0, $logo_qr_width, $logo_qr_height,  $src_w, $src_h);
                        imagedestroy($image);
                        break;
                    default:
                        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, 50);
                        break;
                }

                imagegif($image_wp,$path);
                break;
            case 2://JPG
                switch ($src_type) {
                    case 2://JPG
                        //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
                        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, 50);
                        break;
                    case 3://PNG
                        $image = imagecreatefrompng($src_path);
                        imagecopyresampled($dst,$image, $x, $y, 0, 0, $logo_qr_width, $logo_qr_height,  $src_w, $src_h);
                        imagedestroy($image);
                        break;
                    default:
                        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, 50);
                        break;
                }
                imagejpeg($dst,$path);
                break;
            case 3://PNG
                $image_wp = imagecreatetruecolor($dst_w, $dst_h);

                /* --- 用以处理缩放gif和png图透明背景变黑色问题 开始 --- */
                $color = imagecolorallocate($image_wp,255,255,255);
                imagecolortransparent($image_wp,$color);
                imagefill($image_wp,0,0,$color);
                /* --- 用以处理缩放gif和png图透明背景变黑色问题 结束 --- */

                $image = imagecreatefrompng($dst_path);
                imagecopyresampled($image_wp,$image, 0, 0, 0, 0, $dst_w, $dst_h,  $dst_w, $dst_h);
                imagedestroy($image);

                switch ($src_type) {
                    case 2://JPG
                        //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
                        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, 50);
                        break;
                    case 3://PNG
                        $image = imagecreatefrompng($src_path);
                        imagecopyresampled($image_wp,$image, $x, $y, 0, 0, $logo_qr_width, $logo_qr_height,  $src_w, $src_h);
                        imagedestroy($image);
                        break;
                    default:
                        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, 50);
                        break;
                }

                imagepng($image_wp,$path);
                break;
            default:
                break;
        }
        imagedestroy($dst);
        imagedestroy($src);
    }

    /**
     * 截取字符
     */
    public function strget($string=''){
        //$newstring= strstr( $string, 'upload/'); //默认返回查找值@之后的尾部，@jb51.net
        if(strpos($string,'uploadFile/')!==false){
            $newstring= substr($string,strpos($string,"uploadFile/")+7); //默认返回查找值@之后的尾部，@jb51.net
            return $newstring;
        }else{
            return $string;
        }
    }
    /**
	 * 上传文件路径
	 *
     */
    public function upfile_dir($string=''){
        $newstring= 'uploadFile/'.$string;
        return $newstring;
    }

    /**
     * 多图片上传
     */
    function upload_img($isModule=false){
        //var_dump($_FILES);
        $config=$this->getConfig();
        if(!$_FILES){
            zy_array(false,"请上传图片","",-99,$isModule);
        }
        if(is_array($_FILES["file"]["tmp_name"]))
        {
            if($config['is_multi']!='true'){
                zy_array(false,"不支持多图上传","",-100,$isModule);
            }
            foreach ($_FILES["file"]["tmp_name"] as $item) {
                $img  = getimagesize($item);//txt类型转换为jpg类型
                //$img  = exif_imagetype($_FILES["file"]["tmp_name"]);//判断是否是真实图像
                if(!$img){
                    zy_array(false,"图片格式错误","",-14,$isModule);
                }
            }

            foreach ($_FILES["file"]["error"] as $key=> $item) {
                if($item!=0){
                    foreach ($_FILES["file"] as $key2=> $item2) {
                        array_splice($_FILES["file"][$key2],$key,1);
                    }
                }
            }

            foreach ($_FILES["file"]["type"] as $key=> $item) {
                if( !in_array($item, $config['upload_type'])){
                    foreach ($_FILES["file"] as $key2=> $item2) {
                        array_splice($_FILES["file"][$key2],$key,1);
                    }
                }
            }

            foreach ($_FILES["file"]["size"] as $key=> $item) {
                if($item > $config['upload_size']){//判断是否大于10M
                    foreach ($_FILES["file"] as $key2=> $item2) {
                        array_splice($_FILES["file"][$key2],$key,1);
                    }
                }
            }

            $time = date('Ymd',time());
            if(!file_exists($config['upload_path'])){
                mkdir($config['upload_path'],0777,true);
            }
            if(!file_exists(($config['upload_path'].$time.'/'))){
                mkdir($config['upload_path'].$time.'/');
            }
            foreach ($_FILES["file"]["name"] as $key=> $item) {
                $filename[$key] = $config['upload_file_pre_name'].substr(md5(time()),0,10).mt_rand(1,10000);
                $ext[$key] = pathinfo($item, PATHINFO_EXTENSION);
                $localName[$key] = $config['upload_path'].$time."/".$filename[$key].'.'.$ext[$key];

                if ( move_uploaded_file($_FILES["file"]["tmp_name"][$key], $localName[$key]) == true) {
                    if($config['is_compress']=='true'){//压缩图片
                        $this->image_png_size_add($localName[$key],$localName[$key]);
                    }
                    if($config['is_watermark']=='true'){//水印
                        switch ($config['watermark_type']) {
                            case '1'://水印图片
                                $this->waterimg($localName[$key],$localName[$key],$config['watermark_img']);
                                break;
                            case '2'://水印文字
                                $this->watertext($localName[$key],$localName[$key],$config['watermark_text']);
                                break;
                            case '3'://水印图片&文字
                                $this->waterimg($localName[$key],$localName[$key],$config['watermark_img']);
                                $this->watertext($localName[$key],$localName[$key],$config['watermark_text']);
                                break;

                        }
                    }
                    $lurl = APP_PATH.$localName[$key];
                    $res[]=$this->add_file($filename[$key].'.'.$ext[$key],$localName[$key],1);
                }else{
                    zy_array(true,'图片上传失败',"",-200,$isModule);
                }
            }
            zy_array(true,'图片上传成功',$res,200,$isModule);
        }
        else
        {
            $img  = getimagesize($_FILES["file"]["tmp_name"]);//txt类型转换为jpg类型
            //$img  = exif_imagetype($_FILES["file"]["tmp_name"]);//判断是否是真实图像
            if(!$img){
                zy_array(false,"图片格式错误","",-14,$isModule);
            }

            if($_FILES["file"]["error"]!=0){
                zy_array(false,$_FILES["file"]["error"],"",0,$isModule);
            }

            if( !in_array($_FILES["file"]["type"], $config['upload_type'])){
                zy_array(false,$_FILES["file"]["type"],"",-1,$isModule);
            }

            if($_FILES["file"]["size"] > $config['upload_size']){//判断是否大于10M
                zy_array(false,'图片大小超过限制',"",-2,$isModule);
            }
            $filename = $config['upload_file_pre_name'].substr(md5(time()),0,10).mt_rand(1,10000);
            $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
            //
            $time = date('Ymd',time());
            if(!file_exists($config['upload_path'])){
                mkdir($config['upload_path'],0777,true);
            }
            if(!file_exists(($config['upload_path'].$time.'/'))){
                mkdir($config['upload_path'].$time.'/');
            }

            $localName = $config['upload_path'].$time."/".$filename.'.jpg';

            if ( move_uploaded_file($_FILES["file"]["tmp_name"], $localName) == true) {
                if($config['is_compress']=='true'){//压缩图片
                    $this->image_png_size_add($localName,$localName);
                }
                if($config['is_watermark']=='true'){//水印
                    switch ($config['watermark_type']) {
                        case '1'://水印图片
                            $this->waterimg($localName,$localName,$config['watermark_img']);
                            break;
                        case '2'://水印文字
                            $this->watertext($localName,$localName,$config['watermark_text']);
                            break;
                        case '3'://水印图片&文字
                            $this->waterimg($localName,$localName,$config['watermark_img']);
                            $this->watertext($localName,$localName,$config['watermark_text']);
                            break;

                    }
                }
                $lurl = APP_PATH.$localName;
                $localName_list = $time."/".$filename.'.jpg';
                $res=$this->add_file($filename.'.'.$ext,$localName_list,1);
                zy_array(true,'图片上传成功',$res,200,$isModule);
            }else{
                zy_array(true,'图片上传失败',"",-200,$isModule);
            }
        }
    }

    /**
     * base64图片上传
     */
    function upload_base64($img,$isModule=false){
        $config=$this->getConfig();
        if(is_array($img)){
            if($config['is_multi']!='true'){
                zy_array(false,"不支持多图上传","",-100,$isModule);
            }
            foreach ($img as $item) {
                $base64_img = trim($item);
                $base64 = preg_replace('/^(data:\s*image\/(\w+);base64,)/', '', $base64_img);
                $base64 = str_replace('=', '', $base64);
                $img_len = strlen($base64);
                $file_size = $img_len - ($img_len / 8) * 2;
                //$file_size = number_format(($file_size / 1024), 2).'kb';
                if ($file_size > $config['upload_size']) {
                    continue;
                }
                $time = date('Ymd',time());
                $filename=$config['upload_file_pre_name'].substr(md5(time()),0,10).mt_rand(1,10000);
                $up_dir = $config['upload_path'].$time."/";//存放在当前目录的upload文件夹下
                if(!file_exists($up_dir)){
                    mkdir($up_dir,0777,true);
                }

                if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
                    $type = $result[2];
                    if(in_array($type,$config['upload_data_type'])){
                        $new_file = $up_dir.$filename.'.'.$type;
                        if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
                            $localName = str_replace('../../..', '', $new_file);
                            if($config['is_compress']=='true'){//压缩图片
                                $this->image_png_size_add($localName,$localName);
                            }
                            if($config['is_watermark']=='true'){//水印
                                switch ($config['watermark_type']) {
                                    case '1'://水印图片
                                        $this->waterimg($localName,$localName,$config['watermark_img']);
                                        break;
                                    case '2'://水印文字
                                        $this->watertext($localName,$localName,$config['watermark_text']);
                                        break;
                                    case '3'://水印图片&文字
                                        $this->waterimg($localName,$localName,$config['watermark_img']);
                                        $this->watertext($localName,$localName,$config['watermark_text']);
                                        break;
                                }
                            }
                            $res[]=$this->add_file($filename.'.'.$type,$localName,1);
                        }else{
                            zy_array(false,'图片上传失败',"",-200,$isModule);
                        }
                    }else{
                        //文件类型错误
                        continue;
                    }
                }else{
                    //文件错误
                    continue;
                }
            }
            zy_array(true,'图片上传成功',$res,200,$isModule);
        }
        else{
            $base64_img = trim($img);
            $base64 = str_replace('data:image/jpeg;base64,', '', $base64_img);
            $base64 = str_replace('=', '', $base64);
            $img_len = strlen($base64);
            $file_size = $img_len - ($img_len / 8) * 2;
            //$file_size = number_format(($file_size / 1024), 2).'kb';
            if ($file_size > $config['upload_size']) {
                zy_array(false,'图片大小超过限制',"",-2,$isModule);
            }

            $time = date('Ymd',time());
            $filename=$config['upload_file_pre_name'].substr(md5(time()),0,10).mt_rand(1,10000);
            $up_dir = $config['upload_path'].$time."/";//存放在当前目录的upload文件夹下
            if(!file_exists($up_dir)){
                mkdir($up_dir,0777,true);
            }

            if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
                //var_dump($result);
                $type = $result[2];
                //array('pjpeg','jpeg','jpg','gif','bmp','png')

                if(in_array($type,$config['upload_data_type'])){
                    $new_file = $up_dir.$filename.'.'.$type;
                    if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
                        $localName = str_replace('../../..', '', $new_file);
                        if($config['is_compress']=='true'){//压缩图片
                            $this->image_png_size_add($localName,$localName);
                        }
                        if($config['is_watermark']=='true'){//水印
                            switch ($config['watermark_type']) {
                                case '1'://水印图片
                                    $this->waterimg($localName,$localName,$config['watermark_img']);
                                    break;
                                case '2'://水印文字
                                    $this->watertext($localName,$localName,$config['watermark_text']);
                                    break;
                                case '3'://水印图片&文字
                                    $this->waterimg($localName,$localName,$config['watermark_img']);
                                    $this->watertext($localName,$localName,$config['watermark_text']);
                                    break;

                            }
                        }
                        $res=$this->add_file($filename.'.'.$type,$localName,1);
                        zy_array(true,'图片上传成功',$res,200,$isModule);
                    }else{
                        zy_array(false,'图片上传失败',"",-200,$isModule);
                    }
                }else{
                    //文件类型错误
                    zy_array(false,'图片上传类型错误',"",-1,$isModule);
                }
            }else{
                //文件错误
                zy_array(false,'文件格式错误',"",-2,$isModule);
            }
        }
    }

    /**
     * desription 压缩图片
     */
    function image_png_size_add($imgsrc,$imgdst='',$quality=75){
        if(!file_exists($imgsrc)){
            return false;
        }
        list($width,$height,$type)=getimagesize($imgsrc);
        $percent=$height/$width;
        $new_width = ($width>600?600:$width)*1;
        $new_height = $new_width*$percent;
        switch($type){
            case 1:
                $giftype=$this->check_gifcartoon($imgsrc);
                if($giftype){
                    //header('Content-Type:image/gif');
                    $image_wp = imagecreatetruecolor($new_width, $new_height);

                    /* --- 用以处理缩放gif和png图透明背景变黑色问题 开始 --- */
                    $color = imagecolorallocate($image_wp,255,255,255);
                    imagecolortransparent($image_wp,$color);
                    imagefill($image_wp,0,0,$color);
                    /* --- 用以处理缩放gif和png图透明背景变黑色问题 结束 --- */

                    $image = imagecreatefromgif($imgsrc);
                    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    //90代表的是质量、压缩图片容量大小
                    imagegif($image_wp, $imgdst,$quality);
                    imagedestroy($image_wp);
                    imagedestroy($image);
                }
                break;
            case 2:
                //header('Content-Type:image/jpeg');
                $image_wp=imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_wp, $imgdst,$quality);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
            case 3:
                //header('Content-Type:image/png');
                $image_wp = imagecreatetruecolor($new_width, $new_height);

                /* --- 用以处理缩放gif和png图透明背景变黑色问题 开始 --- */
                $color = imagecolorallocate($image_wp,255,255,255);
                imagecolortransparent($image_wp,$color);
                imagefill($image_wp,0,0,$color);
                /* --- 用以处理缩放gif和png图透明背景变黑色问题 结束 --- */

                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($image_wp, $imgdst,($quality/100*10));
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
        }
        return $imgdst;
    }

    /**
     * desription 判断是否gif动画
     */
    function check_gifcartoon($image_file){
        $fp = fopen($image_file,'rb');
        $image_head = fread($fp,1024);
        fclose($fp);
        return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head)?false:true;
    }

    /**
     * desription 添加到数据库
     */
    function add_file($filename,$path,$filetype,$des="图片"){
        $path2=substr(strchr($path, "uploadFile/"), 7);
        $data=[
            'filename'=>$filename,
            'filetype'=>$filetype,
            'filepath'=>$path,
            //'fileurl'=>$this->APP_PATH.$path,
            'fileurl'=>cmf_get_image_url($path2),
            'filedes'=>$des,
            'status'=>'1',
            'addtime'=>date("Y-m-d H:i:s",time())
        ];
        PluginFileModel::set($data);
        return $data;
    }

    /**
     * desription 添加到数据库
     */
    function status(){
        if(is_array(input('id'))){

        }else{
            $info=PluginFileModel::get_one(['id'=>input('id')]);
            if($info){
                PluginFileModel::update
                (['status'=>input('status')],['id'=>input('id')]);
                $this->success("更新成功");
            }
        }
    }

    /**
     * 删除图片文件
     */
    public function delete()
    {
        if(input('ids')){
            $ids = explode(",", input('ids'));
            foreach($ids as $key=>$value){
                $info=PluginFileModel::get_one(['id'=>$value]);
                if($info){
                    if(file_exists($info['filepath'])){
                        unlink($info['filepath']);
                    }
                    PluginFileModel::del($info['id']);
                }
            }
            return json(['type'=>'success','msg'=>'删除成功']);
            //$this->success("删除成功");
        }else{
            $info=PluginFileModel::get_one(['id'=>input('id')]);
            if($info){
                if(file_exists($info['filepath'])){
                    unlink($info['filepath']);
                }
                PluginFileModel::del(input('id'));
                $this->success("删除成功");
            }
        }
    }

    /**
     * 上传水印图片
     */
    function upload_water_img($isModule=false){
        //var_dump($_FILES);
        $config=$this->getConfig();
        if(!$_FILES){
            zy_array(false,"请上传图片","",-99,$isModule);
        }
        $img  = getimagesize($_FILES["file"]["tmp_name"]);//txt类型转换为jpg类型
        //$img  = exif_imagetype($_FILES["file"]["tmp_name"]);//判断是否是真实图像
        if(!$img){
            zy_array(false,"图片格式错误","",-14,$isModule);
        }

        if($_FILES["file"]["error"]!=0){
            zy_array(false,$_FILES["file"]["error"],"",0,$isModule);
        }

        if( !in_array($_FILES["file"]["type"], $config['upload_type'])){
            zy_array(false,$_FILES["file"]["type"],"",-1,$isModule);
        }

        if($_FILES["file"]["size"] > $config['upload_size']){//判断是否大于10M
            zy_array(false,'图片大小超过限制',"",-2,$isModule);
        }
        $filename = "water_img";
        $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $upload_path="plugins/file_manage/statics/img/";
        if(!file_exists($upload_path)){
            mkdir($upload_path,0777,true);
        }

        $localName = $upload_path.$filename.'.'.$ext;

        if ( move_uploaded_file($_FILES["file"]["tmp_name"], $localName) == true) {
            $lurl = $this->APP_PATH.$localName;
            $data=[
                'filename'=>$filename.'.'.$ext,
                'filetype'=>1,
                'filepath'=>$localName,
                'fileurl'=>$this->APP_PATH.$localName,
                'filedes'=>"图片",
                'status'=>'1',
                'addtime'=>date("Y-m-d H:i:s",time())
            ];
            zy_array(true,'图片上传成功',$data,200,$isModule);
        }else{
            zy_array(true,'图片上传失败',"",-200,$isModule);
        }
    }
}