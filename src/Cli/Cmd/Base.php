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

    protected function configure()
    {
        parent::configure();
        /* Magento related config (Object Manager) */
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->manObj->get(\Magento\Framework\App\State::class);
        try {
            /* area code should be set only once */
            $appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_ADMIN;
            $appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
    }
}