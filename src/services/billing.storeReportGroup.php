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
        
        if($_POST['mode'] == 'new'){
            
            $reportGroupID = $apiReport->insertToReportGroup(
                                            $_POST['name'], 
                                            $_POST['description']
                                );
            if(!is_null($reportGroupID)){
                $updateUserClause = [
                        'column'        => 'BILLING_REPORT_GROUP_ID',
                        'value'         => $reportGroupID,
                        'whereClause'   => ' USER_ID IN ('.implode(", ",$_POST['user']).')',
                    ];

                $apiReport->updateUser($updateUserClause);
            }
        }else if($_POST['mode'] == 'edit'){
             if(!empty($_POST['reportGroupID'])){
                $reportGroupID = $_POST['reportGroupID'];
                
                $apiReport->updateReportGroup(
                                                $reportGroupID, 
                                                $_POST['name'], 
                                                $_POST['description']
                                            );
                 
                $updateUserClause = [
                    'column'        => 'BILLING_REPORT_GROUP_ID ',
                    'value'         => 'NULL',
                    'whereClause'   => ' BILLING_REPORT_GROUP_ID  = '.$reportGroupID.'',
                ];
                
                $apiReport->updateUser($updateUserClause);
               
                $updateUserClause = [
                    'column'        => 'BILLING_REPORT_GROUP_ID',
                    'value'         => $reportGroupID,
                    'whereClause'   => ' USER_ID IN ('.implode(", ",$_POST['user']).')',
                ];
                $apiReport->updateUser($updateUserClause);
             }
        }
         header("location: ./billing.view.php");
} catch (Exception $e) {
    
}