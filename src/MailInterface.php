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
interface MailInterface
{
    /**
     * @var int Indicates that the e-mail has no recipient yet.
     */
    const E_NO_TO = 1;

    /**
     * @var int Indicates that the e-mail has no subject yet.
     */
    const E_NO_SUBJECT = 2;

    /**
     * Creates a new Mail object, optionally using the given Tracker as a
     * tracker. Creates a tracker of it's own if no tracker is specified.
     *
     * @param MailTrackerInterface $tracker
     */
    public function __construct(MailTrackerInterface $tracker = null);

    /**
     * Returns a PHPMailer object, which only needs to have it's SMTP settings
     * set. All body content is already generated and all CSS is inlined.
     *
     * @param bool|null $exceptions Passed to PHPMailer, if the PHPMailer should
     * use exceptions to handle errors (doesn't by default).
     * @return PHPMailer
     */
    public function convertToPHPMailer(bool $exceptions = null): \PHPMailer;

    /**
     * Returns the body as HTML
     *
     * @return string
     */
    public function getMailBody();

    /**
     * Returns the body as plain text
     *
     * @return string
     */
    public function getMailBodyPlainText();

    /**
     * Returns the subject of the mail
     *
     * @return string
     */
    public function getSubject();

    /**
     * Returns the recipient, as either "j.doe@example.com" or
     * "John Doe <j.doe@example.com>".
     *
     * @return string
     */
    public function getTo();

    /**
     * Returns the tracker that is being used.
     *
     * @param MailTrackerInterface
     */
    public function getTracker(): MailTrackerInterface;

    /**
     * Sets the mail body, send HTML here.
     *
     * @param string $body
     */
    public function setMailBody(string $body);

    /**
     * Sets the subject of the e-mail.
     *
     * @param string $subject
     */
    public function setSubject(string $subject);

    /**
     * Sets the recipient. The first argument MUST be a valid e-mail address.
     * Specifying a name is optional, but recommended.
     *
     * @param string $email
     * @param string $name
     */
    public function setTo(string $email, string $name = null);

    /**
     * Sets the tracker that will be used.
     *
     * @param MailTrackerInterface $tracker
     */
    public function setTracker(MailTrackerInterface $tracker);
}
