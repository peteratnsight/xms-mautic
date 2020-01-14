<?php
return array(
	'ctrl' => array(
		'title'	=> 'Dynamic Content Conditions',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => FALSE,
		'sortby' => 'sorting',

		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('xms_mautic') . 'Resources/Public/Icons/newsletter.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, name',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, name, ifsubject, ifaction, ifdetails, ifvalue, thenaction'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	
	
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'name' => array(
			'exclude' => 1,
			'label' => 'Name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),

		'ifsubject' => array(
			'exclude' => 1,
			'label' => 'If Subject Type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('Anonymous', 'anonymous'),
					array('Not Anonymous', 'notanonymous'),
					array('Is Assigned to List ID (:Value)', 'isassigned'),
					array('Not Assigned to List ID (:Value)', 'notassigned'),
					array('Is Confirmed Assigned to List ID (:Value)', 'isconfirmed'),
					array('Not Booked to Event ID (:Value)', 'notbooked'),
					array('Is Booked to Event ID (:Value)', 'isbooked'),
					array('Is DNC', 'isdnc'),
					array('More Hits Total (:Value)', 'greaterhitstotal'),
					array('Smaller Hits Total (:Value)', 'lesshitstotal'),
					array('More Domain Hits Total (:Value|Page)', 'greaterdomainhits'),
					array('Smaller Domain Hits Total (:Value|Page)', 'lessdomainhits'),
					array('More Page Hits Total (:Value|Page)', 'greaterpagehits'),
					array('Smaller Page Hits Total (:Value|Page)', 'lesspagehits'),
					array('Has Last Referer From URL (:Value)', 'lastrefererfrom'),
					array('Has Calling Parameter with Key (:Value)', 'lastgetparameter'),

					array('Has Longer Visit Time Than (:Value)', 'greatervisittime'),
					array('Has Shorter Visit Time Than (:Value)', 'lessvisittime'),
					array('Has More Points Than (:Value)', 'greaterpoints'),
					array('Has Less Points Than (:Value)', 'lesspoints'),
					array('Has Tag(s) (:Value)', 'hastags'),
					array('Has Not Tag(s) (:Value)', 'nottags'),

					array('Is New Visit', 'isnewvisit'),

				),
				'size' => 5,
				'multiple' => 1,
				'maxitems' => 5,
			)
		),
		'ifaction' => array(
			'exclude' => 1,
			'label' => 'If Action Type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('Show ThenAction Element', 'showelement'),
					array('Add to Campaign (:Details)', 'addtocampaign'),
					array('Assign to List (:Details)', 'assigntolist'),
					array('Add to Tags (:Details)', 'addtags'),
				),
				'size' => 5,
				'multiple' => 1,
				'maxitems' => 5,
				'default' => "showelement"
			)
		),
		'ifdetails' => array(
			'exclude' => 1,
			'label' => 'If Condition Details Definition',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'ifvalue' => array(
			'exclude' => 1,
			'label' => 'If Condition Value',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'thenaction' => array(
			'exclude' => 1,
			'label' => 'Then Action Value (ttcontent:xxx oder mauticform:1',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
	),
);