<?php

namespace LwEvents\Model\Entry\Specification;

define("LW_REQUIRED_ERROR", "1");
define("LW_MAXLENGTH_ERROR", "2");
define("LW_BOOL_ERROR", "3");
define("LW_FILETOOBIG_ERROR", "4");
define("LW_WHITELIST_ERROR", "5");
define("LW_BLACKLIST_ERROR", "6");

class isValid extends \LWmvc\Model\Validator
{
    public function __construct()
    {
        $this->allowedKeys = array(
                "id",
                "opt2number",
                "opt1bool",
                "opt1text",
                "opt2text",
                "opt3text",
                "opt1file");
        
        $mvalue = ini_get("upload_max_filesize");
        if (intval($mvalue) > intval(ini_get("post_max_size"))) $mvalue = ini_get("post_max_size");
        
        if(substr($mvalue, -1, 1)=="M") {
            $mvalue = substr($mvalue, 0, strlen($mvalue)-1)*1024*1024;
        }
        elseif(substr($mvalue, -1, 1)=="K") {
            $mvalue = substr($mvalue, 0, strlen($mvalue)-1)*1024;
        }
        elseif(substr($mvalue, -1, 1)=="G") {
            $mvalue = substr($mvalue, 0, strlen($mvalue)-1)*1024*1024*1024;        
        }
        $this->maxfilesize = $mvalue;
    }
    
    static public function getInstance()
    {
        return new isValid();
    }
    
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function isSatisfiedBy(\LwEvents\Model\Entry\Object\entry $object)
    {
        $valid = true;
        foreach($this->allowedKeys as $key){
            $method = $key."Validate";
            if (method_exists($this, $method)) {
                $result = $this->$method($key, $object);
                if($result == false){
                    $valid = false;
                }
            }
        }
        return $valid;
    }

    public function opt1textValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
               
        if (!$value) {
            $this->addError($key, LW_REQUIRED_ERROR);
            return false;
        }
        
        $maxlength = 255;
        if (!$this->hasMaxlength($value, array("maxlength"=>$maxlength)) ) {
            $this->addError($key, LW_MAXLENGTH_ERROR, array("maxlength"=>$maxlength));
            return false;
        }
        return true;
    }
    
  
}