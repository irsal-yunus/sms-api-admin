<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\Test\TestCase;

class InvoiceProductTest extends TestCase
{
    /**
     * Model instance
     *
     * @var
     */
    protected $model;

    /**
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = new InvoiceProduct();
        $this->model->beginTransaction();
    }

    /**
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->model->rollBack();
        unset($this->model);
    }

    /**
     * Initial data
     *
     * @return  void
     */
    protected function initialData()
    {
        $this->model->select('DELETE FROM ' . $this->model->tableName())->execute();

        $data = [
            [
                'productName' => "SMS API",
                'period' => null,
                'unitPrice' => "200",
                'qty' => "200",
                'useReport' => 0,
                'reportName' => null,
                'ownerType' => 'PROFILE',
                'ownerId' => 1,
            ],
            [
                'productName' => "SMS HISTORY",
                'period' => date('Y-m-d'),
                'unitPrice' => "200",
                'qty' => "200",
                'useReport' => 1,
                'reportName' => '1rstwap',
                'ownerType' => 'HISTORY',
                'ownerId' => 1,
            ],
        ];

        foreach ($data as $value) {
            $this->model->insert($value);
        }
    }

    /**
     * Test all method
     *
     * @return  void
     */
    public function testAllMethod()
    {
        $this->initialData();

        $result = $this->model->all();

        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);
        $this->assertGreaterThanOrEqual(2, count($result));
    }

    /**
     * Test find method
     *
     * @return  void
     */
    public function testFindMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result);
    }

    /**
     * Test profile method
     *
     * @return  void
     */
    public function testProfileMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);

        $result = $this->model->profile();
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);
        $this->assertEquals($this->model::PROFILE_PRODUCT, $result[0]['ownerType']);

        $result = $this->model->profile(1);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);
        $this->assertEquals($this->model::PROFILE_PRODUCT, $result[0]['ownerType']);
    }

    /**
     * Test history method
     *
     * @return  void
     */
    public function testHistoryMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);

        $result = $this->model->history();
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);
        $this->assertEquals($this->model::HISTORY_PRODUCT, $result[0]['ownerType']);

        $result = $this->model->history(1);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);
        $this->assertEquals($this->model::HISTORY_PRODUCT, $result[0]['ownerType']);
    }

    /**
     * Test updateProduct method
     *
     * @return  void
     */
    public function testUpdateProductMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result);

        $updateData = [
            'productName' => "SMS SAPI",
            'period' => '2018-01-01',
            'unitPrice' => "2000",
            'qty' => "2000",
            'useReport' => 0,
            'reportName' => 'anu',
            'ownerType' => 'PROFILE',
            'ownerId' => 1,
        ];

        $this->model->updateProduct($result->key(), $updateData);
        $result = $this->model->find($result->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProduct::class, $result);
        $this->assertArrayHasKey('productName', $result);
        $this->assertArrayHasKey('period', $result);
        $this->assertArrayHasKey('reportName', $result);
        $this->assertArrayHasKey('unitPrice', $result);
        $this->assertArrayHasKey('qty', $result);
        $this->assertArrayHasKey('useReport', $result);
        $this->assertArrayHasKey('reportName', $result);
        $this->assertArrayHasKey('ownerType', $result);
        $this->assertArrayHasKey('ownerId', $result);
        $this->assertEquals($updateData['productName'], $result['productName']);
        $this->assertEquals($updateData['reportName'], $result['reportName']);
        $this->assertEquals($updateData['period'], $result['period']);
        $this->assertEquals($updateData['unitPrice'], $result['unitPrice']);
        $this->assertEquals($updateData['qty'], $result['qty']);
        $this->assertEquals($updateData['useReport'], $result['useReport']);
        $this->assertEquals($updateData['reportName'], $result['reportName']);
        $this->assertEquals($updateData['ownerType'], $result['ownerType']);
        $this->assertEquals($updateData['ownerId'], $result['ownerId']);
        $this->assertEquals(json_encode($result->attributes()), (string) $result);

        try {
            $this->model->updateProduct(0, $updateData);
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('Product Not Found', $e->getMessage());
        }
    }

    /**
     * Test Delete Method
     *
     * @return void
     */
    public function testDeleteMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProduct::class, $result[0]);

        $this->assertTrue($result[0]->delete());

        $notfound = $this->model->find($result[0]->key());
        $this->assertFalse($notfound);

        try {
            $result[0]->delete();
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('data not found', strtolower($e->getMessage()));
        }

        try {
            $model = new InvoiceProduct();
            $model->delete();
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('no primary key', strtolower($e->getMessage()));
        }
    }
}
