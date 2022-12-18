<?php 
namespace Satrio\Scrapdata;
class Config{
    public function __construct()
    {
    $data = file(dirname(__FILE__).'/../.env',FILE_IGNORE_NEW_LINES);
foreach($data as $conf){
putenv($conf);
}
    }
}

return new Config;

