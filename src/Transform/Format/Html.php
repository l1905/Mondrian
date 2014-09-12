<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Format;

use Trismegiste\Mondrian\Transform\Vertex\StaticAnalysis;

/**
 * Html is an exporter to Html + Json + d3.js format 
 * 
 * Do not require Graphviz
 */
class Html extends Json
{

    public function export()
    {
        ob_start();
        require(__DIR__ . '/template-html.php');

        return ob_get_clean();
    }

}