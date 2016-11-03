# Tracking e-mails

This package tracks the interactions with e-mails in two different ways:

1. Images are re-routed through the tracker, which will mark the e-mail as
   opened.
2. Links are routed through the tracker, which will mark the e-mail as acted
   upon.

The 2nd step is very common to use, but the first is less common. Images are
the first thing a user loads when he/she recieves an e-mail and since almost
all big services now route their images through an image 'proxy' that checks
for virusses, a "Click to load images" bar is hardly being used.

This gives this package a great advantage, as you can see *when* a user opened
your e-mail for the first time.

You can show this in a dashboard, but you'll have to design that yourself.

## How the URL builder works

The tracker format used by default in the `Schakel\Mail\Router\AltoRouterTrait`
is named `track` and constructs the URL as follows:
`https://example.com/track/<request type>/<tracker ID>/<endpoint>`. To write it
down a bit more extensively:

 -  `request type` is the type of resource that is being requested. It's either
    an image or a link, which counts as a 'view' and an 'interact' respectively.
    They are identical to the `TYPE_*` constants in
    `Schakel\Mail\Router\UrlRouterInterface`.
 -  `tracker ID` is an integer indicating what tracker to use. It's not very
    unique (just a number, no UUID or anything), but that's no problem as the
    users don't  get to see what's actually in the trackers.
 -  `endpoint` is the path of the endpoint (so without the domain) which is
    base-64 encoded.

An example URL could look like this:
```
https://example.com/track/1/13929/L1NjaGFrZWxNYXJrZXRlZXJzL21haWw=
```
Which would, after updating the tracker, redirect to `/SchakelMarketeers/mail`.

## Writing the tracker

As you can read above, the tracker has a couple of features:

 - Get the tracker with the same ID as the ID mentioned in the URL
 - Check what request type was issued
 - Update either the `opened` or `created` date.
 - Write the changes to the database.
 - Redirect the user to the provided URL.

The code below would do the above tasks, one by one.

:warning: The code below is example code and is **not secure**
<sup>[[1][sec-1]][[2][sec-2]]</sup>. You're free to use it as a foundation for your
tracker implementation, but usage in production is **strongly disadvised**.

```php
<?php
declare(strict_types=1);

namespace MyProject\Tracking;

use Schakel\Mail\Router\UrlRouterInterface;
use Schakel\Mail\Tracker\MailTracker;

/**
 * Handles tracking e-mail interations and redirecting users afterwards.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class TrackHandler
{
    /**
     * Handles a request to the 'track' route.
     *
     * WARNING! This is example code!
     * <strong>**DO NOT USE IN PRODUCTION!**</strong>
     *
     * @param int $type Type of request, an integer
     * @param string $trackerId ID of the tracker
     * @param string $target Base64 encoded URL
     */
    public function handle($type, $trackerId, $target)
    {
        // Get the tracker with the same URL. Assumes you're using Doctrine2 and
        // the EntityManager is available as $em in global scope
        global $em;

        $tracker = $em->find(MailTracker::class, $trackerId);

        // Check the request type, but only if we have a tracker
        if ($tracker !== null) {

            // If we have a LINK, mark the e-mail as 'clicked', else mark it as
            // 'opened'.
            if ($type === UrlRouterInterface::TYPE_LINK) {
                $tracker->setClicked(new \DateTime);
            } else {
                $tracker->setOpened(new \DateTime);
            }

            // Write the changes to the database
            $em->flush();
        }

        // Redirect the user to the requested location.
        $targetPath = base64_decode($target);
        header("Location: {$targetPath}");
    }
}
```

Then we'd have to configure the AltoRouter route. I'm assuming you're using the
closure handler [described on the AltoRouter.com site][altorouter-closure],
which would mean that the tracker would be set up like this:

```php
$trackHandlerInstance = new \MyProject\Tracking\TrackHandler;
$router->map(
    'GET',
    '/track/[i:type]/[:tracker]/[:target]',
    [$trackHandlerInstance, 'handle']
);
```

And there you have it, you can now track links.

[sec-1]: https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Untrusted_data
[sec-2]: https://www.owasp.org/index.php/Unvalidated_Redirects_and_Forwards_Cheat_Sheet
[altorouter-closure]: http://altorouter.com/usage/processing-requests.html
