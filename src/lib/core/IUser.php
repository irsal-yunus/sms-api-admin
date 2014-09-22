<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

interface IUser{
	/**
	 * @return mixed
	 */
	public function getID();
	/**
	 * @return string
	 */
	public function getName();
	/**
	 * @return bool
	 */
	public function hasPrivilege($privilege);
}
