<?php

namespace LwEvents\Model\Entry\DataHandler;

class CommandHandler extends \LWmvc\Model\DataCommandHandler
{
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function setFilePath($path)
    {
        $this->filePath = $path;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }
    
    public function deleteEntity($id)
    {
        $this->deleteLogo($id);
        
        $this->db->setStatement("DELETE FROM t:lw_master WHERE lw_object = 'lw_events' AND id = :id");
        $this->db->bindParameter("id", 'i', $id);
        return $this->db->pdbquery();
    }
    
    public function addEntity($array, $userId)
    {
        $this->db->setStatement("INSERT INTO t:lw_master ( lw_object, language, opt1number, opt2number, opt4number, opt5number, opt1text, opt2text, opt3text, lw_first_date, lw_first_user, lw_last_date, lw_last_user ) VALUES ( 'lw_events', :language, :opt1number, :opt2number, :opt4number, :opt5number, :opt1text, :opt2text, :opt3text, :firstdate, :firstuser, :lastdate, :lastuser ) ");
        $this->db->bindParameter("id", 'i', $id);
        $this->db->bindParameter("opt1number", 'i', $array['opt1number']);
        $this->db->bindParameter("opt2number", 'i', $array['opt2number']);
        $this->db->bindParameter("opt4number", 'i', $array['opt4number']);
        $this->db->bindParameter("opt5number", 'i', $array['opt5number']);
        $this->db->bindParameter("opt1text", 's', $array['opt1text']);
        $this->db->bindParameter("opt2text", 's', $array['opt2text']);
        $this->db->bindParameter("opt3text", 's', $array['opt3text']);
        $this->db->bindParameter("language", 's', $array['language']);
        $this->db->bindParameter("firstdate", 's', date("YmdHis"));
        $this->db->bindParameter("firstuser", 'i', $userId);
        $this->db->bindParameter("lastdate", 's', date("YmdHis"));
        $this->db->bindParameter("lastuser", 'i', $userId);
        $id = $this->db->pdbinsert($this->db->gt('lw_master'));

        $ok2 = $this->db->saveClob($this->db->gt('lw_master'), 'opt1clob', $this->db->quote($array['opt1clob']), $id);
        $ok3 = $this->db->saveClob($this->db->gt('lw_master'), 'opt2clob', $this->db->quote($array['opt2clob']), $id);
        
        if ($id && $array['opt1file']['size'] > 0) {
            $this->saveLogo($id, $array['opt1file']);
        }
        return $id;
    }
    
    public function saveEntity($id, $array, $userId)
    {
        $this->db->setStatement("UPDATE t:lw_master SET opt1number = :opt1number, opt2number = :opt2number, opt4number = :opt4number, opt5number = :opt5number, opt1text = :opt1text, opt2text = :opt2text, opt3text = :opt3text, lw_last_date = :lastdate, lw_last_user = :lastuser WHERE id = :id ");
        $this->db->bindParameter("id", 'i', $id);
        $this->db->bindParameter("opt1number", 'i', $array['opt1number']);
        $this->db->bindParameter("opt2number", 'i', $array['opt2number']);
        $this->db->bindParameter("opt4number", 'i', $array['opt4number']);
        $this->db->bindParameter("opt5number", 'i', $array['opt5number']);
        $this->db->bindParameter("opt1text", 's', $array['opt1text']);
        $this->db->bindParameter("opt2text", 's', $array['opt2text']);
        $this->db->bindParameter("opt3text", 's', $array['opt3text']);
        $this->db->bindParameter("lastdate", 's', date("YmdHis"));
        $this->db->bindParameter("lastuser", 'i', $userId);
        $ok = $this->db->pdbquery();
        
        $ok2 = $this->db->saveClob($this->db->gt('lw_master'), 'opt1clob', $this->db->quote($array['opt1clob']), $id);
        $ok3 = $this->db->saveClob($this->db->gt('lw_master'), 'opt2clob', $this->db->quote($array['opt2clob']), $id);
        
        if ($ok && $array['opt1file']['size'] > 0) {
            $this->saveLogo($id, $array['opt1file']);
        }
        return $ok;
    }
    
    public function saveLogoName($id, $filename)
    {
        $this->db->setStatement("UPDATE t:lw_master SET opt1file = :opt1file WHERE id = :id ");
        $this->db->bindParameter("id", 'i', $id);
        $this->db->bindParameter("opt1file", 's', $filename);
        return $this->db->pdbquery();
    }
    
    private function saveLogo($id, $fileDataArray)
    {
        $extension = "." . \lw_io::getFileExtension($fileDataArray["name"]);
        $filename = "events_logo_" . $id . $extension;
        
        $uploadDir = \lw_directory::getInstance($this->getFilePath());
        $files = $uploadDir->getDirectoryContents("file");

        foreach ($files as $file) {
            $name = $file->getName();
            $explodeName = explode(".", $name);
            $nameWithoutExtention = $explodeName[0];

            if ($nameWithoutExtention == "events_logo_" . $id) {
                $uploadDir->deleteFile($file->getName());
            }
        }

        $uploadDir->addFile($fileDataArray['tmp_name'], $filename);
        $this->saveLogoName($id, $filename);
        
        list($width) = @getimagesize($this->getFilePath()."/".$filename);
        if($width > 170){
            $image = new \LwEvents\Services\LogoResizer($this->getFilePath()."/".$filename);
            $image->setParams(170, 100);
            $image->resize();
            $image->saveImage();
        }
    }    
    
    public function deleteLogo($id)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $result = $this->db->pselect1();
        $this->deleteLogoFile($result['opt1file']);
        
        $this->db->setStatement("UPDATE t:lw_master SET opt1file = '' WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->pdbquery();
        return true;
    }
  
    private function deleteLogoFile($filename)
    {
        $dir = \lw_directory::getInstance($this->getFilePath());
        $dir->deleteFile($filename);
        return true;
    }
    
    private function _updatePermissions($file)
    {
        return true;
        $config = \lw_registry::getInstance()->getEntry("config");
        
        if ($config['files']['chgrp']) {
            @chgrp($file, $config['files']['chgrp']);
        }
        if ($config['files']['chmod']) {
            @chmod($file, octdec($config['files']['chmod']));
        }
    } 
}
