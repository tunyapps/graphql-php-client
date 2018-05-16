<?php

namespace Interfaces;


interface VariablesInterface
{
    public function string($name, $value);

    public function boolean($name, $value);

    public function integer($name, $value);

    public function float($name, $value);
}