<?php

namespace PHPFastCGI\SpeedfonyBundle\Command;

use PHPFastCGI\FastCGIDaemon\SocketDaemon;
use PHPFastCGI\FastCGIDaemon\StreamSocketDaemon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('socket', InputArgument::OPTIONAL, 'The socket stream to listen on')
            ->addArgument('port',   InputArgument::OPTIONAL, 'Port to listen on')
            ->addArgument('cycles', InputArgument::OPTIONAL, 'Request cycles to live for, 0 means infinite', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $getIntegerArgument = function ($argument, $minimumValue) use ($input) {
            $value    = (string) $input->getArgument($argument);
            $intValue = (int)    $value;

            if ((string) $intValue !== $value) {
                throw new \Exception('The ' . $argument . ' argument must be an integer');
            } elseif ($value < $minimumValue) {
                throw new \Exception('The ' . $argument . ' argument must be at least ' . $minimumValue);
            }

            return $value;
        };

        if ($input->hasArgument('socket')) {
            $daemon = new StreamSocketDaemon($input->getArgument('socket'));
        } elseif ($input->hasArgument('port')) {
            $daemon = new SocketDaemon($getIntegerArgument('port', 0));
        } else {
            throw new \Exception('You must specify either the socket or port argument');
        }

        $maximumCycles = $getIntegerArgument('cycles', 0);

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
