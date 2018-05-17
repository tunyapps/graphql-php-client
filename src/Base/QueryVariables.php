<?php

namespace Base;


use Interfaces\VariablesInterface;

class QueryVariables implements VariablesInterface
{
    const STRING = 'String';

    const INT = 'Int';

    const FLOAT = 'Float';

    const BOOLEAN = 'Boolean';

    const ID = 'ID';

    /**
     * [
     *     'foo' => ['String', 'bar'],
     *      ...
     * ]
     *
     * @var array
     */
    protected $variables = [];

    /**
     * @param $type
     * @param $name
     * @param $value
     */
    public function add($type, $name, $value)
    {
        $this->variables[$name] = [$type, $value];
    }

    /**
     * @param $name
     * @param $value
     */
    public function string($name, $value)
    {
        $this->add(self::STRING, $name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function boolean($name, $value)
    {
        $this->add(self::BOOLEAN, $name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function integer($name, $value)
    {
        $this->add(self::INT, $name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function float($name, $value)
    {
        $this->add(self::FLOAT, $name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function id($name, $value)
    {
        $this->add(self::ID, $name, $value);
    }

    /**
     * @return array
     */
    public function values()
    {
        $values = [];
        foreach($this->variables as $name => $data)
        {
            $values[$name] = $data[1];
        }
        return $values;
    }

    /**
     * @return array
     */
    public function prototype()
    {
        $variables = [];
        foreach($this->variables as $name => $data) {
            $type = $data[0];
            $variables[$name] = $type;
        }
        return $variables;
    }
}