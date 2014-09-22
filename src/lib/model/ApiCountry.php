<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */


/**
 * Description of SmsApiAccount
 *
 * @author setia.budi
 */
class ApiCountry extends ApiBaseModel{
	public function __construct() {
		parent::__construct();
	}
	/**
	 *
	 * @return array
	 */
	public function getAll() {
		try {
			static $list = null;
			if($list !== null)
				return $list;
			$query = 'select COUNTRY_CODE, COUNTRY_NAME from COUNTRY order by COUNTRY_NAME';
			$result = $this->db->query($query);
			$result->setFetchMode(PDO::FETCH_NUM);
			$list = array();
			while($row = $result->fetch())
				$list[$row[0]] = $row[1];
			unset($result);
			return $list;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error!");
		}
	}
}
