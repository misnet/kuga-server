<?php
/**
 * 配置文件
 * 注意：如果config/config.php文件存在，则本文件内容可以被config.php覆盖
 * @author Donny
 */
define('CONFIG_DIR',__DIR__);

$_CONFIG['dbwrite'] = array(
    'adapter' =>'mysql',
    'port'=>3306,
    'host'=>'localhost',
    'username'=>'root',
    'password'=>'',
    'dbname'=>'',
    'charset'=>'utf8mb4',
);
$_CONFIG['dbread'] = $_CONFIG['dbwrite'];
$_CONFIG['weixin'] = array(
    'token'=>'',
    'appid'=>'',
    'appsecret'=>'',
    'notcheck'=>true
);
$_CONFIG['system']['charset']       = 'utf-8';
$_CONFIG['system']['locale']       = 'zh_CN';

//cache配置文件
$_CONFIG['cache'] = CONFIG_DIR.'/cache.json';

//用的队列程序
$_CONFIG['queue']['adapter'] = 'redis';

//权限资源配置文件
$_CONFIG['acc'] = CONFIG_DIR.'/acc.xml';



//API KEY配置文件
//$_CONFIG['apiKeys'] = CONFIG_DIR.'/apikeys.config.json';

//文件存储配置，具体用哪一个在env.php中
$_CONFIG['fileStorage']['adapter'] = 'aliyun'; //值为aliyun或localfile
$_CONFIG['fileStorage']['localfile'] = [
    'hostUrl'=>'',
    'baseDir'=>'data',
    'rootDir'=>'',
];
$_CONFIG['fileStorage']['aliyun'] = [
    'configFile'=>CONFIG_DIR.'/aliyunoss/config.json',
    'policyFile'=>CONFIG_DIR.'/aliyunoss/policy-all.txt'
    //,'serverEndConfigFile'=>CONFIG_DIR.'/aliyunoss/config.serverend.json'
];

//短信配置
$_CONFIG['sms']['adapter']  = 'aliyun'; //采用腾讯的，要用阿里云的，改为aliyun
$_CONFIG['sms']['tencent']  = CONFIG_DIR.'/sms/tencent.config.json';
$_CONFIG['sms']['aliyun']   = CONFIG_DIR.'/sms/aliyun.config.json';

$_CONFIG['email']  = CONFIG_DIR.'/aliemail.config.json';
//session配置
$_CONFIG['session']= CONFIG_DIR.'/session.config.json';

//jwt secret key
$_CONFIG['jwtTokenSecret'] = 'jwt token secret';

//是否开启记录API访问日志
$_CONFIG['apiLogEnabled'] = false;

//accessToken中用户标识的键值
$_CONFIG['accessTokenUserIdKey'] = 'uid';

//多域名配置
//当访问a.xxx.com和访问api.xxx.com一样效果
//$_CONFIG['domainMapping'] = [
//    'api'=>'a'
//];
//测试模式
$_CONFIG['testmodel'] = true;
//jwt secret key
$_CONFIG['jwtTokenSecret'] = 'jwt token secret';

$_CONFIG['apiLogEnabled'] = false;
//调试模式，非预见性错误或程序错误时，api会显示debug信息
$_CONFIG['debug']     = true;

$_CONFIG['app']['acc'] = [
    'apiUri'=>'https://acc.api.depoga.com/v3/gateway',
    'appKey' =>1000,
    'appSecret'=>'IsuZLMPJDVnwYp8XYp/Pf4HH6e5PY28c8oQy8akF5vWxMjvvSNORdPvDu6HK9eOAGcVmDk1jLRYIkAcGu7tgUQ=='
];


//是否开启记录API访问日志
$_CONFIG['apiLogEnabled'] = false;

//accessToken中用户标识的键值
$_CONFIG['accessTokenUserIdKey'] = 'uid';
return $_CONFIG;
