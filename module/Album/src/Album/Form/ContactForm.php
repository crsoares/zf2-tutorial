<?php

namespace Album\Form;

use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;

class ContactForm extends Form
{
	protected $captcha;

	public function __construct(CaptchaAdapter $captcha)
	{
		parent::__construct();

		$this->captcha = $captcha;

		$this->add(array(
			'name' => 'name',
			'options' => array(
				'label' => 'Seu Nome',
			),
			'type' => 'Text'
		));

		$this->add(array(
			'type' => 'Zend\Form\Element\Email',
			'name' => 'email',
			'options' => array(
				'label' => 'Seu endereço de email'
			)
		));

		$this->add(array(
			'name' => 'subject',
			'options' => array(
				'label' => 'Assunto',
			),
			'type' => 'Text',
		));

		$this->add(array(
			'type' => 'Zend\Form\Element\Textarea',
			'name' => 'message',
			'options' => array(
				'label' => 'Mensagem',
			)
		));

		$this->add(array(
			'type' => 'Zend\Form\Element\Captcha',
			'name' => 'captcha',
			'options' => array(
				'label' => 'Por favor, verifique que você é humano.',
				'captcha' => $this->captcha,
			)
		));

		$this->add(new Element\Csrf('security'));

		$this->add(array(
			'name' => 'send',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Submit',
			)
		));
	}
}