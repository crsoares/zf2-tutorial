<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Album\Model\Album;
use Album\Form\AlbumForm;

class AlbumController extends AbstractActionController
{
	protected $albumTable;

	public function indexAction()
	{
		//pegue o paginador do AlbumTable
		$paginator = $this->getAlbumTable()->fetchAll(true);
		//definir a página atual com o que foi passado na string de consulta, ou 1 se nenhum set
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		//definir o número de itens por página para 10
		$paginator->setItemCountPerPage(10);
		return new ViewModel(array(
			'paginator' => $paginator,
			//'albums' => $this->getAlbumTable()->fetchAll(),
		));
	}

	public function addAction()
	{
		$form = new AlbumForm();
		$form->get('submit')->setValue('Add');

		$request = $this->getRequest();
		if($request->isPost()) {
			$album = new Album();
			$form->setInputFilter($album->getInputFilter());
			$form->setData($request->getPost());

			if($form->isValid()) {
				$album->exchangeArray($form->getData());
				$this->getAlbumTable()->saveAlbum($album);

				return $this->redirect()->toRoute('album');
			}
		}
		return array('form' => $form);
	}

	public function editAction()
	{

	}

	public function deleteAction()
	{

	}

	public function getAlbumTable()
	{
		if(!$this->albumTable) {
			$sm = $this->getServiceLocator();
			$this->albumTable = $sm->get('Album\Model\AlbumTable');
		}
		return $this->albumTable;
	}
}