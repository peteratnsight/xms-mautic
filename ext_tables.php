<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Xms.' . $_EXTKEY,
	'app',
	'Mautic'
);

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY));
$pluginName = strtolower('app');
$pluginSignature = $extensionName.'_'.$pluginName;
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
 \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY . '/Configuration/FlexForms/setup.xml');


$tempPages = array(
    'tx_mauticpush_flags' => array(
        'exclude' => true,
        'label' => 'Mautic Integration Flags', 
        'config' => array(
            'type' => 'check',
            'cols' => 1,
            'items' => array(
                array('noaction - Exclude from Push Popup Integration', ''),
                array('nouserident - Exclude from User Identification', ''),
            ),
        )
    ),
);

if (version_compare(TYPO3_branch, '6.2', '<')) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempPages, 1);
} else {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempPages);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages','tx_mauticpush_flags;;;;1-1-1', '', 'before:cache_timeout');


if (TYPO3_MODE === 'BE') {

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Xms.' . $_EXTKEY,
		'tools',
		'mod1',
		'',
		array(
			'Backend'=>'index,mauticAuth,requestData,updateData,syncWithMautic,integrateSubscribersFromMautic,listSubscribers,integrateSubscribersFromBooking,updateSubscribersFromBooking,mauticConsole',
		),
		array(
			'access' => 'user,group',
			'labels' => 'LLL:EXT:xms_mautic/Resources/Private/Language/locallang_be.xlf',
		)
	);

}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Mautic Tracker');

//t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds_pi1.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_xmsmautic_domain_model_condition');