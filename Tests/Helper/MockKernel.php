<?php

namespace PHPFastCGI\SpeedfonyBundle\Tests\Helper;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

class MockKernel extends Kernel
{
    /**
     * @var callable
     */
    private $callback;

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
    }
}
