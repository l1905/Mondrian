<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * TraitVertex is a vertex for a trait
 */
class TraitVertex extends StaticAnalysis
{

    protected function getSpecific()
    {
        $default = array('shape' => 'pentagon', 'style' => 'filled',
            'color' => 'hotpink', 'label' => $this->compactFqcn($this->name));
        // because traits are in he same place as rainbow and unicorn : it's magic so: pentagon & pink

        return $default;
    }

}
