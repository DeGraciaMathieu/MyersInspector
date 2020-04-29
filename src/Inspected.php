<?php

namespace DeGraciaMathieu\MyersInspector;

use DeGraciaMathieu\MyersInspector\Utilities\Arr;
use Symfony\Component\Console\Output\OutputInterface;

class Inspected {

    protected array $complexity;

    public function __construct(array $complexity)
    {
        $this->complexity = $complexity;
    }

    public function classesFound(): int
    {
        return count($this->complexity);
    }

    public function methodsFound(): int
    {
        $methods = Arr::pluck($this->complexity, 'methods.*.method_name');

        return count(Arr::flatten($methods));
    }

    public function maxComplexity(): int
    {
        $complexity = Arr::pluck($this->complexity, 'methods.*.complexity');

        $flatted = Arr::flatten($complexity);

        return max($flatted);
    }

    public function avgComplexity(): int
    {
        $complexity = Arr::pluck($this->complexity, 'methods.*.complexity');

        $flatted = Arr::flatten($complexity);

        return array_sum($flatted) / count($flatted);
    }
}
