<?php

namespace Environment\Task;

use BusinessLogic\Task\ITaskRequest;
use BusinessLogic\Task\TaskRequest;
use LanguageFeatures\ArrayParser;

class Reception extends \Environment\Basis\Reception
{
    const ID = 'id';
    const CAPACITY = 'capacity';
    const PAGE = 'page';
    const SAMPLE = 'sample';

    /**
     * @return ITaskRequest
     */
    public function toRead(): ITaskRequest
    {
        $item = $this->setupFromPath();

        return $item;
    }

    private function setupFromPath(): ITaskRequest
    {
        $this->setParser(new ArrayParser($this->getArguments()));

        $task = $this->setupTaskRequest();

        return $task;
    }

    private function setupTaskRequest(): ITaskRequest
    {
        $id = $this->getId();
        $capacity = $this->getCapacity();
        $page = $this->getPage();
        $sample = $this->getSample();

        $taskRequest = (new TaskRequest())
            ->setId($id)
            ->setCapacity($capacity)
            ->setPage($page)
            ->setSample($sample);
        return $taskRequest;
    }

    private function getId(): int
    {
        $value = $this->getParser()->getIntegerField(self::ID);
        return $value;
    }

    private function getCapacity(): int
    {
        $value = $this->getParser()->getIntegerField(self::CAPACITY);
        return $value;
    }

    private function getPage(): int
    {
        $value = $this->getParser()->getIntegerField(self::PAGE);
        return $value;
    }

    private function getSample(): int
    {
        $value = $this->getParser()->getIntegerField(self::SAMPLE);
        return $value;
    }
}
