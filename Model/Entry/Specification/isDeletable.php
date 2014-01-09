<?php

namespace LwEvents\Model\Entry\Specification;

class isDeletable 
{
    public function __construct()
    {
    }
    
    static public function getInstance()
    {
        return new isDeletable();
    }
    
    public function isSatisfiedBy(LwEvents\Model\Entry\Object\entry $entity)
    {
        return true;
    }
}