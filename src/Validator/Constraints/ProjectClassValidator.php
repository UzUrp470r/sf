<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Entity\Project;

class ProjectClassValidator extends ConstraintValidator
{
    /**
     * Validate if Client or Company is entered
     * @param Project $project
     * @param Constraint $constraint
     *
     * @return [type]
     */
    public function validate($project, Constraint $constraint)
    {
        if (empty($project->getClient()) && empty($project->getCompany())) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}