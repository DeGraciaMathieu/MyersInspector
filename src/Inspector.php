<?php

namespace DeGraciaMathieu\MyersInspector;

use PhpParser\Node\Stmt\If_ ;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\While_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\ClassMethod;

class Inspector
{
    public function inspect(array $parsed): Inspected
    {
        $complexity = [];

        foreach ($parsed as $class) {
            
            if ($class instanceof Class_)
            {
                $methods = $class->getMethods();

                $complexity[] = [
                    'class_name' => $class->name->name,
                    'methods' => $this->diveIntoMethods($methods),
                ];
            }
        }

        return new Inspected($complexity);
    }

    protected function diveIntoMethods(array $methods): array
    {
        foreach ($methods as $method) {
            
            if ($method instanceof ClassMethod)
            {
                $statements = $method->getStmts();

                $complexity[] = [
                    'method_name' => $method->name->name,
                    'complexity' => $this->diveIntoStatements($statements),
                ];
            }
        }

        return $complexity;
    }

    protected function diveIntoStatements(array $statements): int
    {
        $complexity = 1;

        foreach ($statements as $statement) {
            $complexity += $this->statementExplorer($statement);
        }

        return $complexity;
    }

    protected function statementExplorer($statement, int $depths = 0): int
    {
        $complexity = 0;

        $statements = is_array($statement) ? $statement : [$statement];

        foreach ($statements as $statement) {

            $depths++;

            if (
                $statement instanceof If_ 
                || $statement instanceof Else_
                || $statement instanceof ElseIf_
                || $statement instanceof Foreach_
                || $statement instanceof For_
                || $statement instanceof Switch_
                || $statement instanceof While_
            ) {

                $complexity += 1 * $depths; 

                $insideStatement = $statement->stmts ?? false;

                if ($insideStatement) {
                    $complexity += $this->statementExplorer($insideStatement, $depths);
                }

                $chainingStatements = [
                    $statement->else ?? false,
                    $statement->elseifs ?? false,
                ];

                foreach ($chainingStatements as $chainingStatement) {
                    
                    if ($chainingStatement) {
                        $complexity += $this->statementExplorer($chainingStatement, $depths - 1);
                    }
                }
            }
        }

        return $complexity;
    }
}
