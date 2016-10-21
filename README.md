# Schakel Marketeers Mail

[![Code Climate rating][shield-cc]][link-cc]
[![PHP 7.0+][shield-php]][php]
[![GPL-3.0 license][shield-license]][license]

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

### Tracking e-mails

To measure user-interaction, this library has a `Schakel\Mail\Tracker\MailTracker`
class that handles linking individual e-mails to user interaction. This way you
can measure when your users click on buttons in e-mails and when images are
loaded (a fairly good sign that users are reading your e-mail).

The `MailTracker` comes with a DCM file to easily use the class in your Doctrine
Entity Manager.

### Use Twig for mail templates

This library also has a package for Twig that'll allow you to twak what URLs
you want to throw through your tracker, and which you want to leave as-is. The
extension provides two functions (`track_url` and `track_image`) that will change
the URL to a trackable URL (when properly provided with the required objects).

## Integrations

### AltoRouter

The examples and the attached `Schakel\Mail\Router\AltoRouterTrait` make it a
breeze to use this library with your AltoRouter installation.

### Doctrine2

The `lib/` directory contains ORM XML constructs that map the
`Schakel\Mail\Tracker\MailTracker` to an object that can be managed by the
Entity Manager. This makes tracking users easy when you use Doctrine, as all
you'll need to do, is load this extra XML file.

<!-- Shield images -->
[shield-cc]: https://img.shields.io/codeclimate/github/SchakelMarketeers/mail.svg
[shield-license]: https://img.shields.io/github/license/SchakelMarketeers/mail.svg
[shield-php]: https://img.shields.io/badge/PHP-7.0%2B-8892BF.svg

<!-- Shield links -->
[link-cc]: https://codeclimate.com/github/SchakelMarketeers/mail

<!-- Files -->
[license]: LICENSE
