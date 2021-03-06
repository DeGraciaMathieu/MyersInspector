<?php

namespace DeGraciaMathieu\MyersInspector;

use PhpParser\Error;
use PhpParser\ParserFactory;

class FileParser
{
    protected ParserFactory $parserFactory;

    public function __construct()
    {
        $this->parserFactory = new ParserFactory();
    }

    public function parse(string $file): array
    {
        try {

            $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7);

            $buffer = file_get_contents($file);

            return $parser->parse($buffer);

        } catch (Error $error) {

        }
    }
}
