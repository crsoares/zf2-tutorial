<?php

namespace Album\View\Helper;

use Zend\View\Helper\AbstractHelper;

class SpecialPurpose extends AbstractHelper
{
	protected $count = 0;

	public function __invoke()
	{
		$this->count++;
		$output = sprintf("Eu vi 'The Jerk' %d tempo(s).", $this->count);
		/*$escaper = $this->getView()->plugin('escapehtml');
		return $escaper($output);*/
		return $output;
	}
}