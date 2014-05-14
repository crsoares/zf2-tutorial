<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\View\Helper\PaginationControl;
use Zend\Cache\StorageFactory;

use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;

use Zend\Stdlib\Parameters;

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

	public function forwardAction()
	{
		$datas = $this->prg(null);
		print_r($datas);die;
		return $this->plugin('forward')->dispatch('Album\Controller\Album', array('action' => 'index'));
	}

	public function redirectAction()
	{
		return $this->plugin('redirect')->toRoute('home');
	}

	public function toUrlAction()
	{
		return $this->plugin('redirect')->toUrl('http://www.zend.com/fr')->setStatusCode(301);
	}

	public function fromRouteAction()
	{
		$home = $this->plugin('url')->fromRoute('home');
		$homeCanonical = $this->plugin('url')->fromRoute('home', array(), array('force_canonical' => true));

		return $homeCanonical;
	}

	public function testeRouteAction()
	{
		$this->request->setMethod('POST');
		$this->request->setPost(new Parameters(array(
			'post_key' => 'value',
		)));
		return $this->prg('/album/forward', true);
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

	public function ex1Action()
	{
		$view = new ViewModel();

		$view1 = new ViewModel(array('val' => 'teste layout'));
		$view1->setTemplate('album/album/teste1.phtml');

		$view->addChild($view1, 'valor1');

		return $view;
	}

	public function ex2Action()
	{

		$view = new ViewModel(array('teste' => 'Maria'));

		$view->setCaptureTo('content');

		return $view;
	}

	public function ex3Action()
	{
		$response = $this->getResponse();
		$view = new ViewModel(array('message' => 'estudos'));

		$view->setTerminal(false);
		$view->setTemplate('album/album/ex2.phtml');
		$html = $this->getServiceLocator()->get('ViewRenderer')->render($view);

		$response->setStatusCode($response::STATUS_CODE_200);
		$response->setContent($html);
		return $response;
	}

	public function ex4Action()
	{
		$layout = $this->layout();
		$sidebarView = new ViewModel();
		$sidebarView->setTemplate('album/album/sidebar.phtml');
		$layout->addChild($sidebarView, 'sidebar');

		return new ViewModel();
	}

	public function ex5Action()
	{
		$layout = $this->layout();
		$layout->setTemplate('layout/novoLayout.phtml');

		$value = 'muda layout';

		return new ViewModel(array(
			'value' => $value
		));
	}

	public function ex6Action()
	{
		$this->layout('layout/novoLayout.phtml');

		$layout = $this->layout();
		$disqusApiKey = false;
		if(isset($layout->disqusApiKey)) {
			$disqusApiKey = $layout->disqusApiKey;
		}

		$viewModel = new JsonModel(array('teste' => 'novo teste'));

		$this->layout()->footer = 'variavel de layout';

		return $viewModel;
	}

	public function ex7Action()
	{
		$renderer = new PhpRenderer();

		$resolver = new Resolver\AggregateResolver();

		$renderer->setResolver($resolver);

		$map = new Resolver\TemplateMapResolver(array(
			//'layout/layout' => getcwd() . '/view/layout.phtml',
			'album/album' => getcwd() . '/view/album/album/ex7.phtml'
		));

		$stack = new Resolver\TemplatePathStack(array(
			'script_paths' => array(
				getcwd() . '/view'
			)
		));

		$viewModel = new ViewModel();
		$viewModel->setTemplate('album/album')
				  ->setVariable('teste', 'novo layout');

		$resolver->attach($map)
			     ->attach($stack);

	    $renderer->render($viewModel);

	}
}