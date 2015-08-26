<?php

namespace PHPFastCGI\SpeedfonyBundle\Tests\Bridge;

use PHPFastCGI\FastCGIDaemon\Http\Request;
use PHPFastCGI\SpeedfonyBundle\Bridge\KernelWrapper;
use PHPFastCGI\SpeedfonyBundle\Tests\Helper\MockKernel;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class KernelWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testKernelWrapper()
    {
        $stream  = fopen('php://temp', 'r');
        $request = new Request(['REQUEST_URI' => '/hello'], $stream);

        $symfonyResponse = new HttpFoundationResponse('Hello World');

        $kernel = new MockKernel(function (HttpFoundationRequest $symfonyRequest) use ($symfonyResponse) {
            $this->assertEquals('/hello', $symfonyRequest->getRequestUri());
    
            return $symfonyResponse;
        });

        $kernelWrapper = new KernelWrapper($kernel);

        $this->assertEquals($symfonyResponse, $kernelWrapper->handleRequest($request));

        fclose($stream);
    }
}
