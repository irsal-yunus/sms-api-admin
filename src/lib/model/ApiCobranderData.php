<?php

/*
 * Copyright(c) 2014 1rstWAP. All rights reserved.
 */

/**
 * Description of ApiCobranderData
 *
 * @author ferri
 */
class ApiCobranderData extends ApiBaseModel {
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     * @return array
     */
    public function getCobranderData() {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_COBRANDER);
            $query = "SELECT 
                        COB_ID as cobranderId, 
                        COB_COUNTRY_CODE as cobranderCountry,
                        COB_COMPANY_NAME as companyName 
                       FROM 
                        COBRANDER";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datas ? $datas : array();
        } catch (PDOException $exc) {
            $this->logger->error("$exc");
            throw new Exception("Query Error");
        }
    }

}