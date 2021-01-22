<?php

namespace WebApp\DataModel;

use WebApp\WebAppException;

class UserDAO extends \TgDatabase\DAO {

	public function __construct($database, $modelClass = NULL, $checkTable = FALSE) {
		parent::__construct($database, '#__users', $modelClass != NULL ? $modelClass : 'WebApp\\DataModel\\User', 'uid', $checkTable);
	}

	public function createTable() {
		// Try to create
		$sql =
			'CREATE TABLE '.$this->database->quoteName($this->tableName).' ('.
				'`uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'`created_on` DATETIME NOT NULL,'.
				'`email` varchar(250) COLLATE utf8mb4_bin NOT NULL,'.
				'`password` varchar(150) COLLATE utf8mb4_bin NOT NULL,'.
				'`name` varchar(50) COLLATE utf8mb4_bin NOT NULL,'.
				'`roles` varchar(250) COLLATE utf8mb4_bin NOT NULL,'.
				'`status` varchar(20) DEFAULT \'active\' NOT NULL,'.
				'`data` text COLLATE utf8mb4_bin NOT NULL,'.
				'PRIMARY KEY (`uid`) '.
			') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin';
		$res = $this->database->query($sql);
		if ($res === FALSE) {
			throw new WebAppException('Cannot create user table: '.$this->database->error());
		}

		// Create initial superadmin
		$now      = new \TgUtils\Date(time(), WFW_TIMEZONE);
		$password = \TgUtils\Utils::generateRandomString();
		$sql = 'INSERT INTO '.$this->database->quoteName($this->tableName).' (`created_on`, `email`, `password`, `name`, `roles`, `status`, `data`) VALUES '.
			   '('.$this->database->quote($now->toMysql()).', \'superadmin@example.com\', '.$this->database->quote($password).', \'Application Superadmin\', \'superadmin\', \'active\', \'{}\')';
		$res = $this->database->query($sql);
		if ($res === FALSE) {
			throw new WebAppException('Cannot create superadmin: '.$this->database->error());
		}

		error_log('===========> Superadmin Email:    superadmin@example.com');
		error_log('===========> Superadmin Password: '.$password);
		error_log('===========> YOU MUST CHANGE THIS IMMEDIATELY!');
		return TRUE;
	}

	public function getByEmail($email) {
		return $this->findSingle(array('email' => strtolower($email), array('status', User::STATUS_DELETED, '!=')));
	}

	public function findByRole($role) {
		$rc = array();
		$users = $this->find(array('status' => 'active', array('roles', '%'.$role.'%', 'LIKE')));
		foreach ($users AS $user) {
			if ($user->hasRole($role)) {
				$rc[] = $user;
			}
		}
		return $rc;
	}
		
	public function search($s, $order = array(), $startIndex = 0, $maxObjects = 0) {
		return $this->find($this->getSearchClause($s), $order, $startIndex, $maxObjects);
	}

	public function getSearchClause($s) {
		$rc = array();
		if (($s != NULL) && !\TgUtils\Utils::isEmpty($s)) {
			$where = '';
			foreach (explode(' ', $s) AS $part) {
				$value = $this->database->escape(strtolower($part));
				$where .= ' OR (LOWER(`name`) LIKE \'%'.$value.'%\') OR (LOWER(`email`) LIKE \'%'.$value.'%\')';
			}
			$rc[] = substr($where, 4);
		}
		return $rc;
	}
}

