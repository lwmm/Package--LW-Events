<?php

namespace LwEvents\Model\Entry\Object;

class entry extends \LWmvc\Model\Entity
{
    public function __construct($id=false)
    {
        parent::__construct($id);
        $this->dic = new \LwEvents\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->path = $this->systemConfiguration['path']['listtool'];
    }
    
    public function renderView($view)
    {
        $view->entity = $this;
    }
    
    public function getFirstDate()
    {
        $date = substr($this->getValueByKey('lw_first_date'), 0, 8);
        return \lw_object::formatDate($date);
    }
    
    public function getFirstTime()
    {
        $hour = substr($this->getValueByKey('lw_first_date'), 8, 2);
        $min = substr($this->getValueByKey('lw_first_date'), 10, 2);
        $sec = substr($this->getValueByKey('lw_first_date'), 12, 2);
        
        return $hour.':'.$min.':'.$sec;
    }
    
    public function getLastDate()
    {
        $date = substr($this->getValueByKey('lw_last_date'), 0, 8);
        return \lw_object::formatDate($date);
    }
    
    public function getLastTime()
    {
        $hour = substr($this->getValueByKey('lw_last_date'), 8, 2);
        $min = substr($this->getValueByKey('lw_last_date'), 10, 2);
        $sec = substr($this->getValueByKey('lw_last_date'), 12, 2);
        
        return $hour.':'.$min.':'.$sec;
    }
    
    public function getFirstUserName()
    {
        $db = $this->dic->getDbObject();
        $result = $db->select1("SELECT name FROM ".$db->gt("lw_in_user")." WHERE id = ".intval($this->getValueByKey('lw_first_user')));
        return $result['name'];
    }
    
    public function getLastUserName()
    {
        $db = $this->dic->getDbObject();
        $result = $db->select1("SELECT name FROM ".$db->gt("lw_in_user")." WHERE id = ".intval($this->getValueByKey('lw_last_user')));
        return $result['name'];
    }
}
