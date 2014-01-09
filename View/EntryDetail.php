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
        $this->view->PrepareLogoWidthHelper = new \LwEvents\View\Helper\PrepareLogoWidthHelper();
        $this->view->PrepareEventDateOutputHelper = new \LwEvents\View\Helper\PrepareEventDateOutputHelper();
        $this->view->PrepareGetBackUrlFromDetailHelper = new \LwEvents\View\Helper\PrepareGetBackUrlFromDetailHelper();
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }    
    
    public function render()
    {
        $this->view->lang = $this->configuration->getValueByKey("language");
        $this->view->ical = $this->configuration->getValueByKey("useical");
        if ($this->configuration->getValueByKey("usecss")) {
            $response = \lw_registry::getInstance()->getEntry('response');
            $response->addHeaderItems('css', file_get_contents(dirname(__FILE__) . '/css/EntryDetail.css'));
        }
        return $this->view->render();
    }
}
