<?php

namespace LwEvents\Model\Configuration\DataHandler;

class QueryHandler extends \LWmvc\Model\DataQueryHandler
{
    public function __construct(\lw_db $db)
    {
        $this->db = $db;
        $this->dic = new \LwEvents\Services\dic();
        $this->setPluginRepository($this->dic->getPluginRepository());
    }
    
    public function setPluginRepository($pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;
    }
    
    public function loadObjectById($id)
    {
        $data = $this->pluginRepository->loadPluginData('lw_events', $id);
        return $data['parameter'];
    }
}