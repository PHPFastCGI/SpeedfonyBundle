<?php

namespace PHPFastCGI\SpeedfonyBundle\Tests\Bridge;

use PHPFastCGI\FastCGIDaemon\Http\RequestEnvironmentBuilder;
use PHPFastCGI\SpeedfonyBundle\Bridge\KernelWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class KernelWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string[] 
     */
    protected $params;

    /**
     * @var string
     */
    protected $content;

    public function testKernelWrapper()
    {
        // Build response
        $response = new Response('Hello World', 200);

        // Mock kernel and get wrapper
        $kernelMock = new MockKernel(function (Request $request) use ($response) {
            foreach ($this->params as $name => $value) {
                $this->assertEquals($request->server->get(strtoupper($name)), $value);
            }

            $this->assertEquals($request->query->all(),   ['bar' => 'foo', 'world' => 'hello' ]);
            $this->assertEquals($request->request->all(), ['foo' => 'bar', 'hello' => 'world' ]);
            $this->assertEquals($request->cookies->all(), ['one' => 'two', 'three' => 'four', 'five' => 'six' ]);

            return $response;
        });

        $kernelWrapper = new KernelWrapper($kernelMock);

        // Build request
        $builder = new RequestEnvironmentBuilder();

        $this->params = [
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD'  => 'POST',
            'content_type'    => 'application/x-www-form-urlencoded',
            'REQUEST_URI'     => '/my-page',
            'QUERY_STRING'    => 'bar=foo&world=hello',
            'HTTP_cookie'     => 'one=two; three=four; five=six',
        ];

        $this->content = 'foo=bar&hello=world';

        foreach ($this->params as $name => $value) {
            $builder->addParam($name, $value);
        }

        $builder->addStdin($this->content);

        $requestEnvironment = $builder->getRequestEnvironment();

        // Hand to callback
        $returnedResponse = $kernelWrapper->handleRequest($requestEnvironment);

        $returnedOutput = (
            'HTTP/' . $response->getProtocolVersion() . ' ' . $returnedResponse->getStatusCode() . ' ' . $returnedResponse->getReasonPhrase() . "\r\n" .
            implode("\r\n", $returnedResponse->getHeaderLines()) . "\r\n\r\n" .
            $returnedResponse->getBody()
        );

        $this->assertEquals($returnedOutput, (string) $response);
    }
}
