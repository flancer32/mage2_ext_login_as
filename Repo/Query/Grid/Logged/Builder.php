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

    public function build(\Flancer32\Lib\Repo\Fw\Db\Select $source = null)
    {
        // TODO: Implement build() method.
    }

    /**
     * Attributes aliases.
     */
    const A_ADMIN = 'Admin';
    const A_CUSTOMER = 'Customer';
    const A_DATE_LOGGED = 'DateLogged';
    const A_ID = 'Id';
    const A_ID_ADMIN = 'AdminId';
    const A_ID_CUST = 'CustId';
    const A_TOTAL = 'Total';

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
        $result = $this->getSelectQuery($qbuild);
        $columns = $result->getPart('columns');
        $exp = 'COUNT(' . self::AS_TBL_LOG . '.' . \Flancer32\LoginAs\Repo\Data\Entity\Log::A_ID . ')';
        $expTotal = new \Flancer32\Lib\Repo\Repo\Query\Expression($exp);
        $colTotal = [
            self::AS_TBL_LOG,
            $expTotal,
            self::A_TOTAL
        ];
        /* Total column shold be the first */
        array_unshift($columns, $colTotal);
        $result->setPart('columns', $columns);
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
        $first = $as . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME;
        $last = $as . '.' . Cfg::E_CUSTOMER_A_LASTNAME;
        $email = $as . '.' . Cfg::E_CUSTOMER_A_EMAIL;
        $expValue = "CONCAT($first, ' ', $last, ' <', $email, '>')";
        $exp = new \Flancer32\Lib\Repo\Repo\Query\Expression($expValue);
        $cols = [
            self::A_ID_CUST => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_CUSTOMER => $exp
        ];
        $result->joinLeft([$as => $tbl], $on, $cols);
        /* LEFT JOIN admin_user */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_ADMIN_USER);
        $as = $asAdm;
        $on = $asAdm . '.' . Cfg::E_ADMIN_USER_A_USER_ID . '=' . $asLog . '.' . Log::A_USER_REF;
        $first = $as . '.' . Cfg::E_ADMIN_USER_A_FIRSTNAME;
        $last = $as . '.' . Cfg::E_ADMIN_USER_A_LASTNAME;
        $email = $as . '.' . Cfg::E_ADMIN_USER_A_EMAIL;
        $expValue = "CONCAT($first, ' ', $last, ' <', $email, '>')";
        $exp = new \Flancer32\Lib\Repo\Repo\Query\Expression($expValue);
        $cols = [
            self::A_ID_ADMIN => Cfg::E_ADMIN_USER_A_USER_ID,
            self::A_ADMIN => $exp
        ];
        $result->joinLeft([$as => $tbl], $on, $cols);
        return $result;
    }

}