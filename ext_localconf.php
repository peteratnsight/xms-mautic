<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Xms.' . $_EXTKEY,
	'app',
	array(
		'Mautic' => 'index,tracker,formIncluder,subscribe,submit,manage,identify,assetForm,ajaxCaller',
		'Backend' => 'callback',
	),
	// non-cacheable actions
	array(
		'Mautic' => 'index,tracker,subscribe,submit,manage,identify,assetForm,ajaxCaller',
		'Backend' => 'callback',
		'Asset' => 'index,getContent',
	)
);

