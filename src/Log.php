<?php 
namespace Satrio\Scrapdata;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
require_once dirname(__FILE__)."/Config.php";
class Log extends Logger {

}

$log = new Logger('Log');
$level = match(getenv('LOGLEVEL')){
'WARNING'=> Logger::WARNING,
'INFO'=> Logger::INFO,
'DEBUG'=> Logger::DEBUG,
};
$log->pushHandler(new StreamHandler(dirname(__FILE__).'/../'.getenv("LOGNAME")),$level);
return $log;