<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\Test\TestCase;

class InvoiceHistoryTest extends TestCase
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
        $this->model = new InvoiceHistory();
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
        $this->model->select("DELETE FROM {$this->model->tableName()}")->execute();
        $data = [
            'invoiceId' => 1,
            'profileId' => 1,
            'invoiceNumber' => 1,
            'startDate' => date('Y-m-d'),
            'dueDate' => date('Y-m-d', strtotime('13 days')),
        ];

        $this->model->insert($data);
    }

    /**
     * Initial data
     *
     * @return  void
     */
    protected function initialProduct()
    {
        $product = new InvoiceProduct();
        $product->select("DELETE FROM {$product->tableName()}")->execute();

        $data = [
            [
                'productName' => "SMS API",
                'period' => null,
                'unitPrice' => "200",
                'qty' => "200",
                'useReport' => 0,
                'reportName' => null,
                'ownerType' => 'HISTORY',
                'ownerId' => 1,
            ],
            [
                'productName' => "MBS",
                'period' => date('Y-m-d'),
                'unitPrice' => "123",
                'qty' => "111",
                'useReport' => 0,
                'reportName' => null,
                'ownerType' => 'HISTORY',
                'ownerId' => 2,
            ],
        ];

        foreach ($data as $value) {
            $product->insert($value);
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
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);
        $this->assertArrayHasKey('invoiceId', $result[0]);
        $this->assertArrayHasKey('invoiceNumber', $result[0]);
        $this->assertArrayHasKey('profileId', $result[0]);
        $this->assertArrayHasKey('startDate', $result[0]);
        $this->assertArrayHasKey('dueDate', $result[0]);
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
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceHistory::class, $result);
    }

    /**
     * Test withProduct method
     *
     * @return  void
     */
    public function testWithProductMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $result = $this->model->withProduct();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result[0]->products);
        $this->assertEquals('HISTORY', $result[0]->products[0]->ownerType);
        $this->assertEquals($result[0]->key(), $result[0]->products[0]->ownerId);

        $result = $this->model->withProduct($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result[0]->products);
        $this->assertEquals('HISTORY', $result[0]->products[0]->ownerType);
        $this->assertEquals($result[0]->key(), $result[0]->products[0]->ownerId);

        try {
            $result = $this->model->withProduct(0);
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('History Not Found', $e->getMessage());
        }
    }

    /**
     * Test updateHistory method
     *
     * @return  void
     */
    public function testUpdateHistoryMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceHistory::class, $result);

        $updateData = [
            'invoiceNumber' => 22,
            'profileId' => 22,
            'startDate' => date('Y-m-d', strtotime('+1 day')),
            'dueDate' => date('Y-m-d', strtotime('+22 days')),
        ];

        $this->model->updateHistory($result->key(), $updateData);
        $result = $this->model->find($result->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceHistory::class, $result);
        $this->assertArrayHasKey('invoiceId', $result);
        $this->assertArrayHasKey('invoiceNumber', $result);
        $this->assertArrayHasKey('profileId', $result);
        $this->assertArrayHasKey('startDate', $result);
        $this->assertArrayHasKey('dueDate', $result);
        $this->assertEquals($updateData['invoiceNumber'], $result['invoiceNumber']);
        $this->assertEquals($updateData['startDate'], $result['startDate']);
        $this->assertEquals($updateData['dueDate'], $result['dueDate']);
        $this->assertEquals(json_encode($result->attributes()), (string) $result);

        try {
            $this->model->updateHistory(0, $updateData);
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains('History Not Found', $e->getMessage());
        }

        $result = $this->model->getProduct('');
        $this->assertEmpty($result);
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
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $this->assertTrue($result[0]->delete());

        $notfound = $this->model->find($result[0]->key());
        $this->assertFalse($notfound);

        try {
            $result[0]->delete();
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains('data not found', strtolower($e->getMessage()));
        }

        try {
            $model = new InvoiceHistory();
            $model->delete();
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('no primary key', strtolower($e->getMessage()));
        }
    }
}
