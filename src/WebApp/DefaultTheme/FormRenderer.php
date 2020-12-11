<?php

namespace WebApp\DefaultTheme;

class FormRenderer extends ContainerRenderer {

	public function __construct($theme, $component) {
		parent::__construct($theme, $component, 'form');
	}

	public function render() {
		$themeClass    = new \ReflectionClass($this->theme);
		$namespaceName = $themeClass->getNamespaceName();
		$rendererClass = $namespaceName.'\\'.ucfirst($this->component->getType()).'FormRenderer';
		if (class_exists($rendererClass)) {
			$rendererClass = '\\'.$rendererClass;
			$renderer = new $rendererClass($this->theme, $this->component);
			return $renderer->render();
		}
		return parent::render();
	}
}

