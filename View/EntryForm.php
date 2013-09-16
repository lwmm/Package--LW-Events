<?php

namespace LwEvents\View;

class EntryForm extends \LWmvc\View\View
{
    public function __construct($type)
    {
        parent::__construct($type);
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $LwResponse = $this->dic->getLwResponse();
        $LwResponse->useJQuery();
        $LwResponse->useJQueryUI();
        $LwResponse->addHeaderItems("jsfile", $this->systemConfiguration['url']['media'].'tinymce3_5_8/jscripts/tiny_mce/tiny_mce.js');
        $this->view = new \lw_view(dirname(__FILE__).'/templates/EntryForm.tpl.phtml');
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }    
    
    public function render()
    {
        $this->view->mediaUrl = $this->systemConfiguration['url']['media'];
        if ($this->view->entity->getId()<1) {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addEntry"));
            $this->view->formTitle = "New Date";
        }
        else {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"saveEntry", "id"=>$this->view->entity->getId()));
            $this->view->formTitle = "Edit Date";
        }
        
        $this->view->lang = $this->configuration->getValueByKey("language");
        $this->view->usecss = $this->configuration->getValueByKey("usecss");
        $this->view->ValidationErrorViewHelper = new \LWmvc\View\Helper\ValidationErrorViewHelper($this->configuration->getValueByKey('language'));
        
        //echo "<pre>";print_r($this->view->errors);exit();
        
        return $this->view->render();
    }
}
