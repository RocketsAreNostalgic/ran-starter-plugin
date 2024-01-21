<?php

/**
 * @package  RanPlugin
 *
 * Base class for activation/deactivation methods
 *
 */

namespace Inc\Base;

/**
 * Activation class that establishes the
 *
 * @package  RanPlugin
 */
class RegisterOptions
{
    private array $options = [];

    /**
     * Undocumented function
     *
     * @param  array $options
     *
     * @return void
     */
    public function registerOptions(): void
    {
        if (!current_user_can('activate_plugins')) return;

        flush_rewrite_rules();

        foreach ($this->options as $option => $value) {
            if (!get_option($option)) {
                update_option($option, $value);
            }
        }
    }

    /**
     * Set an option to the options array.
     *
     * @param  string $option_name
     * @param  mixed  $value
     * @param  bool   $autoload
     *
     * @return void
     */
    public function setOption(string $option_name, mixed $value, string|bool $autoload = false)
    {

        $this->options[$option_name] = $value;
    }
}
