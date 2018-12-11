<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;
use Firstwap\SmsApiAdmin\Test\TestCase;

class InvoiceBankTest extends TestCase
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
        $this->model = new InvoiceBank();
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
            'bankName' => "BCA",
            'address' => "address",
            'accountName' => "accountName",
            'accountNumber' => "90909090909",
        ];

        $this->model->insert($data);
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
        $this->assertInstanceOf(InvoiceBank::class, $result[0]);
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
        $this->assertInstanceOf(InvoiceBank::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceBank::class, $result);
    }

    /**
     * Test updateBank method
     *
     * @return  void
     */
    public function testUpdateBankMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceBank::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceBank::class, $result);

        $updateData = [
            'bankName' => "ABC",
            'address' => "22222",
            'accountName' => "111111",
            'accountNumber' => "12333333",
        ];

        $this->model->updateBank($result->key(), $updateData);
        $result = $this->model->find($result->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceBank::class, $result);
        $this->assertArrayHasKey('bankName', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('accountName', $result);
        $this->assertArrayHasKey('accountNumber', $result);
        $this->assertEquals($updateData['bankName'], $result['bankName']);
        $this->assertEquals($updateData['address'], $result['address']);
        $this->assertEquals($updateData['accountName'], $result['accountName']);
        $this->assertEquals($updateData['accountNumber'], $result['accountNumber']);
        $this->assertEquals(json_encode($result->attributes()), (string) $result);

        try {
            $this->model->updateBank(0, $updateData);
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('Bank Not Found', $e->getMessage());
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
        $this->assertInstanceOf(InvoiceBank::class, $result[0]);

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
            $model = new InvoiceBank();
            $model->delete();
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('no primary key', strtolower($e->getMessage()));
        }
    }


    /**
     * Test isAccountNumberDuplicate Method
     *
     * @return  void
     */
    public function testIsAccountNumberDuplicateMethod()
    {
        $this->initialData();
        $banks = $this->model->all();
        $this->assertNotEmpty($banks);
        $this->assertTrue(is_array($banks));
        $this->assertInstanceOf(InvoiceBank::class, $banks[0]);
        // Check existing acount nummber
        $result = $this->model->isAccountNumberDuplicate($banks[0]->accountNumber);
        $this->assertTrue($result);
        $result = $this->model->isAccountNumberDuplicate($banks[0]->accountNumber, $banks[0]->key());
        $this->assertFalse($result);

        /**
         * Test if delete bank account and check the account number should not duplicate
         */
        $this->assertTrue($banks[0]->delete());
        $result = $this->model->isAccountNumberDuplicate($banks[0]->accountNumber);
        $this->assertFalse($result);
    }
}
