<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;
use Firstwap\SmsApiAdmin\Test\TestCase;

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

}
