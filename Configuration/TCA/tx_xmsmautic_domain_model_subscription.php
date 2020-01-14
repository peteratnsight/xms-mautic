<?php
return array(
	'ctrl' => array(
		'title'	=> 'Subscription',
		'label' => 'firstname',
		'label_alt' => 'lastname,company',
	        'label_alt_force' => 1,

		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => FALSE,

		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'gender,firstname,lastname,company,email,phone,position,event_context,mautic_context,remote_ip,validstate,optinstate,subscriptions,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('xms_mautic') . 'Resources/Public/Icons/subscription.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, gender, firstname, lastname, company, email, phone, position, event_context, mautic_context, remote_ip, validstate, optinstate, subscriptions, history, sysmail_log,mautic_context_variants',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, gender, firstname, lastname, company, email, phone, position, event_context, mautic_context, remote_ip, validstate, optinstate, subscriptions,mautic_context_variants, last_syncdate, last_userupdate'),
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

		'gender' => array(
			'exclude' => 1,
			'label' => 'Gender',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('-- Label --', 0),
					array('Male', 1),
					array('Female', 2),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''

			)
		),
		'firstname' => array(
			'exclude' => 1,
			'label' => 'Firstname',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'lastname' => array(
			'exclude' => 1,
			'label' => 'Lastname',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'company' => array(
			'exclude' => 1,
			'label' => 'Company',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'email' => array(
			'exclude' => 1,
			'label' => 'Email',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required,email'
			),
		),
		'hash' => array(
			'exclude' => 1,
			'label' => 'Hash',
			'config' => array(
				'type' => 'passthrough'
			),
		),
		'phone' => array(
			'exclude' => 1,
			'label' => 'Phone',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'position' => array(
			'exclude' => 1,
			'label' => 'Position',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'event_context' => array(
			'exclude' => 1,
			'label' => 'Event Context',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'mautic_context' => array(
			'exclude' => 1,
			'label' => 'Mautic Context',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'mautic_context_variants' => array(
			'exclude' => 1,
			'label' => 'Mautic_Vontext_Variants',
			'config' => array(
				'type' => 'passthrough'
			),
		),
		'last_syncdate' => array(
			'exclude' => 1,
			'label' => 'Last SyncDate',
			'config' => array(
				'type' => 'none',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'last_userupdate' => array(
			'exclude' => 1,
			'label' => 'Last UserUpdate',
			'config' => array(
				'type' => 'none',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'remote_ip' => array(
			'exclude' => 1,
			'label' => 'Remote IP',
			'config' => array(
				'type' => 'none',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'validstate' => array(
			'exclude' => 1,
			'label' => 'Validstate',
			'config' => array(
				'type' => 'none',
				'default' => 0
			)
		),
		'optinstate' => array(
			'exclude' => 1,
			'label' => 'Optin State',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('-- Label --', 'none'),
					array('Registered', 'registered'),
					array('DoubleOptin', 'confirmed-optin'),
					array('Unlisted', 'unlisted'),
					array('Unsubscribed', 'unsubscribed'),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
		),
		'history' => array(
			'exclude' => 1,
			'label' => 'History',
			'config' => array(
				'type' => 'passthrough'
			),
		),
		'sysmail_log' => array(
			'exclude' => 1,
			'label' => 'SysMailLog',
			'config' => array(
				'type' => 'passthrough'
			),
		),
		'subscriptions' => array(
			'exclude' => 1,
			'label' => 'Subscriptions',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_xmsmautic_domain_model_newsletter',
				'foreign_field' => 'subscription',
				'maxitems' => 9999,
				'appearance' => array(
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),

		),
		
	),
);