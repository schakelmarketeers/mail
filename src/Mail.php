<?php
declare(strict_types=1);

namespace Schakel\Mail;

use Schakel\Mail\Tracker\MailTrackerInterface;
use Schakel\Mail\Tracker\MailTracker;

/**
 * Defines a MailInterface, which can send emails to single users and will
 * allow for the tracking system to easily update itself when a mail is updated.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class Mail implements MailInterface
{
    /**
     * @var string Recipient
     */
    protected $to;

    /**
     * @var string subject of the mail
     */
    protected $subject;

    /**
     * @var array HTML and plain-text mail bodies
     */
    protected $bodies;

    /**
     * @var MailTrackerInterface Mail tracker
     */
    protected $tracker;

    /**
     * {@inheritdoc}
     */
    public function __construct(MailTrackerInterface $tracker = null)
    {
        Utils::assertArgumentType($tracker, 'null', MailTrackerInterface::class);

        $this->bodies = [
            'html' => null,
            'plain' => null
        ];

        if ($tracker != null) {
            $this->tracker = $tracker;
        } else {
            $this->tracker = new MailTracker;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPMailer(bool $exceptions = null): \PHPMailer
    {
        if ($this->to === null) {
            throw new \UnexpectedValueException('E-mail has no recipient', self::E_NO_TO);
        }
        if ($this->subject === null) {
            throw new \UnexpectedValueException('E-mail has no subject', self::E_NO_SUBJECT);
        }

        $mailer = new \PHPMailer($exceptions);
        $mailer->Subject = $this->getSubject();

        if (preg_match('/^(.+?) <(.+)>$/', $this->getTo(), $matches)) {
            $mailer->addAddress($matches[2], $matches[1]);
        } else {
            $mailer->addAddress($this->getTo());
        }

        $mailer->isHtml(true);
        $mailer->Body = $this->getMailBody();
        $mailer->AltBody = $this->getMailBodyPlainText();

        return $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailBody()
    {
        return $this->bodies['html'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMailBodyPlainText()
    {
        return $this->bodies['plain'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function getTracker(): MailTrackerInterface
    {
        return $this->tracker;
    }

    /**
     * {@inheritdoc}
     */
    public function setMailBody(string $body)
    {
        Utils::assertArgumentType($body, 'string');

        if (empty(trim($body))) {
            throw new \InvalidArgumentException('Body can not be empty!');
        }

        $this->bodies['html'] = MailUtils::createEmailHtml($body);
        $this->bodies['plain'] = MailUtils::createEmailPlain($body);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(string $subject)
    {
        Utils::assertArgumentType($subject, 'string');

        $this->subject = trim($subject);
        $this->tracker->setSubject($this->subject);
    }

    /**
     * {@inheritdoc}
     */
    public function setTo(string $email, string $name = null)
    {
        Utils::assertArgumentType($email, 'email');
        Utils::assertArgumentType($name, 'null', 'string');

        if ($name !== null && empty(trim($name))) {
            $name = null;
        }

        $this->tracker->setEmail($email);
        if ($name !== null) {
            $this->to = "{$name} <{$email}>";
        } else {
            $this->to = $email;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTracker(MailTrackerInterface $tracker)
    {
        Utils::assertArgumentType($tracker, MailTrackerInterface::class);

        $this->tracker = $tracker;
    }
}
