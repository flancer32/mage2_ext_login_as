<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Data\Entity;

class Log
    extends \Flancer32\Lib\Repo\Repo\Data\Def\Entity
    implements \Flancer32\Lib\Repo\Repo\Data\IEntity
{
    /**
     * These attributes should be equal to attributes in DEM (./etc/dem.json).
     */
    const A_CUST_REF = 'customer_ref';
    const A_DATE = 'date';
    const A_USER_REF = 'user_ref';
    /**
     * Entity name is a composition of the "package.package.entity" aliases from DEM.
     */
    const ENTITY_NAME = 'fl32_loginas_log';

    /**
     * @return int
     */
    public function getCustomerRef()
    {
        $result = parent::get(self::A_CUST_REF);
        return $result;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        $result = parent::get(self::A_DATE);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::A_CUST_REF, self::A_USER_REF];
    }

    /**
     * @return int
     */
    public function getUserRef()
    {
        $result = parent::get(self::A_USER_REF);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setCustomerRef($data)
    {
        parent::set(self::A_CUST_REF, $data);
    }

    /**
     * @param string $data
     */
    public function setDate($data)
    {
        parent::set(self::A_DATE, $data);
    }

    /**
     * @param int $data
     */
    public function setUserRef($data)
    {
        parent::set(self::A_USER_REF, $data);
    }

}