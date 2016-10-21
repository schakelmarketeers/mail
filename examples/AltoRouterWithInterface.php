<?php
declare(strict_types=1);

namespace Schakel\Mail\Demo;

use Schakel\Mail\Router\UrlRouterInterface;
use Schakel\Mail\Router\AltoRouterTrait;

// Create an AltoRouter instance, which uses the
// Schakel\Mail\Router\AltoRouterTrait.

class AltoRouterWithInterface extends \AltoRouter implements UrlRouterInterface
{
    use AltoRouterTrait;
}
