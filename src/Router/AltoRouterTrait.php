<?php
declare(strict_types=1);

namespace Schakel\Mail\Router;

/**
 * A trait that incorporates the AltoRouter's `generate` method to generate a
 * tracking url.
 *
 * **IMPORTANT** This assumes that the following route is available:
 * ```php
 * $router->map('GET', '/track/[i:type]/:[:tracker]/[:target]', ..., 'track');
 * ```
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
trait AltoRouterTrait
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
        // Check if the $path is a path or a route name, if it's the latter ask
        // AltoRoute to solve it for us.
        if (!preg_match('/^(\/|(https?|mailto):)/i', $path)) {
            try {
                $url = $this->generate($path, $arguments);
            } catch (\Exception $e) {
                $url = $path;
            }
        }

        // Try to use the 'track' route to build a tracking URL
        try {
            return $this->generate('track', [
                'type' => $linkType,
                'tracker' => $tracker->getId(),
                'target' => base64_encode($url)
            ]);

        // If there's no 'track' method, return the URL, since we should not
        // break the e-mails.
        } catch (\Exception $e) {
            return $url;
        }
    }
}
