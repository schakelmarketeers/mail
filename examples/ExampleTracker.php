<?php
/**
 * Demonstration of e-mail tracker.
 *
 * The demonstration below is just to give a crude overview of what the tracker
 * can do. **IT IS NOT RECOMMENDED TO USE THE BELOW CODE AS-IS**, there are
 * ample of remarks to be made concerning the security of the script, especially
 * with the unsafe redirect on line 58.
 *
 * Don't blindly copy code.
 *
 * @license GPL v3.0
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
declare(strict_types=1);

namespace Schakel\Mail\Demo;

use Schakel\Mail\Router\UrlRouterInterface;
use Schakel\Mail\Tracker\MailTracker;

// See AltoRouterWithInterface.php
$router = new AltoRouterWithInterface;

// Mapa a route
$router->map('GET', '/track/[i:type]/:[:tracker]/[:target]', function ($params) {
    // We're using the Doctrine2 Entity Manager, just as an example.
    global $entityManager;

    // Get variables from AltoRouter
    $type = $params['type'];
    $trackerId = $params['tracker'];
    $target = $params['target'];

    // Find tracker in entity manager
    $tracker = $entityManager->find(MailTracker::class, $trackerId);

    // If tracker still exists
    if ($tracker) {
        // Mark as clicked on a link
        if ($type === UrlRouterInterface::TYPE_LINK) {
            $tracker->setClicked(new \DateTime);

        // Mark as seen on an image
        } else {
            $tracker->setOpened(new \DateTime);
        }

        // Save changes in entity manager
        $entityManager->flush();
    }

    // Decode URL from the request.
    // SECURITY RISK! Only for demonstration purposes!
    $urlDecoded = base64_decode($target);

    // Redirect user to targt destination
    header("Location: {$urlDecoded}");
}, 'track');

// Rest of code omitted to keep it simple.
//
// For more information about AltoRouter, see
// http://altorouter.com/
