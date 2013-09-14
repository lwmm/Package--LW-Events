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

class Frontend extends \LWmvc\Controller\Controller
{
    public function __construct($cmd, $oid)
    {
        parent::__construct($cmd, $oid);
        $this->dic = new \LwEvents\Services\dic();
        $this->response = \lw_registry::getInstance()->getEntry("response");
        $this->request = \lw_registry::getInstance()->getEntry("request");
        $this->config = $this->dic->getConfiguration();
    }
    
    public function execute()
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'getConfigurationEntityById', array("id"=>$this->getContentObjectId()));
        $this->listConfig = $response->getDataByKey('ConfigurationEntity');

        $method = $this->getCommand()."Action";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        else {
            die("command ".$method." doesn't exist");
        }
    }    
    
    protected function showListAction($error = false)
    {
        $this->response->useJQuery();
        $this->response->useJQueryUI();

        $view = new \LwEvents\View\EntryList();
        $view->setConfiguration($this->listConfig);
        $view->setListId($this->getContentObjectId());

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getListEntriesCollection', array("configuration"=>$this->listConfig, "listId"=>$this->getContentObjectId(), "listRights"=>$this->listRights));
        $view->setCollection($response->getDataByKey('listEntriesCollection'));

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getIsDeletableSpecification');
        $view->setIsDeletableSpecification($response->getDataByKey('isDeletableSpecification'));
        
        return $this->returnRenderedView($view);    
     }
     
     protected function addEntryAction()
     {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'add', array("configuration" => $this->listConfig, "userId"=>\lw_in_auth::getInstance()->getUserdata("id")), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file'), 'opt2file'=>$this->request->getFileData('opt2file')));
        if ($response->getParameterByKey("error")) {
            if ($this->request->getAlnum("type") == "file") {
                return $this->showAddFileFormAction($response->getDataByKey("error"));
            } 
            else {
                return $this->showAddLinkFormAction($response->getDataByKey("error"));
            }
        }
        return $this->buildReloadResponse(array("cmd"=>"showList"));
     }

     protected function showEditEntryFormAction($error=false)
     {
        if ($error) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityFromPostArray', array(), array("postArray"=>$this->request->getPostArray()));
        }
        else {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->getContentObjectId()));
        }
        $entity = $response->getDataByKey('EntryEntity');
        $entity->setId($this->request->getInt("id"));
        
        $formView = new \LwEvents\View\EntryForm('edit');
        $formView->setEntity($entity);
        $formView->setConfiguration($this->listConfig);
        $formView->setErrors($error);
        return $this->returnRenderedView($formView);
     }
     
    protected function saveEntryAction()
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'save', array("id"=>$this->request->getInt("id"), "configuration" => $this->listConfig, "userId"=>\lw_in_auth::getInstance()->getUserdata("id")), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file')));
        if ($response->getParameterByKey("error")) {
            return $this->showEditEntryFormAction($response->getDataByKey("error"));
        }
        return $this->buildReloadResponse(array("cmd"=>"showList"));
    }
    
    protected function deleteEntryThumbnailAction()
    {
       if ($this->listRights->isWriteAllowed()) {
           $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'deleteThumbnail', array("id"=>$this->request->getInt("id")), array());
           if ($response->getParameterByKey("error")) {
               return $this->showEditEntryFormAction($response->getDataByKey("error"));
           }
           return $this->buildReloadResponse(array("cmd"=>"showList", "reloadParent"=>1));
       }
    }
     
    protected function showAddFormAction($error=false)
    {
        $formView = new \LwEvents\View\EntryForm("add");
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityFromPostArray', array(), array("postArray"=>$this->request->getPostArray()));
        $formView->setConfiguration($this->listConfig);
        $formView->setEntity($response->getDataByKey('EntryEntity'));
        $formView->setErrors($error);
        return $this->returnRenderedView($formView);
    }
     
    protected function deleteLogoAction()
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'deleteLogo', array("id"=>$this->request->getInt("id")));
        return $this->buildReloadResponse(array("cmd"=>"showList"));
    }

    protected function deleteEntryAction()
    {
       if ($this->listRights->isWriteAllowed()) {
           $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'delete', array("id"=>$this->request->getInt("id")));
           return $this->buildReloadResponse(array("cmd"=>"showList"));
       }
    }
}
