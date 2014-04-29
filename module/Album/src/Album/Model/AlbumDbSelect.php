<?php

namespace Album\Model;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class AlbumDbSelect extends DbSelect
{
	public function count()
	{
		$select = new Select();
		$select->from('album')
			   ->columns(array('c' => new Expression('COUNT(1)')));

		$statement = $this->sql->prepareStatementForSqlObject($select);
		$result    = $statement->execute();
		$row 	   = $result->current();

		$this->rowCount = $row['c'];
		return $this->rowCount; 
	}
}