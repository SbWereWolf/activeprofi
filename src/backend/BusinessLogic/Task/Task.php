<?php

namespace BusinessLogic\Task;


class Task implements ITask
{
    private $id = 0;
    private $title = '';
    private $dateAsNumber = 0;
    private $dateAsString = '';
    private $author = '';
    private $status = '';
    private $description = '';


    public function setId(int $id): ITask
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): ITask
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDateNumber(int $date): ITask
    {
        $this->dateAsNumber = $date;
        return $this;
    }

    public function getDateNumber(): int
    {
        return $this->dateAsNumber;
    }

    public function setAuthor(string $author): ITask
    {
        $this->author = $author;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setStatus(string $status): ITask
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setDescription(string $description): ITask
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDateString(string $dateAsString): ITask
    {
        $this->dateAsString = $dateAsString;
        return $this;
    }

    public function getDateString(): string
    {
        return $this->dateAsString;
    }
}
