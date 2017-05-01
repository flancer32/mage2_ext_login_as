<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Service;


/**
 * Clean Up "LoginAs" logs.
 */
interface ICleanup
{
    /**
     * @param \Flancer32\LoginAs\Service\Cleanup\Request $request
     * @return \Flancer32\LoginAs\Service\Cleanup\Response
     */
    public function execute(\Flancer32\LoginAs\Service\Cleanup\Request $request);
}