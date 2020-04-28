<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use DeGraciaMathieu\MyersInspector\Commands\InspectCommand;

$application = new Application();

$command = $application->add(new InspectCommand());

$application->setDefaultCommand($command->getName());

$application->run();
