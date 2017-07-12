<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd;

/**
 * Base for module's CLI commands.
 */
abstract class Base
    extends \Symfony\Component\Console\Command\Command
{

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj
    ) {
        /* object manager is used in __construct/configure */
        $this->manObj = $manObj;
        parent::__construct();
    }
}