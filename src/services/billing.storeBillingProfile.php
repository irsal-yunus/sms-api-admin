<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiReport.php';
SmsApiAdmin::filterAccess();

try {
        $apiReport = new ApiReport();

        /**
         * Check if action mode is create new billing profile or update the existing one
         */
        if($_POST['mode'] == 'new'){
            $billingProfileID = $apiReport
                                        ->insertToBillingProfile(
                                                $_POST['name'],
                                                strtoupper($_POST['price_based']),
                                                $_POST['description']
                                            );

            if(!empty($billingProfileID)){
                if(isset($_POST['user']) && !empty($_POST['user'])){
                    $user = isset($_POST['user']) && !is_null($_POST['user']) ? $_POST['user'] : [];
                    $updateUserClause = [
                        'column'        => 'BILLING_PROFILE_ID',
                        'value'         => $billingProfileID,
                        'whereClause'   => ' USER_ID IN ('.implode(", ",$user).')',
                    ];
                    $apiReport->updateUser($updateUserClause);

                   /**
                    *delete the current BILLING_TIERING_GROUP_ID that belongs to the user
                    *when the user move to another Billing profile (create a new billing)
                    */
                    $updateUserClause = [
                        'column'        => 'BILLING_TIERING_GROUP_ID',
                        'value'         => 'NULL',
                        'whereClause'   => 'BILLING_PROFILE_ID = '.$billingProfileID.'',
                    ];
                    $apiReport->updateUser($updateUserClause);

                }

                if(strtolower($_POST['price_based']) == 'operator'){
                    foreach($_POST['operatorID'] as $key=>$val){
                       $apiReport
                                ->insertToOperator(
                                        $billingProfileID,
                                        $val['operator'],
                                        $val['price']
                                    );
                    }
                }else if(strtolower($_POST['price_based']) == 'tiering'){
                    foreach($_POST['tiering'] as $key=>$val){
                       $apiReport
                               ->insertToTiering(
                                       $billingProfileID,
                                       $val['from'],
                                       $val['to'],
                                       $val['price']
                                    );
                    }
                }
            }
        }else if($_POST['mode'] == 'edit'){
            if(!empty($_POST['billingProfileID'])){
                $billingProfileID = $_POST['billingProfileID'];

                $apiReport->updateBillingProfile(
                                                $billingProfileID,
                                                $_POST['name'],
                                                strtoupper($_POST['price_based']),
                                                $_POST['description']
                                            );
                if(isset($_POST['user']) && !empty($_POST['user'])){
                    $user = isset($_POST['user']) && !is_null($_POST['user']) ? $_POST['user'] : [];

                    /*
                    *Check if the additional users have different billing profile
                    *then delete its BILLING_TIERING_GROUP_ID
                    */
                    $users= $apiReport->getUserByCertainUser($user);
                    foreach($users as $userbyBilling){
                        if ($userbyBilling['BILLING_PROFILE_ID']!==$billingProfileID) {
                           $thisUserID = $userbyBilling['USER_ID'];
                           $updateUserClause = [
                                'column'        => 'BILLING_TIERING_GROUP_ID',
                                'value'         => 'NULL',
                                'whereClause'   => 'USER_ID = '.$thisUserID.'',
                            ];
                            $apiReport->updateUser($updateUserClause);
                        }
                    }

                    /*
                    get tiering ID that have the same current billing profile ID
                    */
                    $userbyBilling = $apiReport->getUserByBilling($billingProfileID);

                    foreach ($userbyBilling as $bill ) {
                        if ($bill['BILLING_TIERING_GROUP_ID']) {
                           $tieringID = $bill['BILLING_TIERING_GROUP_ID'];
                        }
                    }

                    /*
                    First set all user from the current BILLING_PROFILE_ID to null
                     */
                    $updateUserClause = [
                        'column'        => 'BILLING_PROFILE_ID',
                        'value'         => 'NULL',
                        'whereClause'   => ' BILLING_PROFILE_ID = '.$billingProfileID.'',
                    ];
                    $apiReport->updateUser($updateUserClause);


                    /*
                    then fill the available users with current BILLING_PROFILE_ID
                     */
                    $updateUserClause = [
                        'column'        => 'BILLING_PROFILE_ID',
                        'value'         => $billingProfileID,
                        'whereClause'   => ' USER_ID IN ('.implode(", ",$user).')',
                    ];
                    $apiReport->updateUser($updateUserClause);

                    /*
                    if this billing profile have a BILLING_TIERING_GROUP_ID
                    update the users that does not have BILLING_PROFILE_ID
                    set the  BILLING_TIERING_GROUP_ID value to NULL ,
                     */
                    if ($tieringID) {
                          $updateUserClause =[
                            'column'        => 'BILLING_TIERING_GROUP_ID',
                            'value'         => 'NULL',
                            'whereClause'   => 'BILLING_PROFILE_ID IS NULL'
                                               .' AND BILLING_TIERING_GROUP_ID =' .$tieringID.'',
                          ];
                          $apiReport->updateUser($updateUserClause);
                    }
                }

                if(strtolower($_POST['price_based']) == 'operator'){
                    $apiReport->deleteBillingProfileOperator($billingProfileID);

                    foreach($_POST['operatorID'] as $key=>$val){
                       $apiReport
                                ->insertToOperator(
                                        $billingProfileID,
                                        $val['operator'],
                                        $val['price']
                                    );
                    }
                }else if(strtolower ($_POST['price_based']) == 'tiering'){
                    $apiReport->deleteBillingProfileTiering($billingProfileID);

                    foreach($_POST['tiering'] as $key=>$val){
                       $apiReport
                               ->insertToTiering(
                                       $billingProfileID,
                                       $val['from'],
                                       $val['to'],
                                       $val['price']
                                    );
                    }
                }
            }
        }
        header('Location: ./billing.view.php');

} catch (Exception $e) {

}