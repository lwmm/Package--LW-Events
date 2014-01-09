<?php

namespace LwEvents\View;

class ConfigurationForm extends \LWmvc\View\View
{
    public function __construct($type)
    {
        parent::__construct('edit');
        $this->dic = new \LwEvents\Services\dic();
        $this->view = new \lw_view(dirname(__FILE__).'/templates/ConfigurationForm.tpl.phtml');
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->response = new \LwEvents\Model\Configuration\Service\Response();
    }

    public function render()
    {        
        $this->view->actionUrl = $this->systemConfiguration['url']['client']."admin.php?obj=content&cmd=open&oid=".$this->view->entity->getId()."&pcmd=save";
        $this->view->backUrl = $this->systemConfiguration['url']['client']."admin.php?obj=content";
        $this->view->bootstrapCSS = $this->systemConfiguration["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $this->view->bootstrapJS  = $this->systemConfiguration["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        
        return $this->view->render();    
    }
}
