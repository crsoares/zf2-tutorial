<?php

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AlbumTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll($paginated = false)
	{
		if($paginated) {
			//criar um novo objeto Select para o álbum de mesa
			$select = new Select('album');
			//criar um novo conjunto de resultados com base na entidade Album
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Album());
			//criar um novo objeto adaptador paginação
			$paginatorAdapter = new DbSelect(
				//nosso seleto objeto configurado
				$select,
				//o adaptador para executá-lo contra 
				$this->tableGateway->getAdapter(),
				//o resultado definido para hidrato 
				$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			return $paginator;
		}
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	public function getAlbum($id)
	{
		$id = (int)$id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if(!$row) {
			throw new \Exception("Não foi possível encontrar linha $id");
		}
		return $row;
	}

	public function saveAlbum(Album $album)
	{
		$data = array(
			'artist' => $album->artist,
			'title' => $album->title,
		);

		$id = (int)$album->id;
		if($id == 0) {
			$this->tableGateway->insert($data);
		} else {
			if($this->getAlbum($id)) {
				$this->tableGateway->update($data, array('id' => $id));
			} else {
				throw new \Exception("Id Album não existe.");
			}
		}
	}

	public function deleteAction($id)
	{
		$this->tableGateway->delete(array('id' => $id));
	}
}