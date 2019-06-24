<?php
/**
 * Copyright © 2019 Volkhin Nikolay
 * Project: activeprofi
 * DateTime: 25.06.2019 2:27
 */

namespace BusinessLogic\Task;


class TaskRequest implements ITaskRequest
{
    private $id = 0;
    private $capacity = 0;
    private $page = 0;
    private $sample = ''; 

    public function setId(int $id): ITaskRequest
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCapacity(int $capacity): ITaskRequest
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setPage(int $page): ITaskRequest
    {
        $this->page = $page;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setSample(string $sample): ITaskRequest
    {
        $this->sample = $sample;
        return $this;
    }

    public function getSample(): string
    {
        return $this->sample;
    }
}
