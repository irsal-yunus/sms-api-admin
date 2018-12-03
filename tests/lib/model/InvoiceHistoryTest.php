<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
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
            'status' => 0,
            'startDate' => date('Y-m-d'),
            'dueDate' => date('Y-m-d', strtotime('13 days')),
        ];

        $this->model->insert($data);
    }

    /**
     * Initial invoice profile data
     *
     * @return  void
     */
    protected function initialProfile()
    {
        $data = [
            'profileId' => 1,
            'bankId' => 1,
            'clientId' => 1,
            'useMinCommitment' => 1,
            'minCommitmentType' => 'PRICE',
            'minCommitmentAmount' => 250000.00,
            'minCharge'=> null,
            'combinedMinCommitment' => 0,
        ];

        $model = new InvoiceProfile();
        $model->select("DELETE FROM {$model->tableName()}")->execute();

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
                'useReport' => 1,
                'reportName' => null,
                'ownerType' => 'HISTORY',
                'ownerId' => 1,
            ],
            [
                'productName' => "MBS",
                'period' => date('Y-m-d'),
                'unitPrice' => "123",
                'qty' => "111",
                'useReport' => 1,
                'reportName' => 'rachmat',
                'ownerType' => 'HISTORY',
                'ownerId' => 1,
            ],
            [
                'productName' => "MBS 2",
                'period' => date('Y-m-d'),
                'unitPrice' => "123",
                'qty' => "111",
                'useReport' => 1,
                'reportName' => null,
                'ownerType' => 'PROFILE',
                'ownerId' => 1,
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
     * Test pendingCount method
     *
     * @return  void
     */
    public function testPendingCountMethod()
    {
        $this->initialData();

        $result = $this->model->pendingCount();

        $this->assertNotEmpty($result);
        $this->assertEquals(1, $result);
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
     * Test loadProduct method
     *
     * @return  void
     */
    public function testLoadProductMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $products = $result[0]->loadProduct();
        $this->assertNotEmpty($products);
        $this->assertNotEmpty($result[0]->products);
    }

    /**
     * Test whereStatus method
     *
     * @runInSeparateProcess
     * @return  void
     */
    public function testWhereStatusMethod()
    {
        $this->initialData();
        $unlocked = $this->model->whereStatus('unlocked');
        $this->assertNotEmpty($unlocked);
        $unlocked = $this->model->whereStatus(0);
        $this->assertNotEmpty($unlocked);
        $locked = $this->model->whereStatus('locked');
        $this->assertEmpty($locked);
        $locked = $this->model->whereStatus(1);
        $this->assertEmpty($locked);

        current($unlocked)->lockInvoice();
        $locked = $this->model->whereStatus('locked');
        $this->assertNotEmpty($locked);
        $locked = $this->model->whereStatus(1);
        $this->assertNotEmpty($locked);
    }

    /**
     * Test whereProfile method
     *
     * @return  void
     */
    public function testWhereProfileMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $result = $this->model->whereProfile($result[0]->profileId);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);
    }

    /**
     * Test whereStartDate method
     *
     * @return  void
     */
    public function testWhereStartDateMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $result = $this->model->whereStartDate(strtotime('now'));
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);
        $this->assertTrue($result[0]->save());

        $result = $this->model->whereStartDate(false);
        $this->assertEmpty($result);
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
     * Test createProduct Method
     *
     * @return void
     */
    public function testCreateProductMethod()
    {
        $modelMock = $this
            ->getMockBuilder(InvoiceHistory::class)
            ->setMethods(['commit'])
            ->getMock();

        $data = [
            'profileId' => 1,
            'invoiceNumber' => 1,
            'startDate' => date('Y-m-d'),
            'dueDate' => date('Y-m-d', strtotime('13 days')),
        ];
        $this->initialProfile();
        $this->initialProduct();

        $invoiceId = $modelMock->createHistory($data);
        $this->assertNotEmpty($invoiceId);

        $profileMock = $this
            ->getMockBuilder(InvoiceProfile::class)
            ->setMethods(['withProduct'])
            ->getMock();
        $profileMock
            ->expects($this->once())->method("withProduct")
            ->willReturn([]);
        $modelMock = $this
            ->getMockBuilder(InvoiceHistory::class)
            ->setMethods(['profile', 'commit'])
            ->getMock();
        $modelMock
            ->expects($this->once())->method("profile")
            ->willReturn($profileMock);

        try {
            $data['invoiceNumber'] = 2;
            $modelMock->createHistory($data);
            $this->fail('Exception not raised when create history without profile!');
        } catch (\Exception $e) {
            $this->assertContains("Profile not found", $e->getMessage());
        }

        $result = $this->model->getProduct('');
        $this->assertEmpty($result);
    }

    /**
     * Test deleteWithProduct Method
     *
     * @return void
     */
    public function testDeleteWithProductMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);

        $history = $result[0];

        $this->assertTrue($history->deleteWithProduct());

        $notfound = $this->model->find($history->key());
        $this->assertFalse($notfound);

        try {
            $result[0]->deleteWithProduct();
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains('failed delete invoice', strtolower($e->getMessage()));
        }

        try {
            $model = new InvoiceHistory();
            $model->deleteWithProduct();
            $this->fail("Exception not rais");
        } catch (\Exception $e) {
            $this->assertContains('failed delete invoice', strtolower($e->getMessage()));
        }

        $model  = $this->getMockBuilder(InvoiceHistory::class)
            ->setMethods(['fileExists' ,'filePath'])
            ->getMock();
        $model
            ->method('fileExists')
            ->willReturn(true);

        $shouldFalse = $model->deleteInvoiceFile();
        $this->assertFalse($shouldFalse);
    }

    /**
     * Test lock invoice method
     *
     * @return void
     */
    public function testLockInvoiceMethod()
    {
        $this->initialData();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);
        $history = $result[0];
        $history->lockInvoice();

        $this->assertEquals($history->status, InvoiceHistory::INVOICE_LOCK);
        $this->assertTrue($history->deleteWithProduct());
    }

    /**
     * Test copy invoice method
     *
     * @return void
     */
    public function testCopyInvoiceMethod()
    {
        $modelMock = $this
            ->getMockBuilder(InvoiceHistory::class)
            ->setMethods(['commit'])
            ->getMock();

        $this->initialData();
        $this->initialProduct();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);
        $history = $result[0];
        $history->lockInvoice();

        foreach ($history->attributes() as $key => $value)
        {
            $modelMock->$key = $value;
        }

        $modelMock->copyInvoice();
        $this->assertEquals($modelMock->status, InvoiceHistory::INVOICE_LOCK);
        $this->assertEquals($modelMock->invoiceType, InvoiceHistory::COPIED);
        $this->assertTrue($modelMock->deleteWithProduct());
    }

    /**
     * Test revise invoice method
     *
     * @return void
     */
    public function testReviseInvoiceMethod()
    {
        $modelMock = $this
            ->getMockBuilder(InvoiceHistory::class)
            ->setMethods(['commit'])
            ->getMock();

        $this->initialData();
        $this->initialProduct();
        $result = $this->model->all();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(InvoiceHistory::class, $result[0]);
        $history = $result[0];
        $history->lockInvoice();

        foreach ($history->attributes() as $key => $value)
        {
            $modelMock->$key = $value;
        }

        $modelMock->reviseInvoice();
        $this->assertEquals($modelMock->status, InvoiceHistory::INVOICE_UNLOCK);
        $this->assertEquals($modelMock->invoiceType, InvoiceHistory::REVISED);

        $modelMock->reviseInvoice();
        $this->assertEquals($modelMock->status, InvoiceHistory::INVOICE_UNLOCK);
        $this->assertEquals($modelMock->invoiceType, InvoiceHistory::REVISED);
        $this->assertTrue($modelMock->deleteWithProduct());
    }

    /**
     * Test subTotal method
     *
     * @return  void
     */
    public function testSubTotalMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $this->assertEmpty($this->model->subTotal());

        $histories = $this->model->withProduct();

        $this->assertNotEmpty($histories);
        $this->assertNotEmpty($histories[0]);
        $history = $histories[0];
        $this->assertNotEmpty($history);
        $this->assertNotEmpty($history->subTotal());

        $this->assertNotEmpty($history->subTotal(1));
    }

    /**
     * Test total method
     *
     * @return  void
     */
    public function testTotalMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $this->assertEmpty($this->model->total());

        $histories = $this->model->withProduct();

        $this->assertNotEmpty($histories);
        $this->assertNotEmpty($histories[0]);
        $history = $histories[0];
        $this->assertNotEmpty($history);
        $this->assertNotEmpty($history->total());
    }

    /**
     * Test spellTotal method
     *
     * @return  void
     */
    public function testSpellTotalMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $this->assertEquals('Zero', $this->model->spellTotal());

        $histories = $this->model->withProduct();

        $this->assertNotEmpty($histories);
        $this->assertNotEmpty($histories[0]);
        $history = $histories[0];
        $this->assertNotEmpty($history);
        $this->assertNotEmpty($history->spellTotal());
    }

    /**
     * Test paymentPeriod method
     *
     * @return  void
     */
    public function testPaymentPeriodMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $histories = $this->model->withProduct();
        $this->assertNotEmpty($histories);
        $this->assertNotEmpty($histories[0]);
        $history = $histories[0];
        $this->assertNotEmpty($history);
        $this->assertNotEmpty($history->paymentPeriod());
        $this->assertEquals(13, $history->paymentPeriod());
    }

    /**
     * Test isLock method
     *
     * @return  void
     */
    public function testIsLockMethod()
    {
        $model = new InvoiceHistory(['status' => 0]);
        $this->assertFalse($model->isLock());

        $model = new InvoiceHistory(['status' => 1]);
        $this->assertTrue($model->isLock());

        $model = new InvoiceHistory(['status' => '0']);
        $this->assertFalse($model->isLock());

        $model = new InvoiceHistory(['status' => '1']);
        $this->assertTrue($model->isLock());
    }

    /**
     * Test isInvoiceNumberDuplicate method
     *
     * @return void
     */
    public function testIsInvoiceNumberDuplicateMethod()
    {
        $this->model->select("DELETE FROM {$this->model->tableName()}")->execute();
        $result = $this->model->isInvoiceNumberDuplicate(1);
        $this->assertFalse($result);
        $this->initialData();
        $result = $this->model->isInvoiceNumberDuplicate(1);
        $this->assertTrue($result);
        $result = $this->model->isInvoiceNumberDuplicate(1, 1);
        $this->assertFalse($result);
    }

    /**
     * Test isInvoiceAlreadyExists method
     *
     * @return void
     */
    public function testIsInvoiceAlreadyExistsMethod()
    {
        $this->model->select("DELETE FROM {$this->model->tableName()}")->execute();
        $result = $this->model->isInvoiceAlreadyExists(date('Y-m-d'), '1');
        $this->assertFalse($result);
        $this->initialData();
        $result = $this->model->isInvoiceAlreadyExists(date('Y-m-d'), 1);
        $this->assertTrue($result);
        $result = $this->model->isInvoiceAlreadyExists(date('Y-m-d'), 1, 1);
        $this->assertFalse($result);
    }

    /**
     * Test isExpired method
     *
     * @return void
     */
    public function testIsExpiredMethod()
    {
       $invoice = new InvoiceHistory;

       $invoice->startDate = date('Y-m-d', strtotime('-10 days'));
       $invoice->dueDate = date('Y-m-d', strtotime('-1 days'));

       $this->assertTrue($invoice->isExpired());

       $invoice->startDate = date('Y-m-d', strtotime('-10 days'));
       $invoice->dueDate = date('Y-m-d', strtotime('+4 days'));

       $this->assertFalse($invoice->isExpired());
    }

    /**
     * Test createInvoiceFile method
     *
     * @return void
     */
    public function testCreateInvoiceFileMethod()
    {
        $invoiceDir = SMSAPIADMIN_BASE_DIR . "archive/invoices";
        $invoiceDirTest = $invoiceDir . "-test";

        $this->initialData();
        $this->initialProfile();
        $this->initialProduct();

        if (file_exists($invoiceDir)) {
            exec("mv " . $invoiceDir . " " . $invoiceDirTest);
        }

        $results = $this->model->all();
        $result = $results[0]->createInvoiceFile();
        $this->assertTrue($result);
        $this->assertNotEmpty($results[0]->fileName);
        $this->assertTrue($results[0]->fileExists());
        ob_start();
        $results[0]->previewFile();
        $results[0]->downloadFile();
        $results[0]->deleteWithProduct();
        $results[0]->previewFile();
        $results[0]->downloadFile();
        ob_end_clean();
        $this->assertFalse($results[0]->fileExists());

        exec('rm -rf ' . $invoiceDir);

        if (file_exists($invoiceDirTest)) {
            exec("mv " . $invoiceDirTest . " " . $invoiceDir);
        }
    }

    /**
    * Test totalQty method
    *
    * @return void
    */
    public function testTotalQtyMethod()
    {
        $this->initialData();
        $this->initialProduct();

        $this->assertEmpty($this->model->totalQty());

        $histories = $this->model->withProduct();
        $history   = $histories[0];

        $this->assertNotEmpty($history->totalQty());

    }

    /**
    * Test minimumCommitment method
    *
    * @return void
    */
    public function testMinimumCommitment()
    {
        $this->initialData();
        $this->initialProduct();
        $dummyProfile = [
            'profileId'             => 1,
            'profileName'           => 'user',
            'clientId'              => 1,
            'bankId'                => 1,
            'autoGenerate'          => 0,
            'approvedName'          => null,
            'approvedPosition'      => Null,
            'useMinCommitment'      => 1,
            'minCommitmentType'     => 'PRICE',
            'minCommitmentAmount'   => 250000.00,
            'minCharge'             => null,
            'combinedMinCommitment' => 0,
        ];

        $this->assertEmpty($this->model->minimumCommitment());

        /*
         Price, with not combined
         */
        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $this->assertCount(4,$history->minimumCommitment($dummyProfile));

        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $products  = $history->minimumCommitment($dummyProfile);
        $this->assertEmpty($products[2]->productId);
        $this->assertEmpty($products[3]->productId);
        $this->assertNotEmpty($products[2]);
        $this->assertNotEmpty($products[3]);

        /*
         Price , combined
         */
        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $dummyProfile['combinedMinCommitment'] = 1;
        $this->assertCount(3,$history->minimumCommitment($dummyProfile));

        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $products  = $history->minimumCommitment($dummyProfile);
        $this->assertEmpty($products[2]->productId);
        $this->assertNotEmpty($products[2]);

        /*
         Price , combined , but dont have any product that have subtotal below
         minimum commitment amount
         */
        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $dummyProfile['minCommitmentAmount'] = 1200.00;
        $this->assertCount(2,$history->minimumCommitment($dummyProfile));

        /*
        Quantity, Combined
         */
        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $dummyProfile['minCommitmentType']     = 'QUANTITY';
        $dummyProfile['minCommitmentAmount']   = 500;
        $dummyProfile['minCharge']             = 50000.00;
        $this->assertCount(3,$history->minimumCommitment($dummyProfile));

        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $products  = $history->minimumCommitment($dummyProfile);
        $this->assertEmpty($products[2]->productId);
        $this->assertNotEmpty($products[2]);

        /*
        Quantity , Combined, but dont have any product that have total quantity below
         minimum commitment amount
        */
        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $dummyProfile['minCommitmentAmount']   = 100;
        $dummyProfile['minCharge']             = 50000.00;
        $this->assertCount(2,$history->minimumCommitment($dummyProfile));

        /*
         Quantity , not combined
        */
        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $dummyProfile['combinedMinCommitment']  = 0;
        $dummyProfile['minCommitmentAmount']    = 500;
        $dummyProfile['minCharge']              = 50000.00;
        $this->assertCount(4,$history->minimumCommitment($dummyProfile) );

        $histories = $this->model->withProduct();
        $history   = $histories[0];
        $products  = $history->minimumCommitment($dummyProfile);
        $this->assertEmpty($products[2]->productId);
        $this->assertEmpty($products[3]->productId);
        $this->assertNotEmpty($products[2]);
        $this->assertNotEmpty($products[3]);

    }


}
