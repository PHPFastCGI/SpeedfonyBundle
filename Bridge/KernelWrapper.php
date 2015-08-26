<?php

namespace PHPFastCGI\SpeedfonyBundle\Bridge;

use PHPFastCGI\FastCGIDaemon\Http\RequestInterface;
use PHPFastCGI\FastCGIDaemon\KernelInterface;
use Symfony\Component\HttpKernel\Kernel;

class KernelWrapper implements KernelInterface
{
    /**
     * @var Kernel 
     */
    private $kernel;

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
    public function handleRequest(RequestInterface $request)
    {
        $symfonyRequest = $request->getHttpFoundationRequest();

        $symfonyResponse = $this->kernel->handle($symfonyRequest);
        $this->kernel->terminate($symfonyRequest, $symfonyResponse);

        return $symfonyResponse;
    }
}
