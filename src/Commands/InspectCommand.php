<?php

namespace DeGraciaMathieu\MyersInspector\Commands;

use DeGraciaMathieu\MyersInspector\Printer;
use DeGraciaMathieu\MyersInspector\Inspector;
use DeGraciaMathieu\MyersInspector\FileParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InspectCommand extends Command
{
    protected static $defaultName = 'inspect';

    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'File path.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        $parsed = (new FileParser())->parse($file);

        $inspected = (new Inspector)->inspect($parsed);

        (new Printer($output))->output($inspected);

        return 1;
    }
}
