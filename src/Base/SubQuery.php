<?php

namespace Base;


use Traits\FieldsBuilderTrait;
use Traits\FieldsTrait;
use Traits\ArgumentsTrait;
use Interfaces\SubQueryInterface;
use Exceptions\BuilderErrorException;

class SubQuery implements SubQueryInterface
{
    use FieldsTrait,
        FieldsBuilderTrait,
        ArgumentsTrait;

    /**
     * @return string
     * @throws BuilderErrorException
     */
    public function build()
    {
        if(0 == count($this->fields)) {
            throw new BuilderErrorException("Nested query has no fields");
        }

        $parts = [];
        $prototype = '';
        foreach($this->args as $arg => $value) {
            $parts[] = $arg . ':' . $value;
        }

        if(count($parts)) {
            $prototype = '(' . join($parts, ',') . ')';
        }

        return $prototype . $this->buildFields();
    }
}