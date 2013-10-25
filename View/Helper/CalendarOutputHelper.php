<?php

namespace LwEvents\View\Helper;

class CalendarOutputHelper
{
    public function __construct()
    {
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->view = new \lw_view(dirname(__FILE__) . '/../templates/Calendar.tpl.phtml');
        
        $response = \lw_registry::getInstance()->getEntry('response');
        $response->addHeaderItems('cssfile', $this->systemConfiguration['url']['media'].'yui-calendar/css/fonts-min.css');
        $response->addHeaderItems('cssfile', $this->systemConfiguration['url']['media'].'yui-calendar/css/calendar.css');
        $response->addHeaderItems('jsfile', $this->systemConfiguration['url']['media'].'yui-calendar/js/yahoo-dom-event.js');
        $response->addHeaderItems('jsfile', $this->systemConfiguration['url']['media'].'yui-calendar/js/calendar-min.js');
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function execute($configuration, $collection)
    {
        $this->view->mediaUrl = $this->systemConfiguration['url']['media'];
        $this->view->collection = $collection;
        $this->view->lang = $configuration->getValueByKey("language");
        $this->view->targetid = $configuration->getValueByKey("targetid");
        
        return $this->view->render();
    }
}
