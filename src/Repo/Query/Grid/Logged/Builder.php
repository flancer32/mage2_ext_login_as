<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Query\Grid\Logged;

use \Flancer32\LoginAs\Config as Cfg;
use \Flancer32\LoginAs\Repo\Data\Entity\Log As Log;

class Builder
    implements \Flancer32\Lib\Repo\Repo\Query\IBuilder
{
    /**
     * Tables aliases.
     */
    const AS_TBL_ADMIN = 'adm'; // default connection
    const AS_TBL_CUSTOMER = 'cust';
    const AS_TBL_LOG = 'log';
    /**
     * Attributes aliases.
     */
    const A_ADMIN_EMAIL = 'AdminEmail';
    const A_ADMIN_NAME_FIRST = 'AdminNameFirst';
    const A_ADMIN_NAME_LAST = 'AdminNameLast';
    const A_CUST_EMAIL = 'CustEmail';
    const A_CUST_NAME_FIRST = 'CustNameFirst';
    const A_CUST_NAME_LAST = 'CustNameLast';
    const A_DATE_LOGGED = 'DateLogged';
    const A_ID = 'Id';
    const A_ID_ADMIN = 'IdAdmin';
    const A_ID_CUST = 'IdCust';

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
    }

    public function getCountQuery(\Flancer32\Lib\Repo\Repo\Query\IBuilder $qbuild = null)
    {
        $asLog = self::AS_TBL_LOG;
        $result = $this->conn->select();
        /* SELECT FROM fl32_loginas_log */
        $tbl = $this->resource->getTableName(Log::ENTITY_NAME);
        $as = $asLog;
        $exp = new \Magento\Rule\Model\Condition\Sql\Expression("COUNT(" . Log::A_ID . ")");
        $cols = [$exp];
        $result->from([$as => $tbl], $cols);
        return $result;
    }

    public function getSelectQuery(\Flancer32\Lib\Repo\Repo\Query\IBuilder $qbuild = null)
    {
        $asAdm = self::AS_TBL_ADMIN;
        $asCust = self::AS_TBL_CUSTOMER;
        $asLog = self::AS_TBL_LOG;
        $result = $this->conn->select();
        /* SELECT FROM fl32_loginas_log */
        $tbl = $this->resource->getTableName(Log::ENTITY_NAME);
        $as = $asLog;
        $cols = [
            self::A_ID => Log::A_ID,
            self::A_ID_CUST => Log::A_CUST_REF,
            self::A_ID_ADMIN => Log::A_USER_REF,
            self::A_DATE_LOGGED => Log::A_DATE
        ];
        $result->from([$as => $tbl], $cols);
        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_CUSTOMER);
        $as = $asCust;
        $on = $asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asLog . '.' . Log::A_CUST_REF;
        $cols = [
            self::A_CUST_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_CUST_NAME_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_CUST_NAME_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $result->joinLeft([$as => $tbl], $on, $cols);
        /* LEFT JOIN admin_user */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_ADMIN_USER);
        $as = $asAdm;
        $on = $asAdm . '.' . Cfg::E_ADMIN_USER_A_USER_ID . '=' . $asLog . '.' . Log::A_USER_REF;
        $cols = [
            self::A_ADMIN_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_ADMIN_NAME_FIRST => Cfg::E_ADMIN_USER_A_FIRSTNAME,
            self::A_ADMIN_NAME_LAST => Cfg::E_ADMIN_USER_A_LASTNAME
        ];
        $result->joinLeft([$as => $tbl], $on, $cols);
        return $result;
    }

}