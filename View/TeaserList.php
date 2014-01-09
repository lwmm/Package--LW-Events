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

class TeaserList extends \LWmvc\View\View
{

    public function __construct()
    {
        parent::__construct('edit');
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->view = new \lw_view(dirname(__FILE__) . '/templates/TeaserList.tpl.phtml');
        $this->view->PrepareEventDateOutputHelper = new \LwEvents\View\Helper\PrepareEventDateOutputHelper();
        $this->view->CalendarOutputHelper = new \LwEvents\View\Helper\CalendarOutputHelper();
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function render()
    {
        if ($this->configuration->getValueByKey("usecss")) {
            $response = \lw_registry::getInstance()->getEntry('response');
            $response->addHeaderItems('css', file_get_contents(dirname(__FILE__) . '/css/TeaserList.css'));
        }
        $this->view->configuration = $this->configuration;
        $this->view->lang = $this->configuration->getValueByKey("language");
        $this->view->calendar = $this->configuration->getValueByKey("calendar");
        $this->view->teaserelements = $this->configuration->getValueByKey("teaserelements");
        $this->view->mediaUrl = $this->systemConfiguration['url']['media'];
        $this->view->baseUrlWithoutIndex = $this->systemConfiguration['url']['client'].'index.php?index=';
        return $this->view->render();
    }
}
