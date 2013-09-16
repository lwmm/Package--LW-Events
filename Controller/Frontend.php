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
        $this->response->useJQuery();
        $this->response->useJQueryUI();
    }
    
    public function setAdmin($bool)
    {
        if ($bool === true) {
            $this->admin = true;
        }
        else {
            $this->admin = false;
        }
    }
    
    public function isAdmin()
    {
        return $this->admin;
    }
    
    public function execute()
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Configuration', 'getConfigurationEntityById', array("id"=>$this->getContentObjectId()));
        $this->listConfig = $response->getDataByKey('ConfigurationEntity');

        if ($this->listConfig->getValueByKey("admin") == 1 || \lw_registry::getInstance()->getEntry("auth")->isLoggedIn()) {
            $this->setAdmin(true);
        }
        
        if ($this->listConfig->getValueByKey('teaserview') == 1) {
            return $this->showTeaserList();
        }
        
        $method = $this->getCommand()."Action";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        else {
            die("command ".$method." doesn't exist");
        }
    }    
    
    protected function getArchiveYear()
    {
        $year = $this->request->getInt("year");
        if (!$year) {
            $year = date("Y");
        }
        return $year;
    }
    
    protected function showListAction()
    {
        $view = new \LwEvents\View\EntryList();
        $view->setConfiguration($this->listConfig);

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getListEntriesCollection', array("configuration"=>$this->listConfig));
        $view->setCollection($response->getDataByKey('listEntriesCollection'));

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getIsDeletableSpecification');
        $view->setIsDeletableSpecification($response->getDataByKey('isDeletableSpecification'));
        
        return $this->returnRenderedView($view);    
    }
    
    protected function showTeaserList() 
    {
        $view = new \LwEvents\View\TeaserList();
        $view->setConfiguration($this->listConfig);

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getListEntriesCollection', array("configuration"=>$this->listConfig));
        $view->setCollection($response->getDataByKey('listEntriesCollection'));

        return $this->returnRenderedView($view);    
    }    
    
    protected function showArchiveAction()
    {
        $view = new \LwEvents\View\ArchiveList();
        $view->setConfiguration($this->listConfig);
        $view->setArchiveYear($this->getArchiveYear());
        
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getArchivedListEntriesCollection', array("configuration"=>$this->listConfig, "year"=>$this->getArchiveYear()));
        $view->setCollection($response->getDataByKey('listEntriesCollection'));

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getAllAvailableYearsArray', array("configuration"=>$this->listConfig));
        $view->setAvailableYearsArray($response->getDataByKey('availableYearsArray'));

        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getIsDeletableSpecification');
        $view->setIsDeletableSpecification($response->getDataByKey('isDeletableSpecification'));
        
        return $this->returnRenderedView($view);    
    }
     
    protected function showDetailAction()
    {
        $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->getContentObjectId()));
        $entity = $response->getDataByKey('EntryEntity');
        $entity->setId($this->request->getInt("id"));

        $view = new \LwEvents\View\EntryDetail();
        $view->setEntity($entity);
        $view->setConfiguration($this->listConfig);
        return $this->returnRenderedView($view);
    }
     
    protected function addEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'add', array("configuration" => $this->listConfig, "userId"=>\lw_in_auth::getInstance()->getUserdata("id")), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file'), 'opt2file'=>$this->request->getFileData('opt2file')));
            if ($response->getParameterByKey("error")) {
                return $this->showAddFormAction($response->getDataByKey("error"));
            }
            return $this->buildReloadResponse(array("cmd"=>"showList"));
        }
     }

     protected function showEditEntryFormAction($error=false)
     {
        if ($this->isAdmin()) {
            $formView = new \LwEvents\View\EntryForm('edit');

            if ($error) {
                $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->getContentObjectId()));
                $formView->setOldEntity($response->getDataByKey('EntryEntity'));
                $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityFromPostArray', array(), array("postArray"=>$this->request->getPostArray()));
            }
            else {
                $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->getContentObjectId()));
                $formView->setOldEntity($response->getDataByKey('EntryEntity'));
            }
            $entity = $response->getDataByKey('EntryEntity');
            $entity->setId($this->request->getInt("id"));
            
            $formView->setEntity($entity);
            $formView->setConfiguration($this->listConfig);
            $formView->setErrors($error);
            return $this->returnRenderedView($formView);
        }
    }
     
    protected function saveEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'save', array("id"=>$this->request->getInt("id"), "configuration" => $this->listConfig, "userId"=>\lw_in_auth::getInstance()->getUserdata("id")), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file')));
            if ($response->getParameterByKey("error")) {
                return $this->showEditEntryFormAction($response->getDataByKey("error"));
            }
            return $this->buildReloadResponse(array("cmd"=>"showEditEntryForm", "id" => $this->request->getInt("id")));
        }
    }
     
    protected function showAddFormAction($error=false)
    {
        if ($this->isAdmin()) {
            $formView = new \LwEvents\View\EntryForm("add");
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'getEntryEntityFromPostArray', array(), array("postArray"=>$this->request->getPostArray()));
            $formView->setConfiguration($this->listConfig);
            $formView->setEntity($response->getDataByKey('EntryEntity'));
            $formView->setOldEntity($response->getDataByKey('EntryEntity'));
            $formView->setErrors($error);
            return $this->returnRenderedView($formView);
        }
    }
     
    protected function deleteLogoAction()
    {
        if ($this->isAdmin()) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'deleteLogo', array("id"=>$this->request->getInt("id")));
            return $this->buildReloadResponse(array("cmd"=>"showEditEntryForm", "id" => $this->request->getInt("id")));
        }
    }

    protected function deleteEntryAction()
    {
        if ($this->isAdmin()) {
            $response = \LWmvc\Model\CommandDispatch::getInstance()->execute('LwEvents', 'Entry', 'delete', array("id"=>$this->request->getInt("id")));
            return $this->buildReloadResponse(array("cmd"=>"showList"));
        }
    }
}