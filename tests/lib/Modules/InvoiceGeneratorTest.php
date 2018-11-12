<?php

use Firstwap\SmsApiAdmin\Test\TestCase;
use Firstwap\SmsApiAdmin\lib\Modules\InvoiceGenerator;
use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

class InvoiceGeneratorTest extends TestCase
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
     * Initial invoice profile data
     *
     * @return  void
     */
    protected function initialData()
    {
        $data = [
            'profileId' => 1,
            'bankId' => 1,
            'clientId' => 1,
            'autoGenerate' => 1,
        ];

        $this->model->select("DELETE FROM {$this->model->tableName()}")->execute();

        return $this->model->insert($data);
    }

    /**
     * Initial data
     *
     * @return  void
     */
    protected function initialProduct($countData = 1)
    {
        $product = new InvoiceProduct();
        $product->select("DELETE FROM {$product->tableName()}")->execute();

        $data = [
            [
                'productName' => "MBS",
                'period' => date('Y-m-d'),
                'unitPrice' => "123",
                'qty' => "111",
                'useReport' => 1,
                'reportName' => 'rachmat',
                'ownerType' => 'PROFILE',
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

        for ($i=0; $i < $countData ; $i++) {
            foreach ($data as $value) {
                $product->insert($value);
            }
        }
    }

    /**
     * Test Generate Invoice
     *
     * @runInSeparateProcess
     * @return  void
     */
    public function testGenerateMethod()
    {
        $this->initialData();

        $this->initialProduct(10);

        $generator = new InvoiceGenerator;
        $history = new InvoiceHistory;
        ob_start();
        $generator->generate();
        ob_end_clean();
        $model = $history->whereProfile($this->model->key());

        $this->assertNotEmpty($model);
        $this->assertTrue($model[0]->fileExists());

        ob_start();
        $generator->generate();
        ob_end_clean();

        $model2 = $history->whereProfile($this->model->key());

        $this->assertEquals(count($model), count($model2));
        $model[0]->deleteWithProduct();
        $this->assertFalse($model[0]->fileExists());
    }


    /**
     * Test Generate Invoice and Trow an Exception
     *
     * @runInSeparateProcess
     * @return  void
     */
    public function testGenerateMethodAndThrowException()
    {
        $this->initialData();
        $history    = new InvoiceHistory;
        $generator  = $this->getMockBuilder(InvoiceGenerator::class)
                    ->setMethods(['createPdfFile'])
                    ->getMock();
        $generator
            ->method('createPdfFile')
            ->will($this->throwException(new \Exception));

        ob_start();
        $generator->generate();
        ob_end_clean();

        $model = $history->whereProfile($this->model->key());
        $this->assertEmpty($model);
    }

}
