<?php

namespace WebApp;

use TgLog\Log;

class Service {

	public $app;
	public $theme;
	public $page;

	public function __construct() {
		$this->app   = $this->createApp();
		$this->theme = $this->createTheme();
		$this->page  = $this->createPage();
	}

	public function run() {
		// Process the request first
		$rc     = $this->page->processRequest();
		$action = is_array($rc) ? $rc[0] : $rc;

		// Render when required
		if ($action == 'render') {
			Log::debug('$_SERVER=', $_SERVER);
			Log::debug('Request=', $this->app->request);
			$this->theme->render($this->page);
		} else if ($action == 'redirect') {
			// redirect
			$this->app->afterRequest();
			header('Location: '.$rc[1]);
		}
		$this->app->afterRequest();
	}


	protected function createPage() {
		$request = $this->app->request;

		// Check the path info
		$canonical = $this->app->router->getCanonicalPath();
		if ($request->path != $request->relativeAppPath.$canonical) {
			$params  = $request->params;
			if ($params) $params = '?'.$params;
			header('Location: '.$request->webRootUri.$request->relativeAppPath.$canonical.$params);
			//Log::debug('redirect='.$request->webRootUri.$request->relativeAppPath.$canonical.$params);
		}

		// Try to find the correct page
		$page  = $this->app->router->getPage();

		// Get a specific error page from the theme
		if ($page == NULL) {
			$page = $this->theme->getErrorPage(404);
		}

		// Get the default error page
		if ($page == NULL) {
			$page = new Error\Error404($this->app);
		}
		return $page;
	}

	protected function createApp() {
		// Default
		$config = NULL;
		if (file_exists(WFW_ROOT_DIR.'/application.php')) {
			$config = Configuration::fromPhpFile(WFW_ROOT_DIR.'/application.php');
		} else {
			$config = new Configuration(array(
				'application' => 'WebApp\\Application',
				'theme'       => 'WebApp\\DefaultTheme\\DefaultTheme',
				'name'        => 'My Application',
			));
		}
		if ($config->has('logLevel')) {
			Log::setDefaultLogLevel($config->get('logLevel'));
		}
		$appClass = $config->get('application');
		if (substr($appClass, 0, 1) != '\\') $appClass = '\\'.$appClass;
		$rc = new $appClass($config);
		$rc->init();
		return $rc;
	}

	/**
     * Returns the theme to be used.
     * @param object $app - the application object
     * @return the theme object
     */
	protected function createTheme() {
		$themeName = NULL;
		if ($this->app != NULL) {
			$themeName = $this->app->getTheme();
		}
		if ($themeName == NULL) {
			$themeName = '\\WebApp\\DefaultTheme\\DefaultTheme';
		} else if (substr($themeName, 0, 1) != '\\') {
			$themeName = '\\'.$themeName;
		}

		if (!class_exists($themeName)) {
			$themeName = '\\WebApp\\DefaultTheme\\DefaultTheme';
		}
		return new $themeName($this->app);
	}
}
