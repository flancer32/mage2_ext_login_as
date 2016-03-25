<?php
/**
 * Script to register M2-module
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
use Flancer32\LoginAs\Config as Cfg;
use Magento\Framework\Component\ComponentRegistrar as Registrar;

Registrar::register(Registrar::MODULE, Cfg::MODULE, __DIR__);