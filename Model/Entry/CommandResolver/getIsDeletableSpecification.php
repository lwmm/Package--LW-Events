<?php

namespace LwEvents\Model\Entry\CommandResolver;

class getIsDeletableSpecification extends \LWmvc\Model\CommandResolver
{
    public function __construct($command)
    {
        parent::__construct($command);
        $this->dic = new \LwEvents\Services\dic();
        $this->baseNamespace = "\\LwEvents\\Model\\Entry\\";
    }
    
    public function getInstance($command)
    {
        return new getIsDeletableSpecification($command);
    }
    
    public function resolve()
    {
        $class = $this->baseNamespace.'Specification\isDeletable';
        $this->command->getResponse()->setDataByKey('isDeletableSpecification', $class::getInstance());
        return $this->command->getResponse();
    }
}