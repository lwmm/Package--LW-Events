<?php

namespace LwEvents\View\Helper;

class PrepareEventDateOutputHelper
{
    public function __construct()
    {
    }
    
    public function execute($entry)
    {
        $date2 = "";
        if ($entry->getValueByKey("opt4number") != $entry->getValueByKey("opt2number")) {
            $date2 = ' - '.\lw_object::formatDate($entry->getValueByKey('opt4number'));
        }
        return \lw_object::formatDate($entry->getValueByKey('opt2number')).$date2;
    }
}
