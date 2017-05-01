<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Repo\Data\Entity;

class Active
    extends \Flancer32\Lib\Repo\Repo\Data\Def\Entity
    implements \Flancer32\Lib\Repo\Repo\Data\IEntity
{
    /**
     * These attributes should be equal to attributes in DEM (./etc/dem.json).
     */
    const A_CUST_REF = 'customer_ref';
    const A_KEY = 'key';
    const A_USER_REF = 'user_ref';
    /**
     * Entity name is a composition of the "package.package.entity" aliases from DEM.
     */
    const ENTITY_NAME = 'fl32_loginas_act';

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
    public function getKey()
    {
        $result = parent::get(self::A_KEY);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::A_KEY];
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
    public function setKey($data)
    {
        parent::set(self::A_KEY, $data);
    }

    /**
     * @param int $data
     */
    public function setUserRef($data)
    {
        parent::set(self::A_USER_REF, $data);
    }

}