<?php $this->headScript()->captureStart(); ?>
	var action = '<?php echo $this->baseUrl ?>';
	$('foo_form').action = action;
<?php $this->headScript()->captureEnd(); ?>