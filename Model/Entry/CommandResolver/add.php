<?php

namespace LwEvents\Model\Entry\CommandResolver;

class add extends \LWmvc\Model\CommandResolver
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
        return new add($command);
    }
    
    public function saveEntity($entity)
    {
        $listConfig = $this->command->getParameterByKey("configuration");
        $config = $this->dic->getConfiguration();
        $this->getCommandHandler()->setFilePath($config['path']['resource'].'lw_events');
        $id = $this->getCommandHandler()->addEntity($entity->getValues(), $this->command->getParameterByKey("userId"));
        $this->postSaveWork($id, $id, $entity);
        return $id;
    }
    
    public function resolve()
    {
        $listConfig = $this->command->getParameterByKey("configuration");
        $array = $this->command->getDataByKey('postArray');
        $array['opt1file'] = $this->command->getDataByKey('opt1file');
        $array['language'] = $listConfig->getValueByKey('language');
        $dto = new \LWmvc\Model\DTO($array);
        $filter = \LWmvc\Model\FilterFactory::buildFilter($this->baseNamespace);
        $DTOFiltered = $filter->filter($dto);
        $entity = \LWmvc\Model\EntityFactory::buildEntityFromDTO($this->ObjectClass, $DTOFiltered);
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