<?php
declare(strict_types=1);

namespace Schakel\Mail\Tracker;

use Schakel\Mail\Utils;

/**
 * Tracks when e-mail is sent, opened and when a user clicks a link
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class MailTracker implements MailTrackerInterface
{
    /**
     * @var int
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $email = null;

    /**
     * @var string
     */
    protected $subject = null;

    /**
     * @var DateTime
     */
    protected $sent = null;

    /**
     * @var DateTime
     */
    protected $opened = null;

    /**
     * @var DateTime
     */
    protected $clicked = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return DateTime
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @return DateTime|null
     */
    public function getOpened()
    {
        return $this->opened;
    }

    /**
     * @return DateTime|null
     */
    public function getClicked()
    {
        return $this->clicked;
    }

    /**
     * @var string $email
     * @throws TypeError if $email is not a valid e-mail address
     */
    public function setEmail(string $email)
    {
        Utils::assertArgumentType($email, 'email');
        $this->email = $email;
    }

    /**
     * @var string $subject
     * @throws TypeError if $subject is not of type string
     */
    public function setSubject($subject)
    {
        Utils::assertArgumentType($subject, 'null', 'string');
        $this->subject = $subject;
    }

    /**
     * @var DateTime $sent
     * @throws TypeError if $sent is not of type DateTime
     */
    public function setSent($sent)
    {
        Utils::assertArgumentType($sent, 'null', 'date');
        $this->sent = $sent;
    }

    /**
     * @var DateTime $opened
     * @throws TypeError if $opened is not of type DateTime
     */
    public function setOpened($opened)
    {
        Utils::assertArgumentType($opened, 'null', 'date');
        $this->opened = $opened;
    }

    /**
     * @var DateTime $clicked
     * @throws InvalidArgumentException if $clicked is not of type DateTime
     */
    public function setClicked($clicked)
    {
        Utils::assertArgumentType($clicked, 'null', 'date');
        $this->clicked = $clicked;
    }
}
