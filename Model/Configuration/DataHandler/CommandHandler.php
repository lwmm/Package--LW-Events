<?php

namespace LwEvents\Model\Configuration\DataHandler;

class CommandHandler extends \LWmvc\Model\DataCommandHandler
{
    public function __construct($db)
    {
        $this->dic = new \LwEvents\Services\dic();
        $this->setPluginRepository($this->dic->getPluginRepository());
    }

    public function setPluginRepository($pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;
    }
    
    public function savePluginData($id, $parameter, $content)
    {
        return $this->pluginRepository->savePluginData('lw_events', $id, $parameter, $content);
    }
    
    public function deletePluginData($id)
    {
        return $this->pluginRepository->deleteEntryByContainer($id);
    }
}