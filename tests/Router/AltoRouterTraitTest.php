<?php
declare(strict_types=1);

namespace Schakel\Mail\Test\Router;

use Schakel\Mail\Router\AltoRouterTrait;
use Schakel\Mail\Tracker\MailTrackerInterface;
use Schakel\Mail\Test\Lib\MailTracker;

class AltoRouterTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns a mock for the AltoRouterTrait
     *
     * @return PHPUnit_Framework_MockObject_MockObject Mocked trait
     */
    public function getTrait(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockForTrait(
            AltoRouterTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['generate']
        );
    }

    /**
     * Returns a MailTracker
     *
     * @return MailTrackerInterface
     */
    public function getTracker(int $id): MailTrackerInterface
    {
        $out = new MailTracker;
        $out->setId($id);
        return $out;
    }

    /**
     * Tests what would happen when a URL is recieved
     *
     * @covers Schakel\Mail\Router\AltoRouterTrait::generateTrackingUrl
     */
    public function testUrlConversionTrait()
    {
        // Set variables
        $trackerId = rand(1234, 5678);
        $linkType = rand(1, 10);
        $url = sprintf(
            'https://example.com/random-page/%s/',
            bin2hex(random_bytes(8))
        );
        $urlReturned = 'return-ok';

        $generateArguments = [
            'type' => $linkType,
            'tracker' => $trackerId,
            'target' => base64_encode($url)
        ];

        // Get mocks
        $router = $this->getTrait();
        $tracker = $this->getTracker($trackerId);

        // Set expectations
        $router->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('track'),
                $this->equalTo($generateArguments)
            )
            ->will($this->returnValue($urlReturned));

        $this->assertEquals($urlReturned, $router->generateTrackingUrl(
            $tracker,
            $linkType,
            $url
        ));
    }

    /**
     * Tests what would happen when a tracking route name is recieved
     *
     * @covers Schakel\Mail\Router\AltoRouterTrait::generateTrackingUrl
     */
    public function testRouteConversionTrait()
    {
        // Set variables
        $trackerId = rand(1234, 5678);
        $linkType = rand(1, 10);
        $url = sprintf(
            'https://example.com/random-page/%s/',
            bin2hex(random_bytes(8))
        );
        $routeName = 'route-to-steve';
        $routeReturn = 'steve/routed';
        $urlReturned = 'return-ok';

        $generateArguments = [
            'type' => $linkType,
            'tracker' => $trackerId,
            'target' => base64_encode($routeReturn)
        ];

        // Get mocks
        $router = $this->getTrait();
        $tracker = $this->getTracker($trackerId);

        // Set expectations
        $router->expects($this->exactly(2))
            ->method('generate')
            ->withConsecutive(
                [$this->equalTo($routeName)],
                [$this->equalTo('track'), $this->equalTo($generateArguments)]
            )
            ->willReturn(
                $this->returnValue($routeReturn),
                $this->returnValue($urlReturned)
            );

        $this->assertEquals($urlReturned, $router->generateTrackingUrl(
            $tracker,
            $linkType,
            $routeName
        ));
    }

    /**
     * Tests what would happen when the initial generate would throw an
     * exception. The second generate should recieve the raw route name.
     *
     * @covers Schakel\Mail\Router\AltoRouterTrait::generateTrackingUrl
     */
    public function testRouteConversionFailureTrait()
    {
        // Set variables
        $trackerId = rand(1234, 5678);
        $linkType = rand(1, 10);
        $url = sprintf(
            'https://example.com/random-page/%s/',
            bin2hex(random_bytes(8))
        );
        $routeName = 'route-to-steve';
        $urlReturned = 'return-ok';

        $generateArguments = [
            'type' => $linkType,
            'tracker' => $trackerId,
            'target' => base64_encode($routeName)
        ];

        // Get mocks
        $router = $this->getTrait();
        $tracker = $this->getTracker($trackerId);

        // Set expectations
        $router->expects($this->exactly(2))
            ->method('generate')
            ->withConsecutive(
                [$this->equalTo($routeName)],
                [$this->equalTo('track'), $this->equalTo($generateArguments)]
            )
            ->willReturn(
                $this->throwException(new \RuntimeException),
                $this->returnValue($urlReturned)
            );

        $this->assertEquals($urlReturned, $router->generateTrackingUrl(
            $tracker,
            $linkType,
            $routeName
        ));
    }

    /**
     * Tests what would happen when a URL is recieved and there is no route
     * named 'track'
     *
     * @covers Schakel\Mail\Router\AltoRouterTrait::generateTrackingUrl
     */
    public function testNoTrackRoute()
    {
        // Set variables
        $trackerId = rand(1234, 5678);
        $linkType = rand(1, 10);
        $url = sprintf(
            'https://example.com/random-page/%s/',
            bin2hex(random_bytes(8))
        );

        // Get mocks
        $router = $this->getTrait();
        $tracker = $this->getTracker($trackerId);

        // Set expectations
        $router->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('track')
            )
            ->will($this->throwException(new \RuntimeException));

        $this->assertEquals($url, $router->generateTrackingUrl(
            $tracker,
            $linkType,
            $url
        ));
    }
}
