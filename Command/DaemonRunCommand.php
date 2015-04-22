<?php

namespace PHPFastCGI\SpeedfonyBundle\Command;

use PHPFastCGI\FastCGIDaemon\SocketDaemon;
use PHPFastCGI\FastCGIDaemon\StreamSocketDaemon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
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
            ->addArgument('cycles', InputArgument::OPTIONAL, 'Request cycles to live for, 0 means infinite (default is 20)', 20)
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'Port to listen on');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $getIntegerInput = function ($type, $name, $minimumValue) use ($input) {
            if ('argument' === $type) {
                $value = (string) $input->getArgument($name);
            } elseif ('option' === $type) {
                $value = (string) $input->getOption($name);
            } else {
                throw new \LogicException('Unknown input type: ' . $type);
            }

            $intValue = (int) $value;

            if ((string) $intValue !== $value) {
                throw new \Exception('The ' . $argument . ' argument must be an integer');
            } elseif ($value < $minimumValue) {
                throw new \Exception('The ' . $argument . ' argument must be at least ' . $minimumValue);
            }

            return $value;
        };

        if (null !== $input->getOption('port')) {
            $daemon = new SocketDaemon($getIntegerInput('option', 'port', 0));
        } else {
            $daemon = new StreamSocketDaemon();
        }

        $maximumCycles = $getIntegerInput('argument', 'cycles', 0);

        for($cycles = 0; ($maximumCycles == 0) || $cycles < $maximumCycles; $cycles++) {
            $request = $daemon->getRequest();

            $httpRequest = new Request(array(), array(), array(), array(),
                array(), $request->getServer(), $request->getContent());

            $response = str_replace(
                array('HTTP/1.1 ', 'HTTP/1.0 '),
                array('Status: ',  'Status: '),
                (string) $this->kernel->handle($httpRequest)
            );

            $request->respond($response);
        }
    }
}
