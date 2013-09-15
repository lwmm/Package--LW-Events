<?php

namespace LwEvents\Model\Entry\CommandResolver;

class getAllAvailableYearsArray extends \LWmvc\Model\CommandResolver
{
    
    public function __construct($command)
    {
        parent::__construct($command);
        $this->dic = new \LwEvents\Services\dic();
        $this->baseNamespace = "\\LwEvents\\Model\\Entry\\";
        $this->ObjectClass = $this->baseNamespace."Object\\entry";
    }
    
    public function getInstance($command)
    {
        return new getAllAvailableYearsArray($command);
    }
    
    public function resolve()
    {
        $conf = $this->command->getParameterByKey("configuration");
        $items = $this->getQueryHandler()->loadAllAvailableYearsByLanguage($conf->getValueByKey("language"));
        $this->command->getResponse()->setDataByKey('availableYearsArray', $items);
        return $this->command->getResponse();
    }
}