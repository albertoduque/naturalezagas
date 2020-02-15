<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/bootstrap.treeview.css',
        'css/bootstrap-select.css',
        'css/jquery.fancybox.css',
        'css/datepicker.css',
        'css/jquery.confirm.css',
        'css/tool.css',
        'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons'
    ];
    public $js = [
        'js/generales.js',
        'js/bootstrap-treeview.js',
        'js/jquery.fancybox.js',
        'js/jquery.confirm.js',
        'js/jquery.numeric.js',
        'js/bootstrap-datepicker.js',
        'js/bootstrap-filestyle.min.js',
        'js/bootstrap-select.js',
        'https://use.fontawesome.com/3d906a6620.js',
        'js/tool.js',
        'js/jquery.maskMoney.js',
        'js/jquery.number.min.js',
        'https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
