<?php

namespace DeGraciaMathieu\MyersInspector;

use DeGraciaMathieu\MyersInspector\Inspected;
use Symfony\Component\Console\Output\OutputInterface;

class Printer {

    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function output(Inspected $inspected): void
    {
        $this->output->writeln(null);
        $this->output->writeln("❀ Myer's Cyclomatic Complexity Checker ❀");
        $this->output->writeln(null);
        $this->output->write("Conclusion... ");
        $this->getConclusion($inspected);
        $this->output->writeln(null);
        $this->output->writeln($inspected->classesFound() ." classe(s) checked.");
        $this->output->writeln($inspected->methodsFound() . " method(s) found.");
        $this->output->writeln(null);
        $this->getMaxComplexity($inspected);
        $this->getAvgComplexity($inspected);
    }

    protected function getConclusion(Inspected $inspected): void
    {
        $complexity = $inspected->avgComplexity();

        switch (true) {
            case $complexity < 6:
                $message = "It's good !";
                $font = 'black';
                $background = 'green';
                break;
            case $complexity < 8:
                $message = "It's ok.";
                $font = 'black';
                $background = 'yellow';
                break;
            default:
                $message = "That may be a problem.";
                $font = 'white';
                $background = 'red';
                break;
        }

        $line = sprintf('<fg=%s;bg=%s>%s</>', $font, $background, $message);

        $this->output->writeln($line);
    }

    protected function getMaxComplexity(Inspected $inspected): void
    {
        $complexity = $inspected->maxComplexity();

        switch (true) {
            case $complexity < 8:
                $font = 'black';
                $background = 'green';
                break;
            case $complexity < 10:
                $font = 'black';
                $background = 'yellow';
                break;
            default:
                $font = 'white';
                $background = 'red';
                break;
        }

        $line = sprintf('Max complexity : <fg=%s;bg=%s> %s </>', $font, $background, $complexity);

        $this->output->writeln($line);
    }

    protected function getAvgComplexity(Inspected $inspected): void
    {
        $complexity = $inspected->avgComplexity();

        switch (true) {
            case $complexity < 6:
                $font = 'black';
                $background = 'green';
                break;
            case $complexity < 8:
                $font = 'black';
                $background = 'yellow';
                break;
            default:
                $font = 'white';
                $background = 'red';
                break;
        }

        $line = sprintf('Average complexity : <fg=%s;bg=%s> %s </>', $font, $background, $complexity);

        $this->output->writeln($line);
    }
}
