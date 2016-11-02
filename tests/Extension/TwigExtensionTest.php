<?php
declare(strict_types=1);

namespace Schakel\Mail\Test\Extension;

use Schakel\Mail\Extension\TwigExtension;
use Schakel\Mail\Test\Lib\UrlRouter;
use Schakel\Mail\Router\UrlRouterInterface;
use Schakel\Mail\Test\Lib\MailTracker;

class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected static $extensionFunctions = [
        'track_url',
        'track_image'
    ];
    /**
     * Returs a function with the given name, or throws an UnderflowException if
     * the given function does not exist.
     *
     * @param string $name Name of the function to find
     * @param array $functions List of functions returned by getFunctions()
     * @return Twig_SimpleFunction function requested
     * @throws UnderflowException if a function named $name cannot be found
     */
    protected function findFunctionByName(string $name, array $functions): \Twig_SimpleFunction
    {
        foreach ($functions as $function) {
            if (!is_object($function)) {
                continue;
            }
            if (!$function instanceof \Twig_SimpleFunction) {
                continue;
            }
            if ($function->getName() !== $name) {
                continue;
            }
            return $function;
        }

        throw new \UnderflowException(sprintf(
            'Cannot find a function named "%s"',
            $name
        ));
    }
    /**
     * @covers Schakel\Mail\Extension\TwigExtension::__construct
     */
    public function testConstructor()
    {
        $tracker = new MailTracker;
        $router = new UrlRouter;

        $obj = new TwigExtension($router, $tracker);

        // Check if setter in constructor works
        $this->assertAttributeSame($tracker, 'tracker', $obj);
        $this->assertAttributeSame($router, 'router', $obj);

        // Make sure the plugin is a Twig extension (simple check, but still)
        $this->assertInstanceOf('Twig_Extension', $obj);

        // Start checking the functions
        $functions = $obj->getFunctions();

        // Make sure we only have registered functions.
        $this->assertInternalType('array', $functions);
        $this->assertCount(count(self::$extensionFunctions), $functions);

        // Make sure only the functions in $validFunctions exist.
        foreach ($functions as $function) {
            $this->assertInstanceOf('Twig_SimpleFunction', $function);
            $this->assertContains($function->getName(), self::$extensionFunctions);
        }
    }

    protected function runTwigFunctionTest(
        string $function,
        $routingType,
        array $extraArguments = []
    ) {
        // Set arguments, return values
        $path = sprintf('path-to-route-%s', time());
        $retValue = sprintf('#%s-not-found', time());

        // Create objects
        $tracker = new MailTracker;
        $router = $this->getMockBuilder(UrlRouter::class)
            ->setMethods(['generateTrackingUrl'])
            ->getMock();

        // Prepare expected arguments
        $expectationArguments = [
            $this->identicalTo($tracker),
            $this->identicalTo($routingType),
            $this->identicalTo($path)
        ];
        $invokementArguments = [$path];

        foreach ($extraArguments as $extraArgument) {
            $expectationArguments[] = $this->identicalTo($extraArgument);
            $invokementArguments[] = $extraArgument;
        }

        // Set expectations on router
        $router->expects($this->once())
            ->method('generateTrackingUrl')
            ->withConsecutive($expectationArguments)
            ->will($this->returnValue($retValue));

        // Create extension and find 'track_url'
        $obj = new TwigExtension($router, $tracker);
        $func = $this->findFunctionByName($function, $obj->getFunctions());

        // Run the test
        $this->assertEquals(
            $retValue,
            call_user_func_array($func->getCallable(), $invokementArguments)
        );
    }

    /**
     * @covers Shakel\Mail\Extension\TwigExtension::getLinkRoute
     */
    public function testTrackUrl()
    {
        $this->runTwigFunctionTest(
            'track_url',
            UrlRouterInterface::TYPE_LINK,
            [['cake' => 'yes', 'boo' => false]]
        );
    }

    /**
     * @covers Shakel\Mail\Extension\TwigExtension::getImageRoute
     */
    public function testTrackImage()
    {
        $this->runTwigFunctionTest(
            'track_image',
            UrlRouterInterface::TYPE_IMAGE
        );
    }
}
