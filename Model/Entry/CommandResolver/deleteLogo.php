<?php

namespace LwEvents\Model\Entry\CommandResolver;

class deleteLogo extends \LWmvc\Model\CommandResolver
{
    public function __construct($command)
    {
        parent::__construct($command);
        $this->dic = new \LwListtool\Services\dic();
        $this->baseNamespace = "\\LwEvents\\Model\\Entry\\";
        $this->ObjectClass = $this->baseNamespace."Object\\entry";
    }
    
    public function getInstance($command)
    {
        return new deleteLogo($command);
    }
    
    public function resolve()
    {
        $config = $this->dic->getConfiguration();
        $this->getCommandHandler()->setFilePath($config['path']['resource']."lw_events/");
        $logoId = $this->command->getParameterByKey("id");

        $ok = $this->getCommandHandler()->deleteLogo($logoId);
        if ($ok) {
            $this->command->getResponse()->setParameterByKey('logo deleted', true);
        }
        else {
            $this->command->getResponse()->setDataByKey('error', 'error deleting logo');
            $this->command->getResponse()->setParameterByKey('error', true);
        }                    
        return $this->command->getResponse();
    }
}