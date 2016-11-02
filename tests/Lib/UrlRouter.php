<?php
declare(strict_types=1);

namespace Schakel\Mail\Test\Lib;

use Schakel\Mail\Tracker\MailTrackerInterface;
use Schakel\Mail\Router\UrlRouterInterface;

/**
 * Dummy handler for PHPUnit
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class UrlRouter implements UrlRouterInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateTrackingUrl(
        MailTrackerInterface $tracker,
        int $linkType,
        string $path,
        array $arguments = null
    ): string {
        $type = $linkType == UrlRouterInterface::TYPE_LINK ? 'link' : 'img';

        return sprintf(
            "/%s/%s/%s",
            $type,
            $tracker->getId(),
            urlencode($path)
        );
    }
}
