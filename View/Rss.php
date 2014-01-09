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

class Rss extends \LWmvc\View\View
{
    public function __construct()
    {
        parent::__construct('edit');
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
    }

    public function old__setListId($id)
    {
        $this->listId = $id;
    }

    public function render()
    {
        $PrepareEntryTextAndMoreLinkHelper = new \LwEvents\View\Helper\PrepareEntryTextAndMoreLinkHelper();
        
        $data['title']          = utf8_encode("Events");
        $data['link']           = utf8_encode(str_replace("&", urlencode("&"), \lw_page::getInstance()->getUrl()));
        $data['description']    = "Events / Veranstaltungen";
        $data['copyright']      = "PtJ";
        $data['pubdate']        = date('YmdHis');
        $data['author']         = "PtJ";
        $data['entries']        = array();
        
	    if ($this->view->collection->count() > 0)
	    {
	        foreach($this->view->collection as $entry)
	        {
		        $linksandmore = $PrepareEntryTextAndMoreLinkHelper->execute($entry);
		        $array['title']         = "<![CDATA[".html_entity_decode(utf8_encode(substr($entry->getValueByKey("opt1text"), 0, 255)))."]]>";
		        $array['description']   = "<![CDATA[".utf8_encode($linksandmore['text'])."]]>";
		        $array['link']          = str_replace("&", urlencode("&"), $linksandmore['targeturl']);
		        $array['id']            = $entry->getValueByKey("id");
		        $data['entries'][]      = $array;
	        }
	    }
	    $feed = new \lw_feed($data);
	    $feed->createFeed("rss20");
	    header("Content-Type: application/xml; charset=UTF-8");
	    echo $feed->getFeed();
	    exit();          
        
    }
}
