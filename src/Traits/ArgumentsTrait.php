<?php

namespace Traits;


use Exceptions\SyntaxErrorException;

trait ArgumentsTrait
{

    /**
     * [
     *     'foo' => 'value',
     *     'bar' => 'value',
     *      ...
     * ]
     *
     * @var $args
     */
    protected $args = [];

    /**
     * @return $this
     * @throws SyntaxErrorException
     */
    public function args()
    {
        $args = func_get_args();

        if(isset($args[0]) && is_string($args[0])) {
            $this->arg($args[0], $args[1]);
            return $this;
        }

        foreach($args as $arg => $value) {
            $this->arg($arg, $value);
        }

        return $this;
    }

    /**
     * @param string $arg
     * @param string $value
     * @return $this
     * @throws SyntaxErrorException
     */
    public function arg($arg, $value)
    {
        if(is_string($arg) && is_string($value)) {
            $this->args[$arg] = $value;
            return $this;
        }

        throw new SyntaxErrorException("Query has a syntax error in arguments definition");
    }
}