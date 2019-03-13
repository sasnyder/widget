<?php

/**
 * Database
 * Singleton class for calling MySQL stored procedures
 *
 * @author Steven Snyder <steveasnyder@gmail.com>
 */
class Database {

	/** @var Database $instance  The static Database instance */
	private static $instance;
	
	/** @var PDO $connection  The PDO object connecting the database server */
	private $connection;

	/**
	 * Connects to the MySQL server
	 */
	private function __construct() {
		try {
			// Make database connection
			$this->connection = new PDO('mysql:host=localhost;dbname=widget', 'root', '');
		} catch (Exception $e) {
			die('Unable to connect to database');
		}
	}
	
	/**
	 * Gets an instance of Database
	 *
	 * @return Database $instance
	 */
	public static function getInstance() {
		if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
	}
	
	/**
	 * Call a stored procedure with arguments
	 *
	 * @param string $name  The name of the stored procedure to call
	 * @param array $arg  The arguments of the stored procedure
	 * @return array $result  The queried dataset
	 */
	public function callSP($name, $arg = array()) {
		// Begin CALL
		$sql = 'CALL '.$name.'(';
	
		// Build SP parameters
		foreach ($arg as $key => $value) {
			$sql .= ':'.$key.', ';
		}
		
		// Remove trailing comma and close CALL
		$sql = trim($sql, ', ').')';
	
		// Prepare SP SQL
		$sp = $this->connection->prepare($sql);
		
		// Bind SP variables
		foreach ($arg as $key => $value) {
			$sp->bindValue(':'.$key, $value);
		}
		
		$sp->execute();
		
		if ($sp->errorCode() === '00000') {
			// Fetch all rows into associative array
			$result = $sp->fetchAll(PDO::FETCH_ASSOC);
		} else {
			// Display error info
			die(print_r($sp->errorInfo(), 1));
		}
		
		$sp->closeCursor();
		
		// Remove one level of nesting if possible
		if (count($result) == 1) {
			return array_shift($result);
		}
		return $result;
	}
	
}