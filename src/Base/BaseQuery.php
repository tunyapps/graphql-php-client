<?php

namespace Base;


use Exceptions\BuilderErrorException;
use Traits\FieldsTrait;
use Traits\ArgumentsTrait;
use Exceptions\SyntaxErrorException;

class BaseQuery
{
    use FieldsTrait,
        ArgumentsTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var QueryVariables
     */
    protected $variables = null;

    /**
     * @param string $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $method
     * @param null $callback
     * @return $this
     */
    public function method($method, $callback = null)
    {
        $this->method = $method;
        if(isset($callback)) {
            $definition = new QueryVariables();
            call_user_func($callback, $definition);
            $this->variables = $definition;
        }
        return $this;
    }

    /**
     * @return string
     * @throws BuilderErrorException
     */
    public function build()
    {
        $prototype = $this->args;
        foreach($this->variables->prototype() as $name => $type) {
            $prototype[] = '$' . $name . ':' . $type;
        }

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

        $variables = [];
        foreach($this->variables->values() as $name => $value) {
            $variables[] = $name . ':"' . $value . '"';
        }

        $type = $this->type;
        $method = $this->method;
        $fields = '{' . join($fields, ',') . '}';
        $prototype = count($prototype) ? '(' . join($prototype, ',') . ')' : '';
        $variables = count($variables) ? 'variables:{' . join($variables, ',') . '}' : '';

        $query = $type . ' ' . $method . $prototype . $fields . ' ' . $variables;

        return urldecode($query);
    }

    /**
     * @param string $name
     * @param array $args
     * @throws SyntaxErrorException
     */
    public function __call($name, $args) {

        if(isset($args[0]) || !is_callable($args[0])) {
            throw new SyntaxErrorException("Method has a syntax error in variables definition");
        }

        $this->method($name, $args[0]);
    }
}