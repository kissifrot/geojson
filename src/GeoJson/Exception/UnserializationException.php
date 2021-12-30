<?php

namespace GeoJson\Exception;

class UnserializationException extends \RuntimeException implements Exception
{
    /**
     * Creates an UnserializationException for a value with an invalid type.
     *
     * @param mixed $value
     */
    public static function invalidValue(string $context, $value, string $expectedType): UnserializationException
    {
        return new self(sprintf(
            '%s expected value of type %s, %s given',
            $context,
            $expectedType,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * Creates an UnserializationException for a property with an invalid type.
     *
     * @param mixed $value
     */
    public static function invalidProperty(string $context, string $property, $value, string $expectedType): UnserializationException
    {
        return new self(sprintf(
            '%s expected "%s" property of type %s, %s given',
            $context,
            $property,
            $expectedType,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * Creates an UnserializationException for a missing property.
     */
    public static function missingProperty(string $context, string $property, string $expectedType): UnserializationException
    {
        return new self(sprintf(
            '%s expected "%s" property of type %s, none given',
            $context,
            $property,
            $expectedType
        ));
    }

    /**
     * Creates an UnserializationException for an unsupported "type" property.
     */
    public static function unsupportedType(string $context, string $value): UnserializationException
    {
        return new self(sprintf('Invalid %s type "%s"', $context, $value));
    }
}
