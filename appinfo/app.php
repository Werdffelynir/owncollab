<?php
/**
 * ownCloud chart application
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Your Name <mail@example.com>
 * @copyright Your Name 2016
 */

namespace OCA\Owncollab\AppInfo;

use OCA\Owncollab\Helper;
use OCP\AppFramework\App;
use OCP\Util;

$appName = 'owncollab';
$app = new App($appName);
$container = $app->getContainer();


/**
 * Navigation menu settings
 */
$container->query('OCP\INavigationManager')->add(function () use ($container, $appName) {
	$urlGenerator = $container->query('OCP\IURLGenerator');
	$l10n = $container->query('OCP\IL10N');
	return [
		'id' => $appName,
		'order' => 10,
		'href' => $urlGenerator->linkToRoute($appName.'.main.index'),
		'icon' => $urlGenerator->imagePath($appName, 'app.svg'),
		'name' => $l10n->t('DashBoard')
	];
});


/**
 * Loading translations
 * The string has to match the app's folder name
 */
Util::addTranslations($appName);


/**
 * Application styles and scripts
 */
if(Helper::isAppPage($appName)){
	Util::addStyle($appName, 'main');
	Util::addStyle($appName, 'media');
	Util::addStyle($appName, 'jquery-ui-timepicker');
//	Util::addStyle($appName, 'jquery.custom-scrollbar');
//	Util::addScript($appName,'jquery-ui-timepicker');
//	Util::addScript($appName, 'jquery.custom-scrollbar');
	Util::addScript($appName, 'libs/jquery-ui-timepicker');
	Util::addScript($appName, 'libs/an.script_1.0.2');
	Util::addScript($appName, 'libs/ns.application');
	Util::addScript($appName, 'init');

}

/**
 * Detect and appoints styles and scripts for particular app page
 */
$currentUri = Helper::getCurrentUri($appName);

if($currentUri == '/') {}