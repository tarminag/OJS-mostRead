<?php

/**
 * @file plugins/blocks/mostRead/MostReadSettingsForm.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class MostReadSettingsForm
 * @ingroup plugins_generic_mostRead
 *
 * @brief Form for journal managers to modify Most Read plugin settings
 */

import('lib.pkp.classes.form.Form');

class MostReadSettingsForm extends Form {

	/**
	 * MostReadSettingsForm constructor.
	 * @param $plugin
	 */
	function __construct($plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
		$this->setData('pluginName', $plugin->getName());

		$this->addCheck(new FormValidator($this, 'mostReadDays', 'required', 'plugins.blocks.mostRead.settings.mostReadDaysRequired'));

		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));		
	}

	/**
	 * Initialize form data.
	 */
	function initData(){
		$plugin = $this->plugin;
		$context = Request::getContext();
		$contextId = $context ? $context->getId() : CONTEXT_ID_NONE;
		$mostReadBlockTitle = unserialize($plugin->getSetting($contextId, 'mostReadBlockTitle'));
		$this->setData('mostReadDays', $plugin->getSetting($contextId, 'mostReadDays'));
		$this->setData('mostReadBlockTitle', $mostReadBlockTitle);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData(){
		$this->readUserVars(array('mostReadDays', 'mostReadBlockTitle'));
	}

	/**
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		return parent::fetch($request);
	}

	/**
	 * Save settings.
	 */
	function execute(){
		$plugin = $this->plugin;
		$context = Request::getContext();
		$contextId = $context ? $context->getId() : CONTEXT_ID_NONE;
		$mostReadBlockTitle = serialize($this->getData('mostReadBlockTitle'));

		$plugin->updateSetting($contextId, 'mostReadDays', $this->getData('mostReadDays'), 'string');
		$plugin->updateSetting($contextId, 'mostReadBlockTitle', $mostReadBlockTitle, 'string');

		# empty current cache
		$cacheManager = CacheManager::getManager();
		$cache = $cacheManager->getCache('mostread', $contextId, array($plugin, '_cacheMiss'));
		$cache->flush();
	}
}
