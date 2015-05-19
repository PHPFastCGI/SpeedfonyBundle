<?php

namespace PHPFastCGI\SpeedfonyBundle\Command;

use PHPFastCGI\FastCGIDaemon\Daemon;
use PHPFastCGI\SpeedfonyBundle\Bridge\KernelWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

class DaemonRunCommand extends Command
{
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('speedfony:daemon:run')
            ->setDescription('Execute the FCGI daemon')
            ->addOption('target', null, InputOption::VALUE_REQUIRED, 'Socket path');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getOption('target');

        if (null !== $target) {
            $stream = stream_socket_server($target);
        } else {
            $stream = fopen('php://fd/' . Daemon::FCGI_LISTENSOCK_FILENO, 'r');
        }

        $kernelWrapper = new KernelWrapper($this->kernel);

        $daemon = new Daemon($stream);
        $daemon->run($kernelWrapper);
    }
}
