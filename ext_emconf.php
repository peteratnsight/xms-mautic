<?php
$EM_CONF[$_EXTKEY] = [
	'title' => 'XMS Mautic Integration',
	'description' => 'Tracker from Mautic',
	'category' => 'plugin',
	'author' => 'Peter Pan',
	'author_email' => '',
	'state' => 'stable',
    	'autoload' => [
        	'psr-4' => ['Xms\\XmsMautic\\' => 'Classes']
    	]
];