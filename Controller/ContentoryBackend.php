<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 2013 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

namespace LwEvents\Controller;

class ContentoryBackend extends \LWmvc\Controller\Controller
{
    public function __construct($cmd, $oid)
    {
        parent::__construct($cmd, $oid);
        $this->request = \lw_registry::getInstance()->getEntry("request");
        $this->config = \lw_registry::getInstance()->getEntry("config");
    }
    
    public function execute()
    {
        /*
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'getConfigurationEntityById', array("id"=>$this->getContentObjectId()));
        $this->listConfig = $response->getDataByKey('ConfigurationEntity');
        if (!$this->listConfig) {
            die("List isn't configured!");
        }
        */
        
        $method = $this->getCommand()."Action";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        else {
            die("command doesn't exist");
        }
    }    

    protected function showFormAction($error = false)
    {
        if ($error) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'getConfigurationEntityFromArray', array(), array("postArray"=>$this->request->getPostArray()));
            $entity = $response->getDataByKey('ConfigurationEntity');
            $entity->setId($this->getContentObjectId());
        }
        else {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'getConfigurationEntityById', array("id"=>$this->getContentObjectId()));
            $entity = $response->getDataByKey('ConfigurationEntity');
            $entity->setId($this->getContentObjectId());
        }
        
        $formView = new \LwEvents\View\ConfigurationForm('edit');
        $formView->setEntity($entity);
        $formView->setErrors($error);
        return $this->returnRenderedView($formView);
    }    

    protected function saveAction()
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'save', array("id"=>$this->getContentObjectId()), array("postArray"=>$this->request->getPostArray()));
        if ($response->getParameterByKey("error")) {
            return $this->editFormAction($response->getDataByKey("error"));
        }
        return $this->buildReloadResponse(array("cmd"=>"showForm"));
    }
    
    protected function deleteAction()
    {
        return $this->buildDeleteAction('Configuration', $this->getContentObjectId());
    }

    protected function deleteListAction($error = false)
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getListEntriesCollection', array("configuration"=>$this->listConfig, "listId"=>$this->getContentObjectId(), "listRights"=>$this->listRights));
        $listEntriesCollection = $response->getDataByKey('listEntriesCollection');

        foreach($listEntriesCollection as $entry) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'delete', array("id"=>$entry->getValueByKey("id"), "listId"=>$this->getContentObjectId()));
        }
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'delete', array("id"=>$this->getContentObjectId()));
        return $response;
    }
    
    
}