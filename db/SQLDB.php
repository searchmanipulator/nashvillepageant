<?php

	class SQLDB
	{
		var $mHost;			// Database host
		var $mDatabase;		
		var $mUsername;		// Database username
		var $mPassword;		// Database password
		var $mConnection;	// Connection to the database
		var $mQuery;		// SQL Query
		var $mResult;		// Result of the query
		var $mNumRows;		// The number of rows in the table

		// Constructor
		function SQLDB( $host, $database, $username, $password )
		{
			// Set member variables for the database constants
			$this->mHost = $host;
			$this->mDatabase = $database;
			$this->mUsername = $username;
			$this->mPassword = $password;
			
			return $this->connect( );
		}

		function getRow( )
		{
			if( $this->mQuery )
			{
				$row = mysql_fetch_row( $this->mQuery );

				return $row;
			}
		}

		function getNumRows( )
		{
			if( !$this->mQuery )
				return 0;

			$this->mNumRows = mysql_num_rows( $this->mQuery );

			return $this->mNumRows;
		}

		function getHost( )
		{
			return $this->mHost;
		}

		function setHost( $host )
		{
			$this->mHost = $host;
		}

		function connect( )
		{
			if( ( $this->mConnection = mysql_connect( $this->mHost, $this->mUsername, $this->mPassword ) ) )
			{
				return mysql_select_db( $this->mDatabase );
			}
			
			return false;
		}

		function disconnect( )
		{
			if( $this->mConnection )
			{
				return mysql_close( $this->mConnection );
			}
			
			return false;
		}

		function query( $sql )
		{
			if( !$this->mConnection )
				return false;

			$this->mQuery = mysql_query( $sql );

			return $this->mQuery;
		}
		
		function getArray( $type = MYSQL_BOTH )
		{
			return mysql_fetch_array( $this->mQuery, $type );
		}

		function getInsertID( )
		{
			return mysql_insert_id( );
		}

		function getResult( $index, $field )
		{
			return mysql_result( $this->mQuery, $index, $field );
		}
	};

?>