<?php

/**
 * Class: DB
 * Some functions and attributes related to manipulation of database.
 * 
 * @author Yihe WANG <tinayihe39@gmail.com>
 */
class DB {

	// DB connection config      //Configuration indépendante -> include
	private $dbname = "weatherDashboard";
	private $dbhost = "localhost";
	private $dbuser = "postgres";
	private $dbpass = "wangyi";

	// DB operation prefix
	private $tableName = "cities";
	private $fields = "city_name,city_id";

	// DB connection session
	private $dbcon;

	/**
	 * Constructor
	 * Connet to PostgreSQL database
	 */
	function __construct() {
		$this->dbcon = new PDO( // PHP Data Objects:définit une interface pour accéder à une base de données depuis PHP. 
			"pgsql:dbname=$this->dbname;host=$this->dbhost", //DSN:constitué par nom du pilote(DBMS)
			$this->dbuser,
			$this->dbpass
		);
	}

	/**
	 * Destructor
	 * Free the connection
	 */
	function __destroy() {
		$this->dbcon = null;
	}

	/**
	 * Function: cleanCityName
	 * Clean the city name before insertion in database.
	 * @param string $cityName the city name to clean
	 */
	function cleanCityName(&$cityName) {
		// In standard pgsql, value inserted should be arrounded by 
		// single quotes.
		// So if a city name contains single quotes, error occurs.
		// Escape the single quote in city name by double single quote.
		$cityName = str_replace('\'', '\'\'', $cityName); //éviter apostrophe
	}

	/**
	 * Function: insert
	 * Insert a city that the user choose to follow its weather.
	 * @param string $cityName the city name to insert
	 * @param string $cityId the city id in OWM to insert
	 * @return int | boolean ID of the last inserted city or false otherwise
	 */
	function insert($cityName, $cityId) {
		$this->cleanCityName($cityName);
		$sql = 'INSERT INTO ' . $this->tableName . '(' . $this->fields . ')' .
			' VALUES (\'' . $cityName . '\', \'' . $cityId . '\');';
		var_dump($sql);die;
		if ($this->dbcon->exec($sql)) {
			return $this->dbcon->lastInsertId();
		} else {
			return false;
		}
	}

	/**
	 * Function: delete
	 * Delete a city by id
	 * @param string $id the id in internal database to delete
	 * @return int Number of tuples which have been removed
	 */
	function delete($id) {
		$sql = 'DELETE FROM ' . $this->tableName . ' WHERE id=' . $id . ';';
		return $this->dbcon->exec($sql);
	}

	/**
	 * Function: get
	 * Retrieve all cities that the user follows
	 * @return array List of cities followed by user and 
	 * theirs ids in database internal and in OWM
	 */
	function get() {
		$results = $this->dbcon->query(
			'SELECT * FROM ' . $this->tableName . ';',
			PDO::FETCH_ASSOC //rentre un tableau associatif
		);
		return $results->fetchAll();
	}

}

// END /lib/DB.class.php
