<?php
//error_reporting(0);
// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', false);
//defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
require(__DIR__ . '/yii/vendor/autoload.php');
require(__DIR__ . '/yii/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/yii/config/web.php');

(new yii\web\Application($config))->run();
//donal