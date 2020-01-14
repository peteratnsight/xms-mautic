<?php

namespace Xms\XmsMautic\Service;

class NewsletterService implements \TYPO3\CMS\Core\SingletonInterface {

     /**
     * @var mixed
     */
    protected $settings = NULL;
    
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $injectedpluginconfigManager;


    public function registerForNewsletter() {

    }


}