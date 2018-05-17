<?php

namespace Base;


use Traits\FieldsTrait;
use Traits\ArgumentsTrait;
use Interfaces\SubQueryInterface;
use Exceptions\BuilderErrorException;

class SubQuery implements SubQueryInterface
{
    use FieldsTrait,
        ArgumentsTrait;

    /**
     * @return string
     * @throws BuilderErrorException
     */
    public function build()
    {
        $parts = [];
        $prototype = '(' . join($this->args, ',') . ')';

        foreach($this->fields as $key => $field) {

            if($field instanceof self) {
                $parts[] = $key . $field->build();
                continue;
            }

            if(is_string($field)) {
                $parts[] = $field;
                continue;
            }

            throw new BuilderErrorException("Structure of query fields data is corrupted");
        }

        return $prototype . '{' . join($parts, ',') . '}';
    }
}