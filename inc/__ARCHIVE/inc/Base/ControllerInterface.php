<?php

namespace Ran\MyPlugin\Base;

/**
 * Service Controllers must implement a register() method in order to be
 * activated by the RegisterService class.
 */
interface ControllerInterface
{

    public function register(): void;
}
