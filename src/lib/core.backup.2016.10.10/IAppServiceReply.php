<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 *
 * @author setia.budi
 */
interface IAppService {
	public function setReplyData($label, $value);
	public function getReplyData($label);
	public function getAllArguments();
	public function getArgument($argName);
	public function addParameter();
	public function getAllParameters();
	public function process();
}
