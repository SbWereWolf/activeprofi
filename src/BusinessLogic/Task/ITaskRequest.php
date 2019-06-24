<?php

namespace BusinessLogic\Task;


interface ITaskRequest
{

    public function setId(int $id): self;

    public function getId(): int;

    public function setCapacity(int $capacity): self;

    public function getCapacity(): int;

    public function setPage(int $page): self;

    public function getPage(): int;

    public function setSample(string $sample): self;

    public function getSample(): string;
}
