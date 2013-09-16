<?php

namespace LwEvents\Model\Entry\CommandResolver;

class delete extends \LWmvc\Model\CommandResolver
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
        return new delete($command);
    }
    
    public function resolve()
    {
        $config = $this->dic->getConfiguration();
        $this->getCommandHandler()->setFilePath($config['path']['resource'].'lw_events/');
        $ok = $this->getCommandHandler()->deleteEntity($this->command->getParameterByKey("id"));
        if ($ok) {
            $this->command->getResponse()->setParameterByKey('deleted', true);
        }
        else {
            $this->command->getResponse()->setDataByKey('error', 'error deleting');
            $this->command->getResponse()->setParameterByKey('error', true);
        }                    
        return $this->command->getResponse();
    }
}