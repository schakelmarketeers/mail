<?php
declare(strict_types=1);

namespace Schakel\Mail\Test\Lib;

use Schakel\Mail\Tracker\MailTracker as BaseMailTracker;

/**
 * Same as Schakel\Mail\Tracker, but allows to set the ID. For testing only.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class MailTracker extends BaseMailTracker
{
    public function setId(int $id)
    {
        return $this->id;
    }
}
