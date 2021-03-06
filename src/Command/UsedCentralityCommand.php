<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Analysis\UsedCentrality;

/**
 * UsedCentralityCommand transforms a bunch of php files into a digraph
 * and exports it into a report file with centrality informations of
 * the using of each node.
 *
 * Higher rank means the vertex has many directed edges toward it
 * (he is the target). It means each time there is a change in the vertex
 * there are many effects accross the source code (a.k.a the ripple effect)
 */
class UsedCentralityCommand extends AbstractCentrality
{

    protected function getAlgorithm()
    {
        return 'ripple';
    }

    protected function createCentrality(Graph $g)
    {
        return new UsedCentrality($g);
    }

}
