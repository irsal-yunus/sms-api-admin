<?php

namespace Firstwap\SmsApiAdmin\Test\lib\model;

use Firstwap\SmsApiAdmin\lib\model\InternationalPrice;
use Firstwap\SmsApiAdmin\Test\TestCase;

class InternationalPriceTest extends TestCase
{
    /**
     * Test Get all International Price value
     *
     * @return void
     */
    public function testAllMethod()
    {
        $intlPrice = new InternationalPrice();

        $intlPrice->beginTransaction();
        $intlPrice->select("DELETE FROM " . $intlPrice->tableName())->execute();

        $results = $intlPrice->all();

        $this->assertNotEmpty($results);

        $intlPrice->rollback();
    }
}
