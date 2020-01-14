<?php
namespace Xms\XmsEventbooking\ViewHelpers\Be;


class IncludeClientUiViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper {
	/**
	 * Includes the given Javascript file
	 *
	 * @param bool $jquery Include JQuery
	 * @param bool $jqueryui Include JQueryUi
	 * @return void
	 */
	public function render($jquery = FALSE, $jqueryui = FALSE) {
		$doc = $this->getDocInstance();
		$pageRenderer = $doc->getPageRenderer();
		if ($jquery) {
			$pageRenderer->loadJquery(NULL, NULL, $pageRenderer::JQUERY_NAMESPACE_NONE);
		}
		
		$extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('xms_eventbooking');
		$pageRenderer->addJsFile($extRelPath . 'Resources/Public/Js/bootstrap.min.js');
		$pageRenderer->addJsFile($extRelPath . 'Resources/Public/Js/backend_script.js');

		$pageRenderer->addCssFile($extRelPath . 'Resources/Public/Css/bootstrap.min.css');
		$pageRenderer->addCssFile($extRelPath . 'Resources/Public/Css/backend_styles.css');
	}
}
?>