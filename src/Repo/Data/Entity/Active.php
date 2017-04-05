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
    const A_ID = 'id';
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
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::A_ID);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
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
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setUserRef($data)
    {
        parent::set(self::A_USER_REF, $data);
    }

}