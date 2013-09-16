<?php

namespace LwEvents\Model\Entry\Specification;

define("LW_REQUIRED_ERROR", "1");
define("LW_MAXLENGTH_ERROR", "2");
define("LW_BOOL_ERROR", "3");
define("LW_FILETOOBIG_ERROR", "4");

class isValid extends \LWmvc\Model\Validator
{
    public function __construct()
    {
        $this->allowedKeys = array(
                "id",
                "opt1number",
                "opt2number",
                "opt1bool",
                "opt1text",
                "opt2text",
                "opt3text",
                "opt1file",
                "opt1clob",
                "opt2clob");
        
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

    public function opt1fileValidate($key, $object)
    {
        $ok = true;
        $array = $object->getValueByKey($key);
        if (!$array['name']) {
            return true;
        }
        if ($array['size'] > $this->maxfilesize) {
            $this->addError($key, "lwmvc_17", array("maxsize"=> $this->maxfilesize, "actualsize"=>$array['size']));
            $ok = false;
        }
        $extlist = '.jpg,.jpeg,.gif,.png';
        $ext = \lw_io::getFileExtension($array['name']);
        $extarray_u = explode(",", $extlist);
        foreach($extarray_u as $singleext) {
            $extarray[] = strtolower(trim($singleext));
        }
        if (!in_array('.'.strtolower($ext), $extarray)) {
            $this->addError($key, "lwmvc_12");
            $ok = false;
        }
        return $ok;
    }
        
    public function opt1textValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
               
        if (!$value) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        
        $maxlength = 255;
        if (!$this->hasMaxlength($value, array("maxlength"=>$maxlength)) ) {
            $this->addError($key, 'lwmvc_2', array("maxlength"=>$maxlength));
            return false;
        }
        return true;
    }
    
    public function opt2textValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
               
        if (!$value) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        
        $maxlength = 255;
        if (!$this->hasMaxlength($value, array("maxlength"=>$maxlength)) ) {
            $this->addError($key, 'lwmvc_2', array("maxlength"=>$maxlength));
            return false;
        }
        return true;
    }

    public function opt2numberValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
               
        if (!$value) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        
        $maxlength = 8;
        if (!$this->hasMaxlength($value, array("maxlength"=>$maxlength)) ) {
            $this->addError($key, 'lwmvc_2', array("maxlength"=>$maxlength));
            return false;
        }
        return true;
    }
    
    public function opt1clobValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
        if (!$value && $object->getValueByKey('opt5number') == 1) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        return true;
    }

    public function opt2clobValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
        if (!$value && ($object->getValueByKey('opt5number') == 2 || $object->getValueByKey('opt5number') == 3)) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        return true;
    }

    public function opt1numberValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
        if (!$value && $object->getValueByKey('opt5number') == 2) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        return true;
    }
    
    public function opt3textValidate($key, $object)
    {
        $value = trim($object->getValueByKey($key));
        if (!$value && $object->getValueByKey('opt5number') == 3) {
            $this->addError($key, 'lwmvc_4');
            return false;
        }
        return true;
    }    
}
