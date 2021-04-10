<?php

//use Illuminate\Database\Capsule\Manager as DB;
// https://developers.whmcs.com/advanced/db-interaction/
use WHMCS\Database\Capsule;

// Perform potentially risky queries in a transaction for easy rollback.
$pdo = Capsule::connection()->getPdo();


class PDOWrapper
{
	public static function query($query, $params = array())
	{
		$statement = Capsule::connection()->getPdo()
			->prepare($query);
		$statement->execute($params);
		return $statement;
	}

	public static function real_escape_string($string)
	{
		return substr(Capsule::connection()->getPdo()->quote($string), 1, -1);
	}

	public static function fetch_assoc($query)
	{
		return $query->fetch(\PDO::FETCH_ASSOC);
	}

	public static function fetch_array($query)
	{
		return $query->fetch(\PDO::FETCH_BOTH);
	}

	public static function fetch_object($query)
	{
		return $query->fetch(\PDO::FETCH_OBJ);
	}

	public static function num_rows($query)
	{
		$query->fetch(\PDO::FETCH_BOTH);
		return $query->rowCount();
	}

	public static function insert_id()
	{
		return Capsule::connection()->getPdo()
			->lastInsertId();
	}
}
