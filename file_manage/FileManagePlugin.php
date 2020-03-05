<?php
//模块的主类
namespace plugins\file_manage;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;//必须引入此库
/**Demo插件英文名，改成你的插件英文就行了 继承此库
 * @pluginInfo('name'=>'附件模块','symbol'=>'FileManage')
 */
class FileManagePlugin extends Plugin
{
    /**
     * 插件基本信息
     * @var [type]
     */
    public $info = [
        'name'        => 'FileManage',//Demo插件英文名，改成你的插件英文就行了
        'title'       => '附件模块',
        'description' => '附件模块(图片，视频，其他文件上传)',
        'status'      => 1,
        'author'      => '王浩力',
        'version'     => '1.0',
    ];

    public $hasAdmin = 1;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

}