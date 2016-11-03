<?php
declare(strict_types=1);

namespace Schakel\Mail\Test;

use Schakel\Mail\Utils;
use Schakel\Mail\Mail;
use Schakel\Mail\MailInterface;
use Schakel\Mail\Tracker\MailTracker;
use Schakel\Mail\Tracker\MailTrackerInterface;

/**
 * Runs a shitload of assertions to make sure the Utils class checks types
 * properly
 *
 * @group medium
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tries an assertion, which is shared code between testAssertionSimpleType
     * and testAssertionClassInterface.
     *
     * @param string $type Type to pass as 2nd argument to assertArgumentType
     * @param mixed $value Value to test with
     * @param bool $vald Is this $type-$value combination valid?
     */
    public function tryAssertion(string $type, $value, bool $valid)
    {
        if (!$valid) {
            $this->setExpectedException(
                'TypeError',
                "Expected one of ({$type})"
            );
            Utils::assertArgumentType($value, $type);
        } else {
            $this->assertEquals($value, Utils::assertArgumentType($value, $type));
        }
    }
    /**
     * @covers Schakel\Mail\Utils::assertArgumentType
     * @covers Schakel\Mail\Utils::checkArgumentSingleType
     * @dataProvider provideSimpleAssertions
     */
    public function testAssertionSimpleType(
        string $type,
        $value,
        bool $valid
    ) {
        $this->tryAssertion($type, $value, $valid);
    }

    /**
     * @covers Schakel\Mail\Utils::assertArgumentType
     * @covers Schakel\Mail\Utils::checkArgumentSingleType
     * @dataProvider provideClassAssertions
     */
    public function testAssertionClassInterface(
        string $classOrInterface,
        $object,
        bool $valid
    ) {
        $this->tryAssertion($classOrInterface, $object, $valid);
    }

    /**
     * Provides a HUGE list of values to test against various argument types.
     *
     * @return array[]
     */
    public function provideSimpleAssertions()
    {
        $types = [
            'null', 'string', 'int', 'uint',
            'float', 'double', 'pfloat', 'numeric',
            'boolean', 'object', 'date', 'array',
            'object', 'scalar', 'email'
        ];

        $tests = [
            [null, 'null'],
            ['', 'string', 'scalar'],
            ['hello world', 'string', 'scalar'],
            ['12345', 'string', 'numeric', 'scalar'],
            ['john.doe@example.com', 'string', 'email', 'scalar'],
            ['john.{doe}+non-spam@example.edu.uk', 'string', 'email', 'scalar'],
            [12345, 'int', 'uint', 'numeric', 'scalar'],
            [00000, 'int', 'uint', 'numeric', 'scalar'],
            [-1234, 'int', 'numeric', 'scalar'],
            [12.34, 'float', 'double', 'pfloat', 'numeric', 'scalar'],
            [0.000, 'float', 'double', 'pfloat', 'numeric', 'scalar'],
            [-1.23, 'float', 'double', 'numeric', 'scalar'],
            [false, 'boolean', 'scalar'],
            [true, 'boolean', 'scalar'],
            [new \RuntimeException, 'object'],
            [new \DateTime, 'object', 'date'],
            [\DateTime::createFromFormat('Y-m-d', '2016-08-20'), 'object', 'date'],
            [[], 'array'],
            [['yes' => true], 'array']
        ];

        $testResults = [];
        foreach ($tests as $test) {
            $testValue = array_shift($test);
            foreach ($types as $testType) {
                $testResults[] = [$testType, $testValue, in_array($testType, $test)];
            }
        }

        return $testResults;
    }

    public function provideClassAssertions()
    {
        $classesAndInterfaces = [
            Mail::class,
            MailInterface::class,
            MailTrackerInterface::class,
            \Throwable::class,
            \Exception::class,
            ClassThatDoesntExist::class
        ];

        $tests = [
            [new MailTracker, MailTrackerInterface::class],
            [new Mail, Mail::class, MailInterface::class],
            [new \TypeError, \Throwable::class],
            [new \RuntimeException, \Throwable::class, \Exception::class],
        ];

        $testResults = [];
        foreach ($tests as $test) {
            $testValue = array_shift($test);
            foreach ($classesAndInterfaces as $className) {
                $testResults[] = [$className, $testValue, in_array($className, $test)];
            }
        }

        return $testResults;
    }
}
