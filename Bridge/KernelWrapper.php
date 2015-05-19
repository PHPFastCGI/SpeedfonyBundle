<?php

namespace PHPFastCGI\SpeedfonyBundle\Bridge;

use PHPFastCGI\FastCGIDaemon\KernelInterface;
use PHPFastCGI\FastCGIDaemon\Http\RequestEnvironmentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

class KernelWrapper implements KernelInterface
{
    /**
     * @var Kernel 
     */
    protected $kernel;

    /**
     * Constructor.
     * 
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestEnvironmentInterface $requestEnvironment)
    {
        $server  = $requestEnvironment->getServer();
        $query   = $requestEnvironment->getQuery();
        $post    = $requestEnvironment->getPost();
        $files   = $requestEnvironment->getFiles();
        $cookies = $requestEnvironment->getCookies();
        $content = null;

        $body = $requestEnvironment->getBody();

        if (null !== $body) {
            $content = stream_get_contents($body);
        }

        $request = new Request($query, $post, [], $cookies, $files, $server, $content);

        $response = $this->kernel->handle($request);

        $statusCode = $response->getStatusCode();

        if (isset($response::$statusTexts[$statusCode])) {
            $reasonPhrase = $response::$statusTexts[$statusCode];
        } else {
            $reasonPhrase = '';
        }

        $headers = explode("\r\n", trim((string) $response->headers));

        return new Response($statusCode, $reasonPhrase, $headers, $response->getContent());
    }
}
