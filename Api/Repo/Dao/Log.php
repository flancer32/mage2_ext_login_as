<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Flancer32\LoginAs\Api\Repo\Dao;

use Flancer32\LoginAs\Api\Repo\Data\Log as Entity;

interface Log
    extends \Flancer32\Base\Api\Repo\Dao\Entity
{
    /** Entity class name to construct data objects in result set. */
    const ENTITY_CLASS = Entity::class;
    /** Database table name. */
    const ENTITY_NAME = 'fl32_loginas_log';
    /** Primary key attributes. */
    const ENTITY_PK = [Entity::ID];

    /**
     * @param Entity $data
     * @return int|null
     */
    public function create($data);

    /**
     * @param Entity|array|int|string $pk
     * @return int
     */
    public function deleteOne($pk);

    /**
     * @param mixed $pk
     * @return Entity|null
     */
    public function getOne($pk);

    /**
     * @param string|array $where
     * @param array $bind
     * @param string|array $order
     * @param string $limit
     * @param string $offset
     * @return Entity[]
     */
    public function getSet(
        $where = null,
        $bind = null,
        $order = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param Entity $data
     * @return int
     */
    public function updateOne($data);

    /**
     * @param Entity|array $data
     * @param mixed $where
     * @return int
     */
    public function updateSet($data, $where);
}