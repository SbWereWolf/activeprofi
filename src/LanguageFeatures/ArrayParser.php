<?php

namespace LanguageFeatures;


class ArrayParser
{
    private $parameters = array();

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getIntegerField(string $field): int
    {

        $value = intval($this->safelyGetByKey($field));
        return $value;
    }

    public function getFloatField(string $field): float
    {

        $value = floatval($this->safelyGetByKey($field));
        return $value;
    }

    public function getStringField(string $field): string
    {

        $value = strval($this->safelyGetByKey($field));
        return $value;
    }

    private function safelyGetByKey(string $key)
    {

        $parameters = $this->parameters;
        $isExists = array_key_exists($key, $parameters);

        $value = null;
        if ($isExists) {
            $value = $parameters[$key];
        }
        return $value;
    }

}
