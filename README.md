# Schakel Marketeers Mail

[![Travis CI build status][shield-1]][link-1]
[![Code Climate rating][shield-2]][link-2]
[![Code Coverage][shield-3]][link-3]
[![PHP 7.0+][shield-4]][php]
[![GPL-3.0 license][shield-5]][license]

Mail package that handles inlining CSS, creating plain-text versions of mails
and tracking when users open and interact with mails.

## Installation

Just require the package using Composer.

```
composer require schakel/mail
```

Examples can be found in the `examples` directory.

## Features

This library has 3 main features:

### Preparing e-mails

Prepping e-mails can be a pain, and so can inlining stylesheets and other
anomalies that the Wonderful World Of E-mail™ contains.

This library has a `Schakel\Mail\MailUtils` class that can take some of the
pain away, but it gets even easier when you use the `Schakel\Mail\Mail` class
that comes packed with this library, as it'll install a tracker for you at the
same time.

The `Schakel\Mail\Mail` class contains a `convertToPHPMailer` method that
returns a PHPMailer object. Other third-party extensions can easily be added as
all the required information is scoped to be publicly available.

### Tracking e-mails

To measure user-interaction, this library has a
`Schakel\Mail\Tracker\MailTracker` class that handles linking individual
e-mails to user interaction. This way you can measure when your users click on
buttons in e-mails and when images are loaded (a fairly good sign that users
are reading your e-mail).

The `MailTracker` comes with a DCM file to easily use the class in your
Doctrine Entity Manager.

### Use Twig for mail templates

This library also has a package for Twig that'll allow you to tweak what URLs
you want to throw through your tracker, and which you want to leave as-is. The
extension provides two functions (`track_url` and `track_image`) that will
change the URL to a trackable URL (when properly provided with the required
objects).

## Integrations

### [AltoRouter][integration-1]

The examples and the attached `Schakel\Mail\Router\AltoRouterTrait` make it a
breeze to use this library with your AltoRouter installation.

### [Doctrine2][integration-2]

The `lib/` directory contains ORM XML constructs that map the
`Schakel\Mail\Tracker\MailTracker` to an object that can be managed by the
Entity Manager. This makes tracking users easy when you use Doctrine, as all
you'll need to do, is load this extra XML file.

### [Twig][integration-3]

There's a Twig template plugin available, which you can load using the
`addPlugin` method on your `TwigEnvironment`. This adds the functions
`track_url` and `track_image` to your template engine, which will generate
*relative* urls to your tracking endpoint. See the example for more
implementation of how to implement the tracker.

<!-- Shield images -->
[shield-1]: https://img.shields.io/travis/SchakelMarketeers/mail.svg
[shield-2]: https://img.shields.io/codeclimate/github/SchakelMarketeers/mail.svg
[shield-3]: https://img.shields.io/codeclimate/coverage/github/SchakelMarketeers/mail.svg
[shield-4]: https://img.shields.io/badge/PHP-7.0%2B-8892BF.svg
[shield-5]: https://img.shields.io/github/license/SchakelMarketeers/mail.svg

<!-- Shield links -->
[link-1]: https://travis-ci.org/SchakelMarketeers/mail
[link-2]: https://codeclimate.com/github/SchakelMarketeers/mail
[link-3]: https://codeclimate.com/github/SchakelMarketeers/mail/coverage

<!-- Files -->
[license]: LICENSE

<!-- External links -->
[php]: https://secure.php.net/supported-versions.php
[integration-1]: http://altorouter.com/
[integration-2]: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/
[integration-3]: http://twig.sensiolabs.org/
