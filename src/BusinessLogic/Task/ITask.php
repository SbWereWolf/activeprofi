<?php

namespace BusinessLogic\Task;


interface ITask
{

    public function setId(int $id): self;

    public function setDescription(string $token): self;

    public function getDescription(): string;

    public function getId(): int;

    public function setTitle(string $title): self;

    public function getTitle(): string;

    public function setDateNumber(int $start): self;

    public function getDateNumber(): int;

    public function setDateString(string $start): self;

    public function getDateString(): string;

    public function setAuthor(string $author): self;

    public function getAuthor(): string;

    public function setStatus(string $status): self;

    public function getStatus(): string;
}
