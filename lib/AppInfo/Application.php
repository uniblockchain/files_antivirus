<?php
/**
 * Copyright (c) 2015 Victor Dubiniuk <victor.dubiniuk@gmail.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Files_Antivirus\AppInfo;

use OCA\Files_Antivirus\AvirWrapper;
use OCA\Files_Antivirus\Scanner\ScannerFactory;
use OCP\Activity\IManager;
use OCP\AppFramework\App;
use OCP\IL10N;
use OCP\ILogger;

class Application extends App {

	const APP_NAME = 'files_antivirus';

	public function __construct (array $urlParams = []) {
		parent::__construct(self::APP_NAME, $urlParams);
	}
	
	/**
	 * Add wrapper for local storages
	 */
	public function setupWrapper(){
		\OC\Files\Filesystem::addStorageWrapper(
			'oc_avir',
			function ($mountPoint, $storage) {
				/**
				 * @var \OC\Files\Storage\Storage $storage
				 */
				if ($storage instanceof \OC\Files\Storage\Storage) {
					$container = $this->getContainer();
					$scannerFactory = $container->query(ScannerFactory::class);
					$l10n = $container->query(IL10N::class);
					$logger = $container->query(ILogger::class);
					$activityManager = $container->query(IManager::class);
					return new AvirWrapper([
						'storage' => $storage,
						'scannerFactory' => $scannerFactory,
						'l10n' => $l10n,
						'logger' => $logger,
						'activityManager' => $activityManager,
					]);
				}

				return $storage;
			},
			1
		);
	}
}
