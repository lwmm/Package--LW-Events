<?php

namespace LwEvents\View;

class EntryDetail extends \LWmvc\View\View
{
    public function __construct($type)
    {
        parent::__construct($type);
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->view = new \lw_view(dirname(__FILE__).'/templates/EntryDetail.tpl.phtml');
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }    
    
    public function render()
    {
        if ($this->configuration->getValueByKey('language') == "de") {
            $this->view->lang = "de";
        }
        else {
            $this->view->lang = "en";
        }
        $this->view->usecss = $this->configuration->getValueByKey("usecss");
        return $this->view->render();
    }
}