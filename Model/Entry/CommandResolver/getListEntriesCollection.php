<?php

namespace LwEvents\Model\Entry\CommandResolver;

class getListEntriesCollection extends \LWmvc\Model\CommandResolver
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
        return new getListEntriesCollection($command);
    }
    
    public function resolve()
    {
        $conf = $this->command->getParameterByKey("configuration");
        $items = $this->getQueryHandler()->loadAllActualEntries($conf->getValueByKey("language"));
        $collection = \LWmvc\Model\EntityCollectionFactory::buildCollectionFromQueryResult($this->ObjectClass, $items);
        $this->command->getResponse()->setDataByKey('listEntriesCollection', $collection);
        return $this->command->getResponse();
    }
}