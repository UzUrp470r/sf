<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ProjectClass extends Constraint
{
    public $message = 'Please enter Company or Client.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}