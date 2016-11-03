<?php
declare(strict_types=1);

namespace Schakel\Mail;

/**
 * General utilities which are used in various projects. Maybe we should try to
 * separate these into their own project, but that's not really important right
 * now.
 *
 * @author Joram Schrijver <joram@schakelmarketeers.nl>
 */
class Utils
{
    /**
     * Check if a value has a certain type.
     *
     * @param object $value The value to check
     * @param string $type One of the following strings:
     *                     - null
     *                     - string
     *                     - int
     *                     - boolean
     *                     - date
     * @return boolean Whether the value is of the specified type.
     */
    private static function checkArgumentSingleType($value, string $type): bool
    {
        switch ($type) {
            case 'null':
                return is_null($value);
            case 'string':
                return is_string($value);
            case 'int':
                return is_integer($value);
            case 'uint':
                return is_integer($value) && $value >= 0;
            case 'float':
            case 'double':
                return is_float($value);
            case 'pfloat':
                return is_float($value) && $value >= 0;
            case 'numeric':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value);
            case 'object':
                return is_object($value);
            case 'date':
                return $value instanceof \DateTime;
            case 'array':
                return is_array($value);
            case 'scalar':
                return is_scalar($value);
            case 'email':
                return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === $value;
        }

        if (class_exists($type) || interface_exists($type)) {
            return is_object($value) && is_a($value, $type, false);
        }

        return false;
    }

    /**
     * Assert that a value is of one of the desired types.
     *
     * @param object $value The value to check
     * @param string[] $types The types to check against
     * @return boolean
     * @throws TypeError When the argument is not of a desired
     *                                  type.
     */
    public static function assertArgumentType($value, string ...$types)
    {
        foreach ($types as $type) {
            if (self::checkArgumentSingleType($value, $type)) {
                return $value;
            }
        }

        throw new \TypeError(sprintf(
            'Invalid argument type. Expected one of (%s), got %s',
            implode(',', $types),
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }
}
