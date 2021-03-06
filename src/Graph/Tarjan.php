<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * Design pattern: Decorator
 * Component : Concrete Decorator
 *
 * Tarjan is a decorator of Graph for finding strongly connected components
 * in a directed graph (a.k.a digraph)
 *
 */
class Tarjan extends Algorithm
{

    private $stack;
    private $index;
    private $partition;

    /**
     * Get the strongly connected components of this digraph by
     * the Tarjan algorithm.
     *
     * Starting from :
     * http://en.wikipedia.org/wiki/Tarjan%27s_strongly_connected_components_algorithm
     *
     * Corrected with the help :
     * https://code.google.com/p/jbpt/source/browse/trunk/jbpt-core/src/main/java/org/jbpt/algo/graph/StronglyConnectedComponents.java
     *
     * @return array the partition of this graph : an array of an array of vertices
     */
    public function getStronglyConnected()
    {
        $this->stack = array();
        $this->index = 0;
        $this->partition = array();

        foreach ($this->getVertexSet() as $v) {
            if (!isset($v->index)) {
                $this->recursivStrongConnect($v);
            }
        }

        return $this->partition;
    }

    private function recursivStrongConnect(Vertex $v)
    {
        $v->index = $this->index;
        $v->lowLink = $this->index;
        $this->index++;
        array_push($this->stack, $v);

        // Consider successors of v
        foreach ($this->getSuccessor($v) as $w) {
            if (!isset($w->index)) {
                // Successor w has not yet been visited; recurse on it
                $this->recursivStrongConnect($w);
                $v->lowLink = min(array($v->lowLink, $w->lowLink));
            } elseif (in_array($w, $this->stack)) {
                // Successor w is in stack S and hence in the current SCC
                $v->lowLink = min(array($v->lowLink, $w->index));
            }
        }
        // If v is a root node, pop the stack and generate an SCC
        if ($v->lowLink === $v->index) {
            $scc = array();
            do {
                $w = array_pop($this->stack);
                array_push($scc, $w);
            } while ($w !== $v);

            if (count($scc)) {
                $this->partition[] = $scc;
            }
        }
    }

}
