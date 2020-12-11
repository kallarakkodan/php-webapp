<?php

namespace WebApp;

define ('WEBAPP_SUB_PATH', '/vendor/technicalguru/webapp');

use TgUtils\Request;

class Utils {

	public static function getWebRootUrl($webapp = FALSE) {
		$request = Request::getRequest();
		return $request->webRootUri.$request->relativeAppPath.($webapp ? WEBAPP_SUB_PATH : '');
	}

	public static function getJavascriptBaseUrl($webapp = FALSE) {
		return self::getWebRootUrl($webapp).'/js';
	}

	public static function getImageBaseUrl($webapp = FALSE) {
		return self::getWebRootUrl($webapp).'/images';
	}

	public static function getCssBaseUrl($webapp = FALSE) {
		return self::getWebRootUrl($webapp).'/css';
	}

	public function getFontBaseUrl($webapp = FALSE) {
		return self::getWebRootUrl($webapp).'/fonts';
	}

	public function getAppPath($localPath, $language = NULL) {
		$request = Request::getRequest();
		if ($language == NULL) $language = $request->language;
		if (!$request->useLanguagePath || ($language == NULL)) $language = '';
		else $language = '/'.$language;

		return $request->webRoot.$language.$request->relativeAppPath.$localPath;
	}
}

