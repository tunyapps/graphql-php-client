<?php

namespace Base;


use Closure;
use Traits\FieldsTrait;
use Traits\FieldsBuilderTrait;
use Exceptions\SyntaxErrorException;

class BaseQuery
{
    use FieldsTrait,
        FieldsBuilderTrait;

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
     * @throws SyntaxErrorException
     */
    public function method($method, $callback = null)
    {
        $this->method = $method;

        if(isset($callback)) {

            if(is_callable($callback) && $callback instanceof Closure) {
                $definition = new QueryVariables();
                call_user_func($callback, $definition);
                $this->variables = $definition;
                return $this;
            }

            throw new SyntaxErrorException("Method has a syntax error in variables definition");
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array $args
     * @return $this
     * @throws SyntaxErrorException
     */
    public function __call($name, $args) {

        if(isset($args[0])) {
            $this->method($name, $args[0]);
        } else {
            $this->method($name);
        }

        return $this;
    }

    /**
     * @param bool $encode
     * @return string
     */
    public function build($encode = true)
    {
        $type = $this->type;
        $method = $this->method;
        $fields = $this->buildFields();
        $prototype = $this->buildPrototype();
        $variables = $this->buildVariables();

        $query = $type . ' ' . $method . $prototype . $fields;
        if('' != $variables) {
            $query .= $variables;
        }

        return $encode ? urlencode($query) : $query;
    }

    /**
     * @return string
     */
    protected function buildPrototype()
    {
        if(empty($this->variables)) {
            return '';
        }

        $prototype = [];
        foreach($this->variables->prototype() as $name => $type) {
            $prototype[] = '$' . $name . ':' . $type;
        }

        return count($prototype) ? '(' . join($prototype, ',') . ')' : '';
    }

    /**
     * @return string
     */
    protected function buildVariables()
    {
        if(empty($this->variables)) {
            return '';
        }

        $variables = [];
        $prototype = $this->variables->prototype();
        foreach($this->variables->values() as $name => $value) {

            switch($prototype[$name]) {
                case QueryVariables::STRING: {
                    $variables[] = $name . ':"' . str_replace('"', '\"', $value) . '"';
                    break;
                }
                case QueryVariables::BOOLEAN: {
                    $variables[] = $name . ':' . ($value ? '1' : '0');
                    break;
                }
                default: {
                    $variables[] = $name . ':' . (string)$value;
                    break;
                }
            }

        }

        return count($variables) ? 'variables:{' . join($variables, ',') . '}' : '';
    }
}