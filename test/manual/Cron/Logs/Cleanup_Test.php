<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cron\Logs;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Cleanup_FuncTest
    extends \PHPUnit_Framework_TestCase
{
    public function test_execute()
    {
        /** @var  $obm \Magento\Framework\App\ObjectManager */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /* start test transaction */
        /** @var  $obj \Flancer32\LoginAs\Cron\Logs\Cleanup */
        $obj = $obm->create(\Flancer32\LoginAs\Cron\Logs\Cleanup::class);
        $obj->execute();
    }
}