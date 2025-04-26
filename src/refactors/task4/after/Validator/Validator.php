<?php

namespace src\refactors\task4\after\Validator;

use src\refactors\task4\after\Model\Model;

interface Validator
{
    public function validate(Model $model):bool;
}
