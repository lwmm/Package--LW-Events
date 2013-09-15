<?php

namespace LwEvents\Model\Entry\DataHandler;

class QueryHandler extends \LWmvc\Model\DataQueryHandler
{
    public function __construct(\lw_db $db)
    {
        $this->db = $db;
        $this->type = "lw_events";
    }
    
    public function loadAllAvailableYearsByLanguage($language)
    {
        $this->db->setStatement('SELECT opt2number FROM t:lw_master WHERE lw_object = :lw_object AND language = :language ORDER BY opt2number DESC ');
        $this->db->bindParameter("lw_object",   "s", "lw_events");
        $this->db->bindParameter("language",    "s", $language);
        $results = $this->db->pselect();
        foreach($results as $year) {
            $dummy = substr($year['opt2number'], 0, 4);
            $array[$dummy] = 1;
        }
        return $array;
    }
    
    public function loadArchivedEntriesByLanguageAndYear($language, $year)
    {
        $this->db->setStatement('SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND language = :language AND opt2number > :yearmin AND opt2number < :yearmax AND opt4number < :today ORDER BY opt2number ASC ');
        $this->db->bindParameter("lw_object",   "s", "lw_events");
        $this->db->bindParameter("yearmin",     "s", $year.'0000');
        $this->db->bindParameter("yearmax",     "s", $year.'1300');
        $this->db->bindParameter("today",       "s", date("Ymd"));
        $this->db->bindParameter("language",    "s", $language);
        $results = $this->db->pselect();
        foreach($results as $result) {
            $array[] = new \LWmvc\Model\DTO($result);
        }
        return $array;
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