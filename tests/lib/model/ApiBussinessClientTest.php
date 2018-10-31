<?php

use Firstwap\SmsApiAdmin\Test\TestCase;

require_once dirname(dirname(dirname(__DIR__))) . '/src/init.d/init.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/lib/model/ApiBusinessClient.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
/**
 * ApiReportTest for class Firstwap/SmsApiAdmin/lib/model/ApiBusinessClient
 *
 * @author Elbananda Permana
 *
 */
class apiBussinessClientTest extends TestCase
{

    public function testGetOnlyUnarchivedClient(){
    	$clientManager = new ApiBusinessClient();
		$clients       = $clientManager->getOnlyUnarchivedClient();
		$this->assertEquals(NULL, current($clients)['archivedDate']);
	}

	public function testSetInactiveUser(){
		//CREATE dumyclient that hve belongs to dummy client
		$clientDummy  = array(
			'clientID'      =>'1111',
			'companyName'   =>'xxxx',
			'companyUrl'    =>'xxxx',
		    'countryCode'   =>'IDN',
			'contactName'   =>'xxxx',
			'contactEmail'  =>'xxxx',
			'contactPhone'  =>'xxxx',
			'customerId'    =>'2345',
			'contactAddress'=>'xxxx'
		);
	    $db    = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
        $query = 'insert into CLIENT (
                        CLIENT_ID,COMPANY_NAME, COMPANY_URL, COUNTRY_CODE,
                        CONTACT_NAME, CONTACT_EMAIL, CONTACT_PHONE,
                        CUSTOMER_ID,CONTACT_ADDRESS
                        )
                    values (
                        :clientID, :companyName, :companyUrl, :countryCode,
                        :contactName, :contactEmail, :contactPhone,
                        :customerId, :contactAddress
                        )';
        $stmt = $db->prepare($query);
        $stmt->bindValue(':clientID',       $clientDummy['clientID'      ], PDO::PARAM_STR);
        $stmt->bindValue(':customerId',     $clientDummy['customerId'    ], PDO::PARAM_STR);
        $stmt->bindValue(':companyName',    $clientDummy['companyName'   ], PDO::PARAM_STR);
        $stmt->bindValue(':companyUrl',     $clientDummy['companyUrl'    ], PDO::PARAM_STR);
        $stmt->bindValue(':countryCode',    $clientDummy['countryCode'   ], PDO::PARAM_STR);
        $stmt->bindValue(':contactName',    $clientDummy['contactName'   ], PDO::PARAM_STR);
        $stmt->bindValue(':contactEmail',   $clientDummy['contactEmail'  ], PDO::PARAM_STR);
        $stmt->bindValue(':contactPhone',   $clientDummy['contactPhone'  ], PDO::PARAM_STR);
        $stmt->bindValue(':contactAddress', $clientDummy['contactAddress'], PDO::PARAM_STR);
        $stmt->execute();

        //CREATE user that hve belongs to dummy client
        $userDummy  = array(
        	'userId'			     =>'1122',
			'userName'               =>'dummy',
			'userPassword'           =>'ASDFGHJK',
			'clientID'               =>'1111',
		    'cobranderID'            =>'1RSTWAP',
			'active'                 =>'1',
			'isPostpaid'             =>'1'
		);
		$db1    = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
		$query2 = 'insert into USER (
                        USER_ID, USER_NAME, PASSWORD, CLIENT_ID,
                        COBRANDER_ID, ACTIVE, IS_POSTPAID
                        )
                    values (
                        :userId, :userName, :userPassword, :clientID,
                        :cobranderID, :active, :isPostpaid
                        )';

        $stmt2 = $db1->prepare($query2);
        $stmt2->bindValue(':userId'      , $userDummy['userId'      ], PDO::PARAM_STR);
        $stmt2->bindValue(':userName'    , $userDummy['userName'    ], PDO::PARAM_STR);
        $stmt2->bindValue(':userPassword', $userDummy['userPassword'], PDO::PARAM_STR);
        $stmt2->bindValue(':clientID'    , $userDummy['clientID'    ], PDO::PARAM_STR);
        $stmt2->bindValue(':cobranderID' , $userDummy['cobranderID' ], PDO::PARAM_STR);
        $stmt2->bindValue(':active'      , $userDummy['active'      ], PDO::PARAM_STR);
        $stmt2->bindValue(':isPostpaid'  , $userDummy['isPostpaid'  ], PDO::PARAM_STR);
        $stmt2->execute();

        $clientManager = new ApiBusinessClient();
		$clientManager->setInactiveUser(1111);

        $userManager   = new ApiUser();
	    $clients       = $userManager->getDetailsByID(1122);

	    $this->assertEquals('Client is archived', $clients['inactiveReason']);

	    $query ='delete from CLIENT where CLIENT_ID = 1111 ';
	    $stmt  = $db->prepare($query);
	    $stmt ->execute();

	    $query ='delete from USER where USER_ID = 1122 ';
	    $stmt  = $db->prepare($query);
	    $stmt ->execute();
	}


	public function testArchived(){
		//CREATE dumyclient
		$clientDummy  = array(
			'clientID'      =>'1111',
			'companyName'   =>'xxxx',
			'companyUrl'    =>'xxxx',
		    'countryCode'   =>'IDN',
			'contactName'   =>'xxxx',
			'contactEmail'  =>'xxxx',
			'contactPhone'  =>'xxxx',
			'customerId'    =>'2345',
			'contactAddress'=>'xxxx'
		);
	    $db    = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
        $query = 'insert into CLIENT (
                        CLIENT_ID,COMPANY_NAME, COMPANY_URL, COUNTRY_CODE,
                        CONTACT_NAME, CONTACT_EMAIL, CONTACT_PHONE,
                        CUSTOMER_ID,CONTACT_ADDRESS
                        )
                    values (
                        :clientID, :companyName, :companyUrl, :countryCode,
                        :contactName, :contactEmail, :contactPhone,
                        :customerId, :contactAddress
                        )';
        $stmt = $db->prepare($query);
        $stmt->bindValue(':clientID',       $clientDummy['clientID'      ], PDO::PARAM_STR);
        $stmt->bindValue(':customerId',     $clientDummy['customerId'    ], PDO::PARAM_STR);
        $stmt->bindValue(':companyName',    $clientDummy['companyName'   ], PDO::PARAM_STR);
        $stmt->bindValue(':companyUrl',     $clientDummy['companyUrl'    ], PDO::PARAM_STR);
        $stmt->bindValue(':countryCode',    $clientDummy['countryCode'   ], PDO::PARAM_STR);
        $stmt->bindValue(':contactName',    $clientDummy['contactName'   ], PDO::PARAM_STR);
        $stmt->bindValue(':contactEmail',   $clientDummy['contactEmail'  ], PDO::PARAM_STR);
        $stmt->bindValue(':contactPhone',   $clientDummy['contactPhone'  ], PDO::PARAM_STR);
        $stmt->bindValue(':contactAddress', $clientDummy['contactAddress'], PDO::PARAM_STR);
        $stmt->execute();

        $clientManager = new ApiBusinessClient();
		$clientManager->archived(1111);
	    $clients       = $clientManager->getDetails(1111);

	    //archived date is not null
	    $this->assertNotEquals(NULL,$clients['archivedDate']);

	    //archived date is null
	    $clientManager->archived(1111);
	    $clients       = $clientManager->getDetails(1111);
	    $this->assertEquals(NULL, $clients['archivedDate']);

	    $query ='delete from CLIENT where CLIENT_ID = 1111 ';
	    $stmt  = $db->prepare($query);
	    $stmt ->execute();
	}
}