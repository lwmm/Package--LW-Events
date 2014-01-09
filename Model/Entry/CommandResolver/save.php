<?php

namespace LwEvents\Model\Entry\CommandResolver;

class save extends \LWmvc\Model\CommandResolver
{
    public function __construct($command)
    {
        parent::__construct($command);
        $this->dic = new \LwEvents\Services\dic();
        $this->baseNamespace = "\\LwEvents\\Model\\Entry\\";
        $this->ObjectClass = $this->baseNamespace."Object\\entry";
    }
    
    public function getInstance($command)
    {
        return new save($command);
    }
    
    protected function buildValueObjectFromPostArrays()
    {
        $array = $this->command->getDataByKey('postArray');
        $array['opt1file'] = $this->command->getDataByKey('opt1file');
        return new \LWddd\ValueObject($array);
    }
    
    public function saveEntity($entity)
    {   
        $inAuth = $this->dic->getLwInAuth();
        $listConfig = $this->command->getParameterByKey("configuration");
        $config = $this->dic->getConfiguration();
        $this->getCommandHandler()->setFilePath($config['path']['resource'].'lw_events');
        $result = $this->getCommandHandler()->saveEntity($entity->getId(), $entity->getValues(), $inAuth->getUserdata("id"), $listConfig->getValueByKey("archive"));
        $this->postSaveWork($result, $entity->getId(), $entity);
        return $entity->getId();
    }
    
    protected function getFilteredObject($dto)
    {
        $filter = \LWmvc\Model\FilterFactory::buildFilter($this->baseNamespace);
        $filteredDTO = $filter->filter($dto);
        return \LWmvc\Model\EntityFactory::buildEntityFromDTO($this->ObjectClass, $filteredDTO);
    }    
    
    public function resolve()
    {
        $array = $this->command->getDataByKey('postArray');
        $array['id'] = $this->command->getParameterByKey('id');
        $array['opt1file'] = $this->command->getDataByKey('opt1file');
        $entity = $this->getFilteredObject(new \LWmvc\Model\DTO($array));
        $entity->setId($this->command->getParameterByKey("id"));
        $isValidSpecification = \LWmvc\Model\SpecificationFactory::buildIsValidSpecification($this->baseNamespace);
        $isValidSpecification->setConfiguration($this->command->getParameterByKey("configuration"));

        if ($isValidSpecification->isSatisfiedBy($entity)) {
            $id = $this->saveEntity($entity);
            $this->command->getResponse()->setParameterByKey('saved', true);
        }
        else {
            $this->command->getResponse()->setDataByKey('error', $isValidSpecification->getErrors());
            $this->command->getResponse()->setParameterByKey('error', true);
        }                    
        return  $this->command->getResponse();
    }
}