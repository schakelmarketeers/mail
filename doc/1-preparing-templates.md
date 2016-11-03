# Preparing templates

Before you can track users, you need to prepare the templates for tracking.
This package has a Twig extension for you, that you can easily use to prepare
the templates for tracking.

## Making the TwigExtension available for Twig

The extension registers 2 new functions that can be used to render links that
can be tracked. The extension does, however, require a `UrlRouterInterface` and
a `MailTrackerInterface` to be available on creation.

A way to do this, is to create the tracker and the template, before creating
the `Mail` object.

For example:

```php
// Ready the dependancies
use Schakel\Mail\Tracker\MailTracker;
use Schakel\Mail\Extension\TwigExtension;

// Create a new tracker
$mailTracker = new MailTracker;

// I'm assuming your AltoRouter instance is available as a global variable named
// $router.
global $router;

// [create Twig environment]

$twig->addExtension(new TwigExtension($router, $mailTracker));

$mail = new Mail($mailTracker);

// [Set recipient etc...]

$mail->setMailBody($twig->render('my-mail-template.twig'));
```

And now your mails contain templates that have the `TwigExtension` that can
create links that can be tracked.

But how do you track templates?

## Modifying templates

As said above, the extension registers 2 new functions. These functions are:

 - `track_url` to track links the user can click on
 - `track_image` to track images that load when the mail is opened.

For best effect, it's recommended to apply the `track_image` method on the
header logo and to put each link through the `track_url` so you can Always see
how a user arrived on the site.

For example:

```twig
<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>

  <body>
    <div class="header">
      <div class="logo">
        <a href="http://example.com{{ track_url('home') }}">
          <img src="http://example.com{{ track_image('assets/logo.png') }}" width="300" height="60" />
        </a>
      </div>
    </div>

    <div class="body">
      {% block content %} {% endblock %}
    </div>

    <div class="footer">
      &copy; My Website. All Rights Reserved.<br />
      <a href="http://example.com{{ track_url('mail-unsubscribe')) }}">Unsubscribe</a>
    </div>
  </body>

</html>
```

Note that the default implementation does not provide a domain name, so you
need to add that before each anchor `href` or image `src`. You can create a
modified `Schakel\Mail\Router\UrlRouterInterface` to add the domain, but that's
up to you.
