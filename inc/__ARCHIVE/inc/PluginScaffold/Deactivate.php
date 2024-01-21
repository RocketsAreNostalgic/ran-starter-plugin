<?php

/**
 * @package  RanPlugin
 */

namespace Inc\PluginScaffold;


class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
