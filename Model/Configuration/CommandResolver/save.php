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

namespace LwEvents\Model\Configuration\CommandResolver;

class save extends \LWmvc\Model\CommandResolver
{
    protected $command;

    public function __construct($command)
    {
        parent::__construct($command);
        $this->baseNamespace = "\\LwEvents\\Model\\Configuration\\";
        $this->ObjectClass = $this->baseNamespace . "Object\\configuration";
    }

    public function getInstance($command)
    {
        return new save($command);
    }

    public function resolve()
    {
        try {
            $dataValueObject = new \LWmvc\Model\DTO($this->command->getDataByKey('postArray'));
            $parameter['language'] = $dataValueObject->getValueByKey("language");
            $parameter['teaserview'] = $dataValueObject->getValueByKey("teaserview");
            $parameter['teaserelements'] = $dataValueObject->getValueByKey("teaserelements");
            $parameter['calendar'] = $dataValueObject->getValueByKey("calendar");
            $parameter['usecss'] = $dataValueObject->getValueByKey("usecss");
            $parameter['adminmode'] = $dataValueObject->getValueByKey("adminmode");
            $parameter['useical'] = $dataValueObject->getValueByKey("useical");
            $content = false;
            $result = $this->getCommandHandler()->savePluginData($this->command->getParameterByKey('id'), $parameter, $content);
            $this->command->getResponse()->setParameterByKey('saved', true);
        } catch (\LWmvc\Model\validationErrorsException $e) {
            $this->command->getResponse()->setDataByKey('error', $e->getErrors());
            $this->command->getResponse()->setParameterByKey('error', true);
        }
        return $this->command->getResponse();
    }

}
