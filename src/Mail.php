<?php
declare(strict_types=1);

namespace Schakel\Mail;

use Schakel\Mail\Tracker\MailTrackerInterface;

/**
 * Defines a MailInterface, which can send emails to single users and will
 * allow for the tracking system to easily update itself when a mail is updated.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class Mail extends MailInterface
{
    protected $to;

    protected $subject;

    protected $bodies;

    protected $tracker;

    public function __construct()
    {
        Utils::assertArgumentType($tracker, MailTrackerInterface::class);

        $this->bodies = [
            'html' => null,
            'plain' => null
        ];

        $this->tracker = new MailTracker;
    }

    /**
     * Returns a PHPMailer object, which only needs to have it's SMTP settings
     * set. All body content is already generated and all CSS is inlined.
     *
     * @return PHPMailer
     */
    public function convertToPHPMailer(): \PHPMailer
    {
        if ($this->to === null) {
            throw new \UnexpectedValueException('E-mail has no recipient', self::E_NO_TO);
        }
        if ($this->subject === null) {
            throw new \UnexpectedValueException('E-mail has no subject', self::E_NO_SUBJECT);
        }

        $mailer = new \PHPMailer;
        $mailer->Subject = $this->getSubject();
        $mailer->addAddress($this->to);

        $mailer->isHtml(true);
        $mailer->Body = $this->getMailBody();
        $mailer->AltBody = $this->getMailBodyPlainText();

        return $mailer;
    }

    /**
     * Returns the body as HTML
     *
     * @return string
     */
    public function getMailBody()
    {
        return $this->bodies['html'];
    }

    /**
     * Returns the body as plain text
     *
     * @return string
     */
    public function getMailBodyPlainText()
    {
        return $this->bodies['plain'];
    }

    /**
     * Returns the subject of the mail
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the recipient, as either "j.doe@example.com" or
     * "John Doe <j.doe@example.com>".
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Returns the tracker that is being used.
     *
     * @param MailTrackerInterface
     */
    public function getTracker(): MailTrackerInterface
    {
        return $this->tracker;
    }

    /**
     * Sets the mail body, send HTML here.
     *
     * @param string $body
     */
    public function setMailBody(string $body)
    {
        Utils::assertArgumentType($body, 'string');

        $this->bodies['html'] = MailUtils::createEmailHtml($body);
        $this->bodies['plain'] = MailUtils::createEmailPlain($body);
    }

    /**
     * Sets the subject of the e-mail.
     *
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        Utils::assertArgumentType($subject, 'string');

        $this->subject = trim($subject);
        $this->tracker->setSubject($this->subject);
    }

    /**
     * Sets the recipient. The first argument MUST be a valid e-mail address.
     * Specifying a name is optional, but recommended.
     *
     * @param string $email
     * @param string $name
     */
    public function setTo(string $email, string $name = null)
    {
        Utils::assertArgumentType($email, 'email');
        Utils::assertArgumentType($name, 'null', 'string');

        if ($name !== null && empty(trim($name))) {
            $name = null;
        }

        $this->tracker->setTo($email);
        if ($name !== null) {
            $this->to = "{$name} <{$email}>";
        } else {
            $this->to = $email;
        }
    }

    /**
     * Sets the tracker that will be used.
     *
     * @param MailTrackerInterface $tracker
     */
    public function setTracker(MailTrackerInterface $tracker)
    {
        Utils::assertArgumentType($tracker, MailTrackerInterface::class);

        $this->tracker = $tracker;
    }
}
