<?php
declare(strict_types=1);

namespace Schakel\Mail\Tracker;

use Schakel\Mail\Utils;

/**
 * Describes methods an e-mail tracker should have.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
interface MailTrackerInterface
{
    /**
     * @var int Indicates that a method has received an invalid typed argument.
     */
    const E_INVALID_TYPE = 1;

    /**
     * @var int Indicates that the given e-mail was invalid.
     */
    const E_INVALID_MAIL = 4;

    /**
     * Returns the id associated with this tracker.
     *
     * @return int
     */
    public function getId();

    /**
     * Returns the e-mail address associated with this tracker.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Returns the subject associated with this tracker.
     *
     * @return string
     */
    public function getSubject();

    /**
     * Returns the date on which the email was sent.
     * @return DateTime
     */
    public function getSent();

    /**
     * Returns the date on which the email was opened.
     * @return DateTime|null
     */
    public function getOpened();

    /**
     * Returns the date on which the email was clicked.
     * @return DateTime|null
     */
    public function getClicked();

    /**
     * Assigns the e-mail address associated with this tracker.
     *
     * @var string $email
     * @throws TypeError if $email is not a valid e-mail address
     */
    public function setEmail(string $email);

    /**
     * Assigns the subject associated with this tracker.
     *
     * @var string $subject
     * @throws TypeError if $subject is not of type string
     */
    public function setSubject($subject);

    /**
     * Sets the date at which the mail associated with this tracker was sent.
     *
     * @var DateTime $sent
     * @throws TypeError if $sent is not of type DateTime
     */
    public function setSent(\DateTime $sent);

    /**
     * Sets the date at which the mail associated with this tracker was opened.
     *
     * @var DateTime $opened
     * @throws TypeError if $opened is not of type DateTime
     */
    public function setOpened(\DateTime $opened);

    /**
     * Sets the date at which the mail associated with this tracker was clicked.
     *
     * @var DateTime $clicked
     * @throws InvalidArgumentException if $clicked is not of type DateTime
     */
    public function setClicked(\DateTime $clicked);
}
