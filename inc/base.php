<?php

/**
 * Base
 * The base class User and Order extend
 *
 * @author Steven Snyder <steveasnyder@gmail.com>
 */
class Base {

	/** @var Database $db  The Database instance */
	protected $db;
	
	/** @var boolean $isAdmin  Logged in admin flag */
	protected $isAdmin;
	
	/** @var array $data  The data used in templates */
	protected $data;

	/**
	 * Get an instance of Database
	 * Validate user token and set admin flag
	 */
	public function __construct() {
		// Get Database instance
		$this->db = Database::getInstance();
		
		// Validate user token and set admin flag
		if (!empty($_COOKIE[USER_COOKIE]) and $user = $this->db->callSP('getUserByToken', array('token' => $_COOKIE[USER_COOKIE]))) {
			$this->isAdmin = (bool)$user['admin'];
		}
	}
	
}