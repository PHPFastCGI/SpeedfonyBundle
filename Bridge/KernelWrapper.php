<?php

namespace PHPFastCGI\SpeedfonyBundle\Bridge;

use PHPFastCGI\FastCGIDaemon\KernelInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\Kernel;

class KernelWrapper implements KernelInterface
{
    /**
     * @var Kernel 
     */
    protected $kernel;

    /**
     * @var HttpFoundationFactoryInterface
     */
    protected $symfonyMessageFactory;

    /**
     * @var HttpMessageFactoryInterface
     */
    protected $psrMessageFactory;

    /**
     * Constructor.
     * 
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel, HttpFoundationFactoryInterface $symfonyMessageFactory, HttpMessageFactoryInterface $psrMessageFactory)
    {
        $this->kernel                = $kernel;
        $this->symfonyMessageFactory = $symfonyMessageFactory;
        $this->psrMessageFactory     = $psrMessageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(ServerRequestInterface $request)
    {
        $symfonyRequest = $this->symfonyMessageFactory->createRequest($request);;

        $symfonyResponse = $this->kernel->handle($symfonyRequest);
        $this->kernel->terminate($symfonyRequest, $symfonyResponse);

        return $this->psrMessageFactory->createResponse($symfonyResponse);
    }
}
