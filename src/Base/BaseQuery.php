<?php

namespace Base;

use Exceptions\SyntaxErrorException;

class BaseQuery
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var array
     *
     * [
     *     'foo',
     *     'bar'
     * ]
     */
    protected $args = [];

    /**
     * @var array
     *
     * [
     *     'foo' => SubQuery,
     *     'bar',
     *     'baz'
     * ]
     */
    protected $fields = [];

    /**
     * @param string $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function fields()
    {

    }

    /**
     * @param string $method
     * @return $this
     */
    public function method($method, $callback = null)
    {
        $this->methods[] = $method;
        $definition = new QueryVariables();
        call_user_func($callback, $definition);
        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $query = $this->type;
        if('mutation' == $this->type) {
            $query .= ' ' . $this->object . '{';
            $query .= $this->method . '(';
        } else {
            $query .= ' ' . $this->method . '{';
            $query .= $this->object . '(';
        }

        $query .= join($this->args, ',');
        $query .= '){' . join($this->fields, ',') . '}';
        $query .= '}';
        $query = urlencode($query);

        return $query;
    }

    /**
     * @param $name
     * @param $args
     * @throws SyntaxErrorException
     */
    public function __call($name, $args) {

        if(isset($args[0]) || !is_callable($args[0])) {
            throw new SyntaxErrorException("Method has a syntax error in variables definition");
        }

        $this->method($name, $args[0]);
    }
}