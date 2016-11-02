<?php
declare(strict_types=1);

namespace Schakel\Mail\Test\Tracker;

use Schakel\Mail\Tracker\MailTracker;
use Schakel\Mail\Tracker\MailTrackerInterface;

class MailTrackerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Schakel\Mail\Tracker\MailTracker::getId
     * @covers Schakel\Mail\Tracker\MailTracker::getEmail
     * @covers Schakel\Mail\Tracker\MailTracker::getSubject
     * @covers Schakel\Mail\Tracker\MailTracker::getSent
     * @covers Schakel\Mail\Tracker\MailTracker::getOpened
     * @covers Schakel\Mail\Tracker\MailTracker::getClicked
     */
    public function testConstructor()
    {
        $obj = new MailTracker;

        $this->assertNull($obj->getId());
        $this->assertNull($obj->getEmail());
        $this->assertNull($obj->getSubject());
        $this->assertNull($obj->getSent());
        $this->assertNull($obj->getOpened());
        $this->assertNull($obj->getClicked());
    }

    /**
     * @param string $get Getter function
     * @param string $set Setter function
     * @param string $value Value to test with
     * @param boolean $valid True if the value is valid.
     */
    public function runInternalSetTest($get, $set, $value, $valid)
    {
        $obj = new MailTracker;
        if (!$valid) {
            try {
                $obj->$set($value);
                $this->fail('Didn\'t throw an exception!');
            } catch (\TypeError $err) {
                $this->assertNull($obj->$get());
            }
        } else {
            $this->assertNull($obj->$set($value));
            $this->assertEquals($value, $obj->$get());
        }
    }

    /**
     * @covers Schakel\Mail\Tracker\MailTracker::getEmail
     * @covers Schakel\Mail\Tracker\MailTracker::setEmail
     * @dataProvider provideEmails
     */
    public function testSetEmail($value, $valid)
    {
        $this->runInternalSetTest('getEmail', 'setEmail', $value, $valid);
    }

    /**
     * @covers Schakel\Mail\Tracker\MailTracker::getSubject
     * @covers Schakel\Mail\Tracker\MailTracker::setSubject
     * @dataProvider provideSubjects
     */
    public function testSetSubject($value, $valid)
    {
        $this->runInternalSetTest('getSubject', 'setSubject', $value, $valid);
    }

    /**
     * @covers Schakel\Mail\Tracker\MailTracker::getSent
     * @covers Schakel\Mail\Tracker\MailTracker::setSent
     * @dataProvider provideDates
     */
    public function testSetSent($value, $valid)
    {
        $this->runInternalSetTest('getSent', 'setSent', $value, $valid);
    }

    /**
     * @covers Schakel\Mail\Tracker\MailTracker::getOpened
     * @covers Schakel\Mail\Tracker\MailTracker::setOpened
     * @dataProvider provideDates
     */
    public function testSetOpened($value, $valid)
    {
        $this->runInternalSetTest('getOpened', 'setOpened', $value, $valid);
    }

    /**
     * @covers Schakel\Mail\Tracker\MailTracker::getClicked
     * @covers Schakel\Mail\Tracker\MailTracker::setClicked
     * @dataProvider provideDates
     */
    public function testSetClicked($value, $valid)
    {
        $this->runInternalSetTest('getClicked', 'setClicked', $value, $valid);
    }

    /**
     * Contains a list of emails
     */
    public function provideEmails()
    {
        return [
            'Simple e-mail' => ['john.doe@example.com', true],
            'Complex e-mail' => ['john.doe+cake{bomb}@example.edu.uk', true],
            'Invalid domain' => ['invalid@domain', false],
            'Non e-mail' => ['steve', false],
            'Empty string' => ['', false],
            'Null' => [null, false],
            'Number' => [12, false]
        ];
    }

    /**
     * Contains a list of subjects that should be either valid or invalid
     */
    public function provideSubjects()
    {
        return [
            'Basic' => ['Hello world', true],
            'Unicode' => ['イロハニホヘト チリヌルヲ ワカヨタレソ ツネナラム', true],
            'Empty' => ['', true],
            'Null' => [null, true],
            'Number' => [12, false]
        ];
    }

    /**
     * Contains a list of dates
     */
    public function provideDates()
    {
        return [
            'Date object from empty constructor' => [new \DateTime, true],
            'Date object from string' => [
                \DateTime::createFromFormat('Y-m-d', '2016-12-18'),
                true
            ],
            'Date string' => ['2016-12-18', false],
            'Null' => [null, false],
            'Number' => [12, false]
        ];
    }
}
