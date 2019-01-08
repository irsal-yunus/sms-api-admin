<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

SmsApiAdmin::filterAccess();

$logger  = Logger::getRootLogger();
$service = new AppJsonService();
$model   = new InvoiceProfile();

function convertCurrencyString($string)
{
    return floatval(str_replace(',', '', $string));
}

try {
    $errorFields = [];
    $definitions = [
        "profileId"             => FILTER_SANITIZE_NUMBER_INT,
        "bankId"                => FILTER_SANITIZE_NUMBER_INT,
        "autoGenerate"          => FILTER_SANITIZE_NUMBER_INT,
        "approvedName"          => FILTER_SANITIZE_STRING,
        "approvedPosition"      => FILTER_SANITIZE_STRING,
        "profileName"           => FILTER_SANITIZE_STRING,
        "useMinCommitment"      => FILTER_SANITIZE_NUMBER_INT,
        "minCommitmentType"     => FILTER_SANITIZE_STRING,
        "minCommitmentAmount"   => [
            'filter'  => FILTER_CALLBACK,
            'options' => 'convertCurrencyString',
        ],
        "minCharge"             => [
            'filter'  => FILTER_CALLBACK,
            'options' => 'convertCurrencyString',
        ],
        "combinedMinCommitment" => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updates = filter_input_array(INPUT_POST, $definitions);

    if ($updates['useMinCommitment'] == 1)
    {
        if (empty($updates['minCommitmentAmount']))
        {
            $errorFields['minCommitmentAmount'] = 'Minimum Commitment Amount should not be empty!';
        }

        if ($updates['minCommitmentType'] == InvoiceHistory::MINIMUM_QTY && empty($updates['minCharge']))
        {
            $errorFields['minCharge'] = 'Minimum Charge should not be empty!';
        }
        elseif ($updates['minCommitmentType'] == InvoiceHistory::MINIMUM_PRICE)
        {
            $updates['minCharge'] = null;
        }
    }

    if (empty($updates['profileId']))
    {
        SmsApiAdmin::returnError("Invalid Invoice Profile ID ({$updates['profileId']}) !");
    }

    if (empty($updates['profileName']))
    {
        $errorFields['profileName'] = 'Profile Name should not be empty!';
    }
    elseif ($model->isProfileNameDuplicate($updates['profileName'], $updates['profileId']))
    {
        $errorFields['profileName'] = 'Profile Name already exists!';
    }

    if (empty($updates['bankId']))
    {
        $errorFields['bankId'] = 'Payment Detail should not be empty!';
    }

    if ($errorFields)
    {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    }
    else
    {
        $model->updateProfile($updates['profileId'], $updates);
        $service->setStatus(true);
        $service->summarise('Invoice Profile successfully updated');
        $service->deliver();
    }
}
catch (Exception $e)
{
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
