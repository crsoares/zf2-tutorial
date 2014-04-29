<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\View\Helper\PaginationControl;
use Zend\Cache\StorageFactory;

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

		$cache = StorageFactory::factory(array( 
		    'adapter' => array( 
		        'name' => 'filesystem', 
		        'options' => array( 
		            'ttl'       => 720000,    // cache with 200 hours 
		            //'cache_dir' => getcwd() . '/data/cache', 
		        ), 
		    ), 
		    'plugins' => array( 
		        'exception_handler' => array('throw_exceptions' => false), 
		    ), 
		));

    	//$cache->setItem('a', 'b');

        $paginator::setCache($cache);

        print_r($paginator->getPageItemCache());

		// Paginator::setDefaultScrollingStyle('Sliding');
		// PaginationControl::setDefaultViewPartial('partial/paginator.phtml');

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


	public function listarAction()
	{
		$array = array(
			'Pessoa1' => array(
				'Nome' => 'Pedro Silva',
				'Endereco' => 'Rua 1',
				'Idade' => 30
			),
			'Pessoa2' => array(
				'Nome' => 'Joao Pereira',
				'Endereco' => 'Rua 2',
				'Idade' => 20
			)
		);

		$paginator = new Paginator(new ArrayAdapter($array));

		$view = new ViewModel();
		$view->setVariable('paginator', $paginator);
		return $view;
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