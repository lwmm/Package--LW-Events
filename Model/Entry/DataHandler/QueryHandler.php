<?php

namespace LwEvents\Model\Entry\DataHandler;

class QueryHandler extends \LWmvc\Model\DataQueryHandler
{
    public function __construct(\lw_db $db)
    {
        $this->db = $db;
        $this->type = "lw_events";
    }
    
    public function loadAllActualEntries($lang)
    {
        $this->db->setStatement('SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND language = :language AND ( :date <= opt2number OR :date <= opt4number ) ORDER BY opt2number ASC ');
        $this->db->bindParameter("lw_object",   "s", "lw_events");
        $this->db->bindParameter("date",        "i", date("Ymd"));
        $this->db->bindParameter("language",    "s", $lang);
        $results = $this->db->pselect();
        foreach($results as $result) {
            $array[] = new \LWmvc\Model\DTO($result);
        }
        return $array;
    }
    
    public function loadEntryById($id)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :type AND id = :id");
        $this->db->bindParameter("type",        "s", "lw_events");
        $this->db->bindParameter("id",          "i", $id);
        $result = $this->db->pselect1();
        return new \LWmvc\Model\DTO($result);
    }
}