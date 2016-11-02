<?php
declare(strict_types=1);

namespace Schakel\Mail\Extension;

use Schakel\Mail\Utils;
use Schakel\Mail\Router\UrlRouterInterface;
use Schakel\Mail\Tracker\MailTrackerInterface;

/**
 * Tracks when e-mail is sent, opened and when a user clicks a link
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @var UrlRouterInterface
     */
    protected $router;

    /**
     * @var MailTrackerInterface
     */
    protected $tracker;

    /**
     * Creates a new TwigExtension with the given tracker as tracker.
     *
     * @param UrlRouterInterface $router Router that can transform regular paths
     * into track-able paths
     * @param MailTrackerInterface $tracker Tracker to use in this template.
     */
    public function __construct(
        UrlRouterInterface $router,
        MailTrackerInterface $tracker
    ) {
        $this->router = $router;
        $this->tracker = $tracker;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Schakel Marketeers Mail Tracker Extension';
    }

    /**
     * Returns a track-able link to a link, which will mark the e-mail as
     * acted upon.
     *
     * @param string $path Name or path to track.
     * @param array $args Optional arguments to pas to the router
     * @return string Absolute link to the resource.
     */
    public function getLinkRoute($path, array $args = []): string
    {
        return $this->router->generateTrackingUrl(
            $this->tracker,
            UrlRouterInterface::TYPE_LINK,
            $path,
            $args
        );
    }

    /**
     * Returns a track-able link to an image, which will mark the e-mail as
     * shown.
     *
     * @param string $path Name or path to track.
     * @return string Absolute link to the resource.
     */
    public function getImageRoute($path): string
    {
        return $this->router->generateTrackingUrl(
            $this->tracker,
            UrlRouterInterface::TYPE_IMAGE,
            $path
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('track_url', [$this, 'getLinkRoute']),
            new \Twig_SimpleFunction('track_image', [$this, 'getImageRoute'])
        ];
    }
}
