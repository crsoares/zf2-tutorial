<?php

namespace Album\View\Renderer;

//Desde nós só queremos implementar o getEncoding () método, podemos estender o renderizador nativo Zend
use Zend\View\Renderer\PhpRenderer;

class MyRenderer extends PhpRenderer
{
	protected $encoding;

	public function __construct($encoding)
	{
		parent::__construct();
		$this->encoding = $encoding;
	}

	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
		return $this;
	}

	public function getEncoding()
	{
		return $this->encoding;
	}
}