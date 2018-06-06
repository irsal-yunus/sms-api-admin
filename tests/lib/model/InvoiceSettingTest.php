<?php

use Firstwap\SmsApiAdmin\Test\TestCase;
use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

class InvoiceSettingTest extends TestCase
{
    /**
     * Test getSetting method
     *
     * @return  void
     */
    public function testGetSettingMethod()
    {
        $setting = new InvoiceSetting();
        $setting->beginTransaction();
        $setting->select('Delete from ' . $setting->tableName())->execute();
        $result = $setting->getSetting();
        $setting->rollBack();

        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceSetting::class, $result);
    }

    /**
     * Test update method
     *
     * @return  void
     */
    public function testUpdateSettingMethod()
    {
        $updateData = [
            'paymentPeriod' => 11,
            'authorizedName' => 'authorizedName',
            'authorizedPosition' => 'authorizedPosition',
            'approvedName' => 'approvedName',
            'approvedPosition' => 'approvedPosition',
            'noteMessage' => 'noteMessage',
            'invoiceNumberPrefix' => 'test prefix',
        ];

        $setting = new InvoiceSetting();

        $setting->beginTransaction();
        $result = $setting->updateSetting($updateData);

        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceSetting::class, $result);
        $setting->rollBack();
    }

    /**
     * Test update method failed
     *
     * @return  void
     */
    public function testUpdateSettingMethodFailed()
    {
        $updateData = [
            'paymentPeriod' => 11,
            'authorizedName' => 'authorizedName',
            'authorizedPosition' => 'authorizedPosition',
            'approvedName' => 'approvedName',
            'approvedPosition' => 'approvedPosition',
            'noteMessage' => 'noteMessage',
            'invoiceNumberPrefix' => 'test prefix',
        ];

        $setting = $this->getMockBuilder(InvoiceSetting::class)
            ->setMethods(['insert', 'getSetting','update'])
            ->getMock();
        $setting->expects($this->exactly(2))
            ->method('getSetting')
            ->willReturnOnConsecutiveCalls(null, $setting);

        $setting->beginTransaction();

        try {
            $result = $setting->updateSetting($updateData);
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains('Setting Not Found', $e->getMessage());
        }

        try {
            $result = $setting->updateSetting($updateData);
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains('Failed Update Setting', $e->getMessage());
        }

        try {
            $this->callMethod($setting, 'initialSetting');
            $this->fail("Exception not raised");
        } catch (\Exception $e) {
            $this->assertContains("Failed Initialize Setting", $e->getMessage());
        }

        $setting->rollBack();
    }

    /**
     * Test refreshInvoiceNumber method
     *
     * @return  void
     */
    public function testRefreshInvoiceNumberMethod()
    {
        $setting = new InvoiceSetting();
        $setting->clearCache();
        $setting->beginTransaction();
        $setting->select('DELETE FROM ' . $setting->tableName())->execute();
        $setting->select('DELETE FROM '.INVOICE_DB.'.INVOICE_HISTORY')->execute();

        /**
         * Last invoice number should be 0
         */
        $setting->refreshInvoiceNumber();
        $result = $setting->getSetting();
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceSetting::class, $result);
        $this->assertEquals(0, $result->lastInvoiceNumber);

        /**
         * Last invoice number should be 1
         */
        $this->initialInvoiceData();
        $setting->refreshInvoiceNumber();
        $setting->clearCache();
        $result = $setting->selectSetting();

        $this->assertNotEmpty($result);
        $this->assertInstanceOf(InvoiceSetting::class, $result);
        $this->assertEquals(1, $result->lastInvoiceNumber);

        $setting->rollBack();
    }


    /**
     * Initial data invoice
     *
     * @return  void
     */
    protected function initialInvoiceData()
    {
        $model = new InvoiceHistory();
        $model->select("DELETE FROM {$model->tableName()}")->execute();
        $data = [
            'invoiceId' => 1,
            'profileId' => 1,
            'invoiceNumber' => 1,
            'startDate' => date('Y-m-d'),
            'dueDate' => date('Y-m-d', strtotime('13 days')),
        ];

        $model->insert($data);
    }
}
