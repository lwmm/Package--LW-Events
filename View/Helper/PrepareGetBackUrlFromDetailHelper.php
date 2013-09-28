<?php

namespace LwEvents\View\Helper;

class PrepareGetBackUrlFromDetailHelper
{
    public function __construct()
    {
    }
    
    public function execute($entry)
    {
        if ($entry->getValueByKey("opt4number") < date("Ymd") && $entry->getValueByKey("opt4number")) {
            return \lw_page::getInstance()->getUrl(array("cmd"=>"showArchive", "year" => substr($entry->getValueByKey("opt2number"), 0, 4))); 
        }
        else {
            return \lw_page::getInstance()->getUrl(array("cmd"=>"showList")); 
        }
    }
}
