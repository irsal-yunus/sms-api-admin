-- MySQL dump 10.14  Distrib 5.5.54-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 10.32.6.71    Database: 10.32.6.71
-- ------------------------------------------------------
-- Server version	5.5.36-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `BILL_U_MESSAGE.DELIVERY_STATUS`
--
USE BILL_U_MESSAGE;

INSERT INTO `DELIVERY_STATUS` (ERROR_CODE, DESCRIPTION, STATUS, IS_RECREDITED, REFERENCE) VALUES 
            ('1153+0+0+0','EC_NNR_noTranslationForThisSpecificAddress','Undelivered','0',''),
            ('1158+0+0+0','EC_NNR_networkCongestion','Undelivered','0',''),
            ('257+0+0+0','EC_SM_DF_equipmentProtocolError','Undelivered','0',''),
            ('502+0+0+0','EC_NO_RESPONSE','Undelivered','0',''),
            ('4100+0+0+0','EC_ValidityUndelivered','Undelivered','0',''),
            ('4104+0+0+0','EC_DESTINATION_TXT_FLOODING','Undelivered','0',''),
            ('903+0+0+0','time out error as temporary that time period','Undelivered','0',''),
            ('Pending','SMS Sent to the vendor','Undelivered','0',''),
            ('1','EC_UNKNOWN_SUBSCRIBER','Undelivered','0',''),
            ('5','EC_UNIDENTIFIED_SUBSCRIBER','Undelivered','0',''),
            ('6','EC_ABSENT_SUBSCRIBER_SM','Undelivered','0',''),
            ('9','EC_ILLEGAL_SUBSCRIBER','Undelivered','0',''),
            ('11','EC_TELESERVICE_NOT_PROVISIONED','Undelivered','0',''),
            ('12','EC_ILLEGAL_EQUIPMENT','Undelivered','0',''),
            ('13','EC_CALL_BARRED','Undelivered','0',''),
            ('21','EC_FACILITY_NOT_SUPPORTED','Undelivered','0',''),
            ('27','EC_ABSENT_SUBSCRIBER','Undelivered','0',''),
            ('31','EC_SUBSCRIBER_BUSY_FOR_MT_SMS','Undelivered','0',''),
            ('32','EC_SM_DELIVERY_FAILURE','Undelivered','0',''),
            ('33','EC_MESSAGE_WAITING_LIST_FULL','Undelivered','0',''),
            ('34','EC_SYSTEM_FAILURE','Undelivered','0',''),
            ('35','EC_DATA_MISSING','Undelivered','0',''),
            ('36','EC_UNEXPECTED_DATA_VALUE','Undelivered','0',''),
            ('256','EC_SM_DF_memoryCapacityExceeded','Undelivered','0',''),
            ('257','EC_SM_DF_equipmentProtocolError','Undelivered','0',''),
            ('258','EC_SM_DF_equipmentNotSM_Equipped','Undelivered','0',''),
            ('259','EC_SM_DF_unknownServiceCentre','Undelivered','0',''),
            ('260','EC_SM_DF_sc_Congestion','Undelivered','0',''),
            ('261','EC_SM_DF_invalidSME_Address','Undelivered','0',''),
            ('262','EC_SM_DF_subscriberNotSC_Subscriber','Undelivered','0',''),
            ('500','EC_PROVIDER_GENERAL_ERROR','Undelivered','0',''),
            ('502','EC_NO_RESPONSE','Undelivered','0',''),
            ('503','EC_SERVICE_COMPLETION_FAILURE','Undelivered','0',''),
            ('504','EC_UNEXPECTED_RESPONSE_FROM_PEER','Undelivered','0',''),
            ('507','EC_MISTYPED_PARAMETER','Undelivered','0',''),
            ('508','EC_NOT_SUPPORTED_SERVICE','Undelivered','0',''),
            ('509','EC_DUPLICATED_INVOKE_ID','Undelivered','0',''),
            ('511','EC_INITIATING_RELEASE','Undelivered','0',''),
            ('1024','EC_OR_appContextNotSupported','Undelivered','0',''),
            ('1025','EC_OR_invalidDestinationReference','Undelivered','0',''),
            ('1026','EC_OR_invalidOriginatingReference','Undelivered','0',''),
            ('1027','EC_OR_encapsulatedAC_NotSupported','Undelivered','0',''),
            ('1028','EC_OR_transportProtectionNotAdequate','Undelivered','0',''),
            ('1030','EC_OR_potentialVersionIncompatibility','Undelivered','0',''),
            ('1031','EC_OR_remoteNodeNotReachable','Undelivered','0',''),
            ('1152','EC_NNR_noTranslationForAnAddressOfSuchNatur','Undelivered','0',''),
            ('1153','EC_NNR_noTranslationForThisSpecificAddress','Undelivered','0',''),
            ('1154','EC_NNR_subsystemCongestion','Undelivered','0',''),
            ('1155','EC_NNR_subsystemFailure','Undelivered','0',''),
            ('1156','EC_NNR_unequippedUser','Undelivered','0',''),
            ('1157','EC_NNR_MTPfailure','Undelivered','0',''),
            ('1158','EC_NNR_networkCongestion','Undelivered','0',''),
            ('1159','EC_NNR_unqualified','Undelivered','0',''),
            ('1160','EC_NNR_errorInMessageTransportXUDT','Undelivered','0',''),
            ('1161','EC_NNR_errorInLocalProcessingXUDT','Undelivered','0',''),
            ('1162','EC_NNR_destinationCannotPerformReassemblyXUDT','Undelivered','0',''),
            ('1163','EC_NNR_SCCPfailure','Undelivered','0',''),
            ('1164','EC_NNR_hopCounterViolation','Undelivered','0',''),
            ('1165','EC_NNR_segmentationNotSupported','Undelivered','0',''),
            ('1166','EC_NNR_segmentationFailure','Undelivered','0',''),
            ('1281','EC_UA_userSpecificReason','Undelivered','0',''),
            ('1282','EC_UA_userResourceLimitation','Undelivered','0',''),
            ('1283','EC_UA_resourceUnavailable','Undelivered','0',''),
            ('1284','EC_UA_applicationProcedureCancellation','Undelivered','0',''),
            ('1536','EC_PA_providerMalfunction','Undelivered','0',''),
            ('1537','EC_PA_supportingDialogOrTransactionRealeased','Undelivered','0',''),
            ('1538','EC_PA_ressourceLimitation','Undelivered','0',''),
            ('1539','EC_PA_maintenanceActivity','Undelivered','0',''),
            ('1540','EC_PA_versionIncompatibility','Undelivered','0',''),
            ('1541','EC_PA_abnormalMapDialog','Undelivered','0',''),
            ('1793','EC_NC_responseRejectedByPeer','Undelivered','0',''),
            ('1794','EC_NC_abnormalEventReceivedFromPeer','Undelivered','0',''),
            ('1795','EC_NC_messageCannotBeDeliveredToPeer','Undelivered','0',''),
            ('1796','EC_NC_providerOutOfInvoke','Undelivered','0',''),
            ('2048','EC_TIME_OUT','Undelivered','0',''),
            ('2049','EC_IMSI_BLACKLISTED','Undelivered','0',''),
            ('2050','EC_DEST_ADDRESS_BLACKLISTED','Undelivered','0',''),
            ('2051','EC_InvalidMscAddress','Undelivered','0',''),
            ('4096','EC_InvalidPduFormat','Undelivered','0',''),
            ('4100','EC_Cancelled','Undelivered','0',''),
            ('4101','EC_ValidityExpired','Undelivered','0',''),
            ('4104','EC_DESTINATION_TXT_FLOODING','Undelivered','0','')
ON DUPLICATE KEY UPDATE
            ERROR_CODE = VALUES(ERROR_CODE),
            DESCRIPTION = VALUES(DESCRIPTION),
            STATUS = VALUES(STATUS),
            IS_RECREDITED = VALUES(IS_RECREDITED),
            REFERENCE = VALUES(REFERENCE);

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-06 13:50:43
