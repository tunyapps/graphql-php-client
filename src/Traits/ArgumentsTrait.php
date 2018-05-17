<?php

namespace Traits;


use Exceptions\SyntaxErrorException;

trait ArgumentsTrait
{

    /**
     * [
     *     'foo',
     *     'bar',
     *      ...
     * ]
     *
     * @var $args
     */
    protected $args = [];

    /**
     * @param array|string $args
     * @return $this
     * @throws SyntaxErrorException
     */
    public function args($args)
    {
        if(is_string($args)) {
            $this->args[] = $args;
            return $this;
        }

        foreach($args as $arg) {
            $this->arg($arg);
        }

        return $this;
    }

    public function arg($arg)
    {
        if(is_string($arg)) {
            $this->args[] = $arg;
            return $this;
        }

        throw new SyntaxErrorException("Query has a syntax error in arguments definition");
    }
}