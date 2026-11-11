<?php

declare(strict_types=1);

namespace App\Core\Traits;

use App\Exception\FrameworkException;

trait GetSetProperty
{
    public function getProperty($property)
    {
        if (!property_exists($this, $property)) {
            throw new FrameworkException("Property does not exist: $property");
        }

        return $this->$property;
    }

    public function setProperty($property, $value): void
    {
        if (!property_exists($this, $property)) {
            throw new FrameworkException("Property does not exist: $property");
        }

        $this->$property = $value;
    }

    public function getConstant($constant)
    {
        $constantName = self::class . '::' . $constant;

        if (!defined($constantName)) {
            throw new FrameworkException(
                "Constant does not exist: {$constant}"
            );
        }

        return constant($constantName);
    }
}
