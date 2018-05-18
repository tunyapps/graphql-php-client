<?php

namespace Traits;


use Base\SubQuery;
use Exceptions\BuilderErrorException;

trait FieldsBuilderTrait
{
    /**
     * [
     *     'foo' => SubQuery,
     *     'bar',
     *      ...
     * ]
     *
     * @var array
     */
    protected $fields = [];

    /**
     * @return string
     * @throws BuilderErrorException
     */
    protected function buildFields()
    {
        $fields = [];
        foreach($this->fields as $key => $field) {

            if($field instanceof SubQuery) {
                $fields[] = $key . $field->build();
                continue;
            }

            if(is_string($field)) {
                $fields[] = $field;
                continue;
            }

            throw new BuilderErrorException("Structure of query fields data is corrupted");
        }

        return '{' . join($fields, ',') . '}';
    }
}