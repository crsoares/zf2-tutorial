<?php

namespace Album;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\MvcEvent;
use Zend\View\Strategy\PhpRendererStrategy;

use Album\View\Renderer\MyRenderer;

use Album\Model\Album;
use Album\Model\AlbumTable;

class Module implements ConfigProviderInterface,
						/*AutoloaderProviderInterface,*/
						ServiceProviderInterface
{
	/*public function onBootstrap($e)
	{
		//Registrar um evento expedição
		$app = $e->getParam('application');
		//$app->getEventManager()->attach('dispatch', array($this, 'setLayout'));

		$event = $e->getApplication();
		//print_r($e->getApplication());die;
		$event->getEventManager()->attach('render', array($this, 'registerJsonStrategy'), 100);
	}*/

	public function setLayout($e)
	{
		$matches = $e->getRouteMatch();
		$controller = $matches->getParam('controller');
		if(false === strpos($controller, __NAMESPACE__)) {
			//não um controlador a partir deste módulo
			return;
		}

		$viewModel = $e->getViewModel();
		//$viewModel->setTemplate('layout/novoLayout.phtml');
	}

	public function registerJsonStrategy($e)
	{
		$app = $e->getTarget();
		$locator = $app->getServiceManager();
		$view = $locator->get('Zend\View\View');
		$jsonStrategy = $locator->get('ViewJsonStrategy');

		//Anexar estratégia, que é um agregado de escuta, de alta prioridade
		$view->getEventManager()->attach($jsonStrategy, 100);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	/*public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				)
			)
		);
	}*/

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'Album\Model\AlbumTable' => function($sm) {
					$tableGateway = $sm->get('AlbumTableGateway');
					$table = new AlbumTable($tableGateway);
					return $table;
				},
				'AlbumTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Album());
					return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
				},
				//Registrar nosso processador personalizado no gerenciador de serviços
				'MyCustomRenderer' => function($serviceManager) {
					$myRenderer = new MyRenderer('ISO-8859-1');
					return $myRenderer;
				},
				'MyCustomStrategy' => function($serviceManager) {
					//Como dito antes, nós só queremos implementar o método) getEncoding (, para que possamos usar
					//Zend\View\Strategy\PhpRendererStrategy e apenas fornecer nosso renderizador personalizado para ele.
					$myRenderer = $serviceManager->get('MyCustomRenderer');
					$strategy = PhpRendererStrategy($myRenderer);
					return $strategy;
				}
			)
		);
	}

	public function onBootstrap(MvcEvent $e)
	{
		//Registrar um evento render
		$app = $e->getParam('application');
		$app->getEventManager()->attach('render', array($this, 'registerMyStrategy'), 100);
	}

	public function registerMyStratety(MvcEvent $e)
	{
		$app = $e->getTarget();
		$locator = $app->getServiceManager();
		$view = $locator->get('Zend\View\View');
		$myStrategy = $locator->get('MyCustomStrategy');

		//Anexar estratégia, que é um agregado de escuta, de alta prioridade
		$view->getEventManager()->attach($myStrategy, 100);
	}
}