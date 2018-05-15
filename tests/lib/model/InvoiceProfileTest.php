<?php

use Firstwap\SmsApiAdmin\Test\TestCase;
use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

class InvoiceProfileTest extends TestCase
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
        $this->model = new InvoiceProfile();
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
            'profileId' => 1,
            'bankId' => 1,
            'clientId' => 1,
        ];

        $this->model->insert($data);
    }

    /**
     * Initial bank data
     *
     * @return  void
     */
    protected function initialBank()
    {
        $data = [
            'bankName' => "BCA",
            'address' => "address",
            'accountName' => "accountName",
            'accountNumber' => "90909090909",
        ];

        $model = new InvoiceBank();

        return $model->insert($data);
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
                'ownerType' => 'PROFILE',
                'ownerId' => 1,
            ],
            [
                'productName' => "MBS",
                'period' => date('Y-m-d'),
                'unitPrice' => "123",
                'qty' => "111",
                'useReport' => 0,
                'reportName' => null,
                'ownerType' => 'PROFILE',
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
        $this->assertInstanceOf(InvoiceProfile::class, $result[0]);
        $this->assertArrayHasKey('clientId', $result[0]);
        $this->assertArrayHasKey('bankId', $result[0]);
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
        $this->assertInstanceOf(InvoiceProfile::class, $result[0]);

        $result = $this->model->find($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProfile::class, $result);
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
        $this->assertEquals('PROFILE', $result[0]->products[0]->ownerType);
        $this->assertEquals($result[0]->key(), $result[0]->products[0]->ownerId);

        $result = $this->model->withProduct($result[0]->key());
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result[0]->products);
        $this->assertEquals('PROFILE', $result[0]->products[0]->ownerType);
        $this->assertEquals($result[0]->key(), $result[0]->products[0]->ownerId);

        try {
            $result = $this->model->withProduct(0);
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('Profile Not Found', $e->getMessage());
        }
    }

    /**
     * Test updateProfile method
     *
     * @return  void
     */
    public function testUpdateProfileMethod()
    {
        $this->initialData();
        $bankId = $this->initialBank();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceProfile::class, $result[0]);

        $original = $this->model->find($result[0]->key());
        $this->assertNotEmpty($original);
        $this->assertInstanceOf(InvoiceProfile::class, $original);

        $updateData = [
            'clientId' => 22,
            'bankId' => $bankId,
        ];

        $this->model->updateProfile($original->key(), $updateData);
        $result = $this->model->find($original->key());
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceProfile::class, $result);
        $this->assertArrayHasKey('bankId', $result);
        $this->assertNotEquals($updateData['clientId'], $result['clientId']);
        $this->assertEquals($updateData['bankId'], $result['bankId']);
        $this->assertEquals(json_encode($result->attributes()), (string) $result);

        try {
            $this->model->updateProfile(0, $updateData);
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains('Profile Not Found', $e->getMessage());
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
        $this->assertInstanceOf(InvoiceProfile::class, $result[0]);

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
            $model = new InvoiceProfile();
            $model->delete();
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('no primary key', strtolower($e->getMessage()));
        }
    }

    /**
     * Test isClientDuplicate Method
     *
     * @return  void
     */
    public function testIsClientDuplicateMethod()
    {
        $this->initialData();
        $profiles = $this->model->all();
        $this->assertNotEmpty($profiles);
        $this->assertTrue(is_array($profiles));
        $this->assertInstanceOf(InvoiceProfile::class, $profiles[0]);
        // Check existing acount nummber
        $result = $this->model->isClientDuplicate($profiles[0]->clientId);
        $this->assertTrue($result);
        $result = $this->model->isClientDuplicate($profiles[0]->clientId, $profiles[0]->key());
        $this->assertFalse($result);

        /**
         * Test if delete profile and check the client id should not duplicate
         */
        $this->assertTrue($profiles[0]->delete());
        $result = $this->model->isClientDuplicate($profiles[0]->clientId);
        $this->assertFalse($result);
    }
}
