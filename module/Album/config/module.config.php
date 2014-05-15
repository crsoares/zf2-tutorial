<?php

namespace Album;

return array(
	'router' => array(
		'routes' => array(
			'album' => array(
				'type' => 'Zend\Mvc\Router\Http\Segment',
				'options' => array(
					'route' => '/album[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Album\Controller\Album',
						'action' => 'index'
					)
				)	
			)
		)
	),
	'view_helper_config' => array(
		'flashmessenger' => array(
			'message_open_format' => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><p>',
			'message_close_string' => '</p></div>',
			'message_separator_string' => '</p><p>',
		)
	),
	'controllers' => array(
		'invokables' => array(
			'Album\Controller\Album' => 'Album\Controller\AlbumController',
		)
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'album' => __DIR__ . '/../view',
		)
	)
);