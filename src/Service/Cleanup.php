<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Service;

use \Flancer32\LoginAs\Repo\Data\Entity\Log as Log;

class Cleanup
    implements ICleanup
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Flancer32\LoginAs\Helper\Config */
    protected $hlpConfig;
    /** @var \Flancer32\LoginAs\Repo\Entity\ILog */
    protected $repoLog;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Flancer32\LoginAs\Helper\Config $hlpConfig,
        \Flancer32\LoginAs\Repo\Entity\ILog $repoLog
    ) {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->hlpConfig = $hlpConfig;
        $this->repoLog = $repoLog;
    }

    public function execute(\Flancer32\LoginAs\Service\Cleanup\Request $request)
    {
        $result = new \Flancer32\LoginAs\Service\Cleanup\Response();
        $days = (int)$request->daysToLeave;
        if ($days <= 0) {
            $days = $this->hlpConfig->getLogsCleanupDaysOld();
        } elseif ($days < \Flancer32\LoginAs\Helper\Config::DEF_LOGS_CLEANUP_MIN_DAYS) {
            $days = \Flancer32\LoginAs\Helper\Config::DEF_LOGS_CLEANUP_MIN_DAYS;
        }
        $time = strtotime("-$days day");
        $date = date('Y-m-d H:i:s', $time);
        /* remove old logs */
        $quoted = $this->conn->quote($date);
        $where = Log::A_DATE . '<=' . $quoted;
        $deleted = $this->repoLog->delete($where);
        $result->deleted = $deleted;
        return $result;
    }
}