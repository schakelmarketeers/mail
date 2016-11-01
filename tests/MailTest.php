<?php
declare(strict_types=1);

namespace Schakel\Mail\Test;

use Schakel\Mail\Mail;
use Schakel\Mail\Tracker\MailTracker;

class MailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Schakel\Mail\Mail::__construct
     */
    public function testDefaultConstructor()
    {
        $obj = new Mail;

        // Test configurable
        $this->assertNull($obj->getSubject());
        $this->assertNull($obj->getTo());

        // Test bodies
        $this->assertNull($obj->getMailBody());
        $this->assertNull($obj->getMailBodyPlainText());
    }

    /**
     * @covers Schakel\Mail\Mail::__construct
     */
    public function testCustomConstructor()
    {
        $tracker = new MailTracker;
        $obj = new Mail($tracker);

        $this->assertSame($tracker, $obj->getTracker());
    }

    /**
     * @covers Schakel\Mail\Mail::convertToPHPMailer
     * @requires extension mbstring
     */
    public function testConvertToPHPMailerWithEmailAndName()
    {
        $name = 'John Doe';
        $email = 'j.doe@example.com';
        $subject = 'You\'re an example!';

        $obj = new Mail;
        $obj->setSubject($subject);
        $obj->setTo($email, $name);

        $phpmail = $obj->convertToPHPMailer();
        $this->assertInstanceOf(\PHPMailer::class, $phpmail);
        $this->assertSame($subject, $phpmail->Subject);

        $recipients = $phpmail->getToAddresses();
        $this->assertCount(1, $recipients);

        $this->assertEquals([$email, $name], reset($recipients));
    }

    /**
     * @covers Schakel\Mail\Mail::convertToPHPMailer
     * @depends testConvertToPHPMailerWithEmailAndName
     */
    public function testConvertToPHPMailerWithJustEmail()
    {
        $email = 'j.doe@example.com';
        $subject = 'You\'re an example!';

        $obj = new Mail;
        $obj->setSubject($subject);
        $obj->setTo($email);

        $phpmail = $obj->convertToPHPMailer();
        $recipients = $phpmail->getToAddresses();
        $this->assertEquals([$email, null], reset($recipients));
    }

    /**
     * @covers Schakel\Mail\Mail::convertToPHPMailer
     * @depends testConvertToPHPMailerWithEmailAndName
     */
    public function testConvertToPHPMailerWithoutRecipient()
    {
        $obj = new Mail;
        $obj->setSubject('Example subject');

        $this->setExpectedException(
            \UnexpectedValueException::class,
            'E-mail has no recipient'
        );
        $obj->convertToPHPMailer();
    }

    /**
     * @covers Schakel\Mail\Mail::convertToPHPMailer
     * @depends testConvertToPHPMailerWithEmailAndName
     */
    public function testConvertToPHPMailerWithoutSubject()
    {
        $obj = new Mail;
        $obj->setTo('john.doe@example.com', 'John Doe');

        $this->setExpectedException(
            \UnexpectedValueException::class,
            'E-mail has no subject'
        );
        $obj->convertToPHPMailer();
    }

    /**
     * Runs a single test to check how the HTML conversion goes. The conversion
     * is delegated to the MailUtils class, and extensive testing on this
     * class is therefore out of scope.
     *
     * @covers Schakel\Mail\Mail::setMailBody
     * @covers Schakel\Mail\Mail::getMailBody
     * @covers Schakel\Mail\Mail::getMailBodyPlainText
     */
    public function testMailBody()
    {
        $templates = [
            'base' => __DIR__ . '/lib/mail.html',
            'html' => __DIR__ . '/lib/mail-emo.html',
            'text' => __DIR__ . '/lib/mail-plain.txt'
        ];

        foreach ($templates as $key => $value) {
            if (file_exists($value) && is_readable($value) && is_file($value)) {
                $templates[$key] = file_get_contents($value);
            } else {
                $templates[$key] = '';
            }
        }

        $obj = new Mail;

        // Set value
        $this->assertNull($obj->setMailBody($templates['base']));

        // Check values
        $this->assertEquals($templates['html'], $obj->getMailBody());
        $this->assertEquals($templates['text'], $obj->getMailBodyPlainText());
    }

    /**
     * Checks if strict type checking works as it should
     *
     * @covers Schakel\Mail\Mail::setMailBody
     */
    public function testMailBodyInvalidType()
    {
        $this->setExpectedException('TypeError');
        $obj = new Mail;
        $obj->setMailBody(['no']);
    }

    /**
     * Checks if empty mail bodies are declined.
     *
     * @covers Schakel\Mail\Mail::setMailBody
     */
    public function testMailBodyEmpty()
    {
        $this->setExpectedException('InvalidArgumentException');
        $obj = new Mail;
        $obj->setMailBody('');
    }

    /**
     * @covers Schakel\Mail\Mail::setSubject
     * @covers Schakel\Mail\Mail::getSubject
     * @dataProvider provideSubject
     */
    public function testSubject($subject, $valid)
    {
        $obj = new Mail;

        if (is_a($valid, \Throwable::class, true)) {
            $this->setExpectedException($valid);
            $obj->setSubject($subject);
        } else {
            $this->assertNull($obj->setSubject($subject));
            if ($valid === true) {
                $valid = $subject;
                $this->assertSame($subject, $obj->getSubject());
            } else {
                $this->assertSame($valid, $obj->getSubject());
            }
        }
    }

    /**
     * @covers Schakel\Mail\Mail::setTo
     * @covers Schakel\Mail\Mail::getTo
     * @dataProvider provideTo
     */
    public function testTo($name, $email, $valid)
    {
        $obj = new Mail;

        if (is_a($valid, \Throwable::class, true)) {
            $this->setExpectedException($valid);
            $obj->setTo($name, $email);
        } else {
            $this->assertNull($obj->setTo($name, $email));
            $this->assertSame($valid, $obj->getTo());
        }
    }

    /**
     * @covers Schakel\Mail\Mail::setTracker
     * @covers Schakel\Mail\Mail::getTracker
     */
    public function testTracker()
    {
        $obj = new Mail;
        $curTracker = $obj->getTracker();
        $newTracker = new MailTracker;

        $this->assertNull($obj->setTracker($newTracker));
        $this->assertNotSame($curTracker, $obj->getTracker());
        $this->assertSame($newTracker, $obj->getTracker());
    }

    /**
     * Provides dummy addresses
     *
     * @return array[]
     */
    public function provideTo()
    {
        $mail = 'john.doe@example.com';
        $name = 'John Doe';
        return [
            // Valid combinations
            'Valid e-mail and name' => [$mail, $name, "{$name} <{$mail}>"],
            'Valid e-mail, no name' => [$mail, null, $mail],
            'Valid e-mail, empty name' => [$mail, '', $mail],
            'Valid e-mail, whitespace name' => [$mail, "\n  ", $mail],

            // Invalid combinations
            'Name as number' => [$mail, 1933, 'TypeError'],
            'Email as null' => [null, null, 'TypeError'],
            'E-mail as number' => [293923, null, 'TypeError']
        ];
    }

    /**
     * Provides example headers
     *
     * @return array[]
     */
    public function provideSubject()
    {
        return [
            'Regular' => ['E-mail subject here', true],
            'Unicode' => ['Greek κόσμε', true],
            'Trimmed' => [' Hello World! ', 'Hello World!']
        ];
    }
}
