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

    public function setOldEntity($entity)
    {
        $this->view->oldEntity = $entity;
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
            $this->view->formtype = "new";
        }
        else {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"saveEntry", "id"=>$this->view->entity->getId()));
            $this->view->formtype = "edit";
        }
        
        $this->view->lang = $this->configuration->getValueByKey("language");
        if ($this->configuration->getValueByKey("usecss")) {
            $response = \lw_registry::getInstance()->getEntry('response');
            $response->addHeaderItems('css', file_get_contents(dirname(__FILE__) . '/css/EntryForm.css'));
        }
        $this->view->ValidationErrorViewHelper = new \LWmvc\View\Helper\ValidationErrorViewHelper($this->configuration->getValueByKey('language'));
        return $this->view->render();
    }
}
