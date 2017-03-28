<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Vertex;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ParamVertex;
use Trismegiste\Mondrian\Visitor\State\AbstractObjectLevel;

/**
 * ObjectLevelHelper is an helper for common behavior of interface/class/trait
 */
abstract class ObjectLevelHelper extends AbstractObjectLevel
{

    final public function enter(Node $node)
    {
        if (($node->getType() == 'Stmt_ClassMethod') &&
            $node->isPublic()
        ) {
            $fqcn = $this->getCurrentFqcn();
            $this->enterPublicMethod($fqcn, $node);
        }
    }

    abstract protected function enterPublicMethod($fqcn, ClassMethod $node);

    /**
     * Adding a new vertex if the method is not already indexed
     * Since it is a method, I'm also adding the parameters
     *
     * @param ClassMethod $node
     */
    protected function pushMethod(ClassMethod $node, $index)
    {
        $dict = $this->getGraphContext();
        if (!$dict->existsVertex('method', $index)) {
            $v = new MethodVertex($index);
            $this->getGraph()->addVertex($v);
            $dict->indicesVertex('method', $index, $v);
            // now param
            foreach ($node->params as $order => $aParam) {
                $this->pushParameter($index, $order);
            }
        }
    }

    /**
     * Add a parameter vertex. I must point out that I store the order
     * of the parameter, not its name. Why ? Because, name can change accross
     * inheritance tree. Therefore, it could fail the refactoring of the source
     * from the digraph.
     *
     * @param string $methodName like 'FQCN::method'
     * @param int $order
     */
    protected function pushParameter($methodName, $order)
    {
        $dict = $this->getGraphContext();
        $index = $methodName . '/' . $order;
        if (!$dict->existsVertex('param', $index)) {
            $v = new ParamVertex($index);
            $this->getGraph()->addVertex($v);
            $dict->indicesVertex('param', $index, $v);
        }
    }

    /**
     * Adding a new vertex if the implementation is not already indexed
     *
     * @param ClassMethod $node
     */
    protected function pushImplementation(ClassMethod $node, $index)
    {
        $dict = $this->getGraphContext();
        if (!$dict->existsVertex('impl', $index)) {
            $v = new ImplVertex($index);
            $this->getGraph()->addVertex($v);
            $dict->indicesVertex('impl', $index, $v);
        }
    }
}