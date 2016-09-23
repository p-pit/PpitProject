<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitProject\Controller\Planning' => 'PpitProject\Controller\PlanningController',
        ),
    ),
				
    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/',
                ),
          		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       		),
            ),
        	'planning' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/planning',
                    'defaults' => array(
                        'controller' => 'PpitProject\Controller\Planning',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	       			'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                            'action' => 'index',
	                        ),
	                    ),
	                ),
	       			'export' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/export',
	                    	'defaults' => array(
	                            'action' => 'export',
	                        ),
	                    ),
	                ),
	       		),
        	),
		),
    ),

    'bjyauthorize' => array(
        // Guard listeners to be attached to the application event manager
        'guards' => array(
            'BjyAuthorize\Guard\Route' => array(

            	// Planning
				array('route' => 'planning/export', 'roles' => array('user')),
            )
        )
    ),

    'view_manager' => array(
    	'strategies' => array(
    			'ViewJsonStrategy',
    	),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',       // On défini notre doctype
        'not_found_template'       => 'error/404',   // On indique la page 404
        'exception_template'       => 'error/index', // On indique la page en cas d'exception
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            'ppitProject' => __DIR__ . '/../view',
        ),
    ),
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'ppit-project'
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),

	'ppitRoles' => array(
			'PpitProject' => array(
			),
	),
	'ppitCommitmentDependencies' => array(
	),
);
