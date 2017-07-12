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
    /**
     * Check area code in commands that require code to be set.
     */
    protected function checkAreaCode()
    {
        /* Magento related config (Object Manager) */
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\State $appState */
        $appState = $manObj->get(\Magento\Framework\App\State::class);
        try {
            /* area code should be set only once */
            $appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_GLOBAL;
            $appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $manObj->configure($config);
        }
    }
}