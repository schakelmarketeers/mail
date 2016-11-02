<?php
declare(strict_types=1);

namespace Schakel\Mail\Router;

use Schakel\Mail\Tracker\MailTrackerInterface;

/**
 * Describes a 'generateTrackingUrl' method that is used to convert simple
 * paths and route pointers to endpoints that can be tracked.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
interface UrlRouterInterface
{
    /**
     * @var int Indicates that the URL is a link, which leads to a target page
     * and should mark the mail as interacted with.
     */
    const TYPE_LINK = 1;

    /**
     * @var int Indicates that the URL is an image, which leads to an image file
     * and should mark the mail as opened.
     */
    const TYPE_IMAGE = 2;

    /**
     * Creates a tracker link containing the link type, tracker ID and target
     * URL. Usually in a format like '/track/<type>/<tracker>/<link>'
     *
     * @param MailTrackerInterface $tracker Tracker to use
     * @param int $linkType Type of link, see the TYPE_ constants
     * @param string $path Path to use, or the name of the route to use
     * @param array $arguments Arguments to pass to the router, when using a route
     * @return string
     */
    public function generateTrackingUrl(
        MailTrackerInterface $tracker,
        int $linkType,
        string $path,
        array $arguments = null
    ): string;
}
