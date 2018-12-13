<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Flancer32\LoginAs\Api\Repo\Dao;

use Flancer32\LoginAs\Api\Repo\Dao\Log as AnObject;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class LogTest
    extends \Flancer32\Base\Test\BaseCase
{
    /** @var AnObject */
    private $obj;


    protected function setUp()
    {
        /** Get object to test */
        $this->obj = self::$manObj->get(AnObject::class);
    }

    public function test_all()
    {
        self::$conn->beginTransaction();
        /* Create new entity */
        $class = AnObject::ENTITY_CLASS;
        /** @var \Flancer32\LoginAs\Api\Repo\Data\Log $entity */
        $entity = new $class();
        $entity->setCustomerRef(1);
        $entity->setUserRef(1);
        $id = $this->obj->create($entity);
        $this->assertNotNull($id);

        /* Get created entity */
        $saved = $this->obj->getOne($id);
        $dateSaved = $saved->getDate();
        $this->assertNotNull($dateSaved);

        /* Update entity */
        $saved->setDate(0);
        $count = $this->obj->updateOne($saved);
        $this->assertEquals(1, $count);
        $updated = $this->obj->getOne($id);
        $this->assertNotNull($updated);

        /* Delete entity */
        $deleted = $this->obj->deleteOne($updated);
        $this->assertEquals(1, $deleted);
        /* Complete test */
        self::$conn->rollBack();
    }
}