<?php

namespace LwEvents\View\Helper;

class PrepareLogoWidthHelper
{
    public function __construct()
    {
    }
    
    public function execute($config, $entry)
    {
        list($width) = getimagesize($config['path']['resource'].'lw_events'."/".$entry->getValueByKey("opt1file"));
        if ($width < 170) {
            $div_width = 300 + (170 - $width);
        }
        else {
            $div_width = 300;
        }
        return $div_width;
    }
}
