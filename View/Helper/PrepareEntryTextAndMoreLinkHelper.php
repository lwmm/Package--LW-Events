<?php

namespace LwEvents\View\Helper;

class PrepareEntryTextAndMoreLinkHelper
{
    public function __construct()
    {
    }
    
    public function execute($entry)
    {
        if ($entry->getValueByKey("opt5number") == 3) {
            $array['text'] = $entry->getValueByKey("opt2clob");
            $array['targeturl'] = $entry->getValueByKey("opt3text");
            $array['class'] = "extern";
        }

        if ($entry->getValueByKey("opt5number") == 2) {
            $array['text'] = $entry->getValueByKey("opt2clob");
            $array['targeturl'] = \lw_page::getInstance($entry->getValueByKey("opt1number"))->getUrl();
            $array['class'] = "intern";
        }

        if ($entry->getValueByKey("opt5number") == 1) {
            if (strlen($entry->getValueByKey("opt1clob")) > 250) {
                $array['text'] = substr(strip_tags($entry->getValueByKey("opt1clob")), 0, 250).'...';
            }
            else {
                $array['text'] = $entry->getValueByKey("opt1clob");
            }
            $array['targeturl'] = \lw_page::getInstance()->getUrl(array("cmd"=>"showDetail", "id"=>$entry->getValueByKey("id")));
            $array['class'] = "intern";
        }
        return $array;
    }
}
