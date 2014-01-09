<?php

/* * ************************************************************************
 *  Copyright notice
 *
 *  Copyright 2014 Logic Works GmbH
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

class Ical extends \LWmvc\View\View
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function render()
    {
        if ($this->entity) {
            $values = $this->entity->getValues();
        } elseif ($this->entities) {
            foreach ($this->entities as $entity) {
                $values[] = $entity->getValues();
            }
        }
        if (count($values) > 0) {
            $cal = new \LwEvents\Services\spn_ical();
            if ($this->entity) {
                $item = $cal->createAndAddItem();
                $item->assignDbRow($this->prepareValues($values));
            } elseif ($this->entities) {
                foreach ($values as $value) {
                    $item = $cal->createAndAddItem();
                    $item->assignDbRow($this->prepareValues($value));
                }
            }
            $cal->sendAsFile();
            exit();
        }
    }

    protected function prepareValues($values)
    {
        $array = array();
        $array["title"] = $values["opt1text"]; #ueberschrift
        $array["archivedate"] = $values["opt2number"]; #startdatum
        $array["todate"] = $values["opt4number"]; #enddatum

        $array["location"] = $values["opt2text"]; #ort

        $array["firstdate"] = $values["lw_first_date"]; #erstelldatum
        $array["lastdate"] = $values["lw_last_date"]; #letzte aenderung

        return $array;
    }

}
