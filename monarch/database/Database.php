<?php

// title:  Database.php
// author: Ryan Lin
// date:   Oct. 3, 2008
// about:  Connects to some communities' database and gives common queries to 
//         update that database.
// ============================================================================

class Database 
{
	private $connect; // connection to a community's database

	// ========================================================================
	// Database
	//    args:  string - The name or ID of the website's to connect to. 
	//                  - "master" connects to communityanalysis
	//                  - "root" does not select any database. This is used 
	//                    for connection to the root, so you can create 
	//                    databases.
	//    ret:   void
	//    about: Connects to some database.
	//    FIX:   a website cannot be named "master" or "root"
	// ------------------------------------------------------------------------
	public function Database($name)
	{
		$server   = 'localhost';
		$user     = 'ryan';
		$password = 'adobe';
		
		$this->connect = @mysql_connect($server, $user, $password) 
			or die('Could not connect to the master database.');
		
		// connection
		if($name == 'root')
			return;
	
		// a website ID number was specified
		if(is_numeric($name))
		{	
			$select = @mysql_select_db('communityanalysis', $this->connect) 
				or die('Could not select the master database.');
			
			$q = $this->fetch('SELECT name FROM websites WHERE id = ' . $name);
			
			$name = $q['name'];
		}
	
		if($name == 'master')
			$name = 'communityanalysis';
			
		$this->connect = @mysql_connect($server, $user, $password) 
			or die('Could not connect to the database.');
			
		$select  = @mysql_select_db($name, $this->connect) 
			or die('Could not select the database: ' . $name);
	}

	// ========================================================================
	// query
	//    args:  string - MySQL syntax. Can contain multiple queries separated
	//                    by semicolons.
	//    ret:   - MySQL result if the SQL is only one command
	//           - Obviously can't return the result if you have multiple 
	//             commands. In this case, returns void. 
	//    about: Ask database to do something
	// ------------------------------------------------------------------------
	public function query($sql)
	{
		// multi commands detected
		if(strpos($sql, ';'))
		{
			$commands = explode(';', $sql);
	
			foreach($commands as $command)
				if(trim($command) != '') 
					@mysql_query($command, $this->connect) or die('QUERY ERROR: ' . $command);
		}
		// single command
		else
		{
			$query = @mysql_query($sql, $this->connect) or die('QUERY ERROR: ' . $sql);
			return $query;
		}
	}
	
	// ========================================================================
	// fetch
	//    args:  string - MySQL syntax
	//    ret:   * associative array
	//           * false if there is nothing to return
	//    about: Ask database to do something and packages the first row
	//           as an array. Used when you know that your query should only
	//           return one row.
	// ------------------------------------------------------------------------
	public function fetch($sql)
	{
		$mySql = $this->query($sql);
		
		return mysql_fetch_array($mySql);
	}

}

?>
