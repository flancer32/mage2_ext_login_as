<?php
/**
 * Script to register M2-module
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

use Magento\Framework\Component\ComponentRegistrar as Registrar;

Registrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    \Flancer32\LoginAs\Config::MODULE,
    __DIR__
);