<?php

namespace Traits;


use Base\SubQuery;
use Exceptions\SyntaxErrorException;

trait FieldsTrait
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
     * @param array|string $fields
     * @return $this
     * @throws SyntaxErrorException
     */
    public function fields($fields)
    {
        if(is_string($fields)) {
            $this->fields[] = $fields;
            return $this;
        }

        foreach($fields as $field => $definition) {

            if(is_callable($field)) {
                $this->field($field, $definition);
                continue;
            }

            if(is_string($definition)) {
                $this->field($definition);
            }
        }

        return $this;
    }

    /**
     * @param $field
     * @param null|callable $definition
     * @return $this
     * @throws SyntaxErrorException
     */
    public function field($field, $definition = null)
    {
        if(is_callable($definition))  {
            $query = new SubQuery();
            call_user_func($definition, $query);
            $this->fields[$field] = $query;
            return $this;
        }

        if(empty($definition)) {
            $this->fields[] = $field;
            return $this;
        }

        throw new SyntaxErrorException("Query has a syntax error in fields definition");
    }
}