<?php

namespace PHPFastCGI\SpeedfonyBundle\Tests\Bridge;

use PHPFastCGI\SpeedfonyBundle\Bridge\KernelWrapper;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Zend\Diactoros\ServerRequestFactory;

class KernelWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testKernelWrapper()
    {
        $globals = array(
            'server' => array(
                'REQUEST_URI' => '/uri'
            ),
            'query' => array(
                'foo' => 'bar'
            ),
            'post' => array(
                'bar' => 'foo'
            )
        );

        $psrRequest      = ServerRequestFactory::fromGlobals($globals['server'], $globals['query'], $globals['post']);
        $symfonyResponse = new Response('Hello World');

        $kernel = new MockKernel(function (Request $symfonyRequest) use ($globals, $symfonyResponse) {
            $this->assertEquals($globals['server'], $symfonyRequest->server->all());
            $this->assertEquals($globals['query'],  $symfonyRequest->query->all());
            $this->assertEquals($globals['post'],   $symfonyRequest->request->all());

            $this->assertEquals($globals['server']['REQUEST_URI'], $symfonyRequest->getRequestUri());
    
            return $symfonyResponse;
        });

        $kernelWrapper = new KernelWrapper($kernel, new HttpFoundationFactory(), new DiactorosFactory());

        $psrResponse = $kernelWrapper->handleRequest($psrRequest);

        $this->assertEquals($symfonyResponse->getContent(),    (string) $psrResponse->getBody());
        $this->assertEquals($symfonyResponse->getStatusCode(), $psrResponse->getStatusCode());
    }
}

class MockKernel extends Kernel
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * Constructor.
     * 
     * @param callable $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;

        parent::__construct('dev', false);
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = 1, $catch = true)
    {
        return call_user_func($this->callback, $request);

        $psrResponse = $kernelWrapper->handleRequest($psrRequest);

        $this->assertEquals($symfonyResponse->getContent(),    (string) $psrResponse->getBody());
        $this->assertEquals($symfonyResponse->getStatusCode(), $psrResponse->getStatusCode());
    }
}
