<?php

/* * ************************************************************************
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
 * ************************************************************************* */

namespace LwEvents\View;

class EntryList extends \LWmvc\View\View
{
    public function __construct()
    {
        parent::__construct('edit');
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->auth = $this->dic->getLwAuth();
        $this->inAuth = $this->dic->getLwInAuth();
        $this->view = new \lw_view(dirname(__FILE__) . '/templates/EntryList.tpl.phtml');
        $this->view->PrepareEntryTextAndMoreLinkHelper = new \LwEvents\View\Helper\PrepareEntryTextAndMoreLinkHelper();
        $this->view->PrepareEventDateOutputHelper = new \LwEvents\View\Helper\PrepareEventDateOutputHelper();
        $this->view->PrepareLogoWidthHelper = new \LwEvents\View\Helper\PrepareLogoWidthHelper();
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    public function isAdmin()
    {
        return $this->admin;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function setListId($id)
    {
        $this->listId = $id;
    }

    public function render()
    {
        $this->view->addUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"showAddForm"));
        if ($this->isAdmin()) {
            $this->view->admin = true;
        }
        $this->view->configuration = $this->configuration;
        $this->view->lang = $this->configuration->getValueByKey("language");
        if ($this->configuration->getValueByKey("usecss")) {
            $response = \lw_registry::getInstance()->getEntry('response');
            $response->addHeaderItems('css', file_get_contents(dirname(__FILE__) . '/css/EntryList.css'));
        }
        return $this->view->render();
    }
}
