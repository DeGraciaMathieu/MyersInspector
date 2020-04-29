<?php

namespace DeGraciaMathieu\MyersInspector;

use PhpParser\Node\Stmt\If_ ;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\While_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\ClassMethod;

class Inspector
{
    public function inspect(array $parsed): Inspected
    {
        $inspected = array_map(
            fn ($unknow) => $this->diveIntoUnknown($unknow)
        , $parsed);

        return new Inspected($inspected);
    }

    public function diveIntoUnknown($unknows)
    {
        $unknows = is_array($unknows) ? $unknows : [$unknows] ;

        foreach ($unknows as $unknow) {

            if ($unknow instanceof Namespace_)
            {
                $insideUnknow = $unknow->stmts;

                $inspected[] = [
                    'namespace' => $unknow->name->toString(),
                    'classes' => $this->diveIntoUnknown($insideUnknow),
                ];
            }

            if ($unknow instanceof Class_)
            {
                $methods = $unknow->getMethods();

                $inspected[] = [
                    'class_name' => $unknow->name->name,
                    'methods' => $this->diveIntoMethods($methods),
                ];
            }
        }

        return $inspected;
    }

    protected function diveIntoMethods(array $methods): array
    {
        foreach ($methods as $method) {
            
            if ($method instanceof ClassMethod)
            {
                $statements = $method->getStmts();

                $inspected[] = [
                    'method_name' => $method->name->name,
                    'complexity' => $this->diveIntoStatements($statements),
                ];
            }
        }

        return $inspected;
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

            if ($statement instanceof Expression) {

                $insideExpression = $statement->expr;

                $complexity += $this->expressionExplorer($insideExpression, $depths);
            }

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

    protected function expressionExplorer($expression, int $depths)
    {
        $complexity = 0;

        if ($expression instanceof Ternary) {

            $complexity += (1 * $depths) * 2;

            if ($expression->if instanceof Ternary) {
                $complexity += $this->expressionExplorer($expression->if, $depths + 1);
            }

            if ($expression->else instanceof Ternary) {
                $complexity += $this->expressionExplorer($expression->else, $depths + 1);
            }
        }

        return $complexity;
    }
}
