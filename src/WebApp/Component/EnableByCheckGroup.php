<?php

namespace WebApp\Component;

/** Enable/Disable a component when a checkbox is clicked */
abstract class EnableByCheckGroup extends FormElementGroup {

	public function __construct($parent, $id, $label, $checkText, $value = NULL, $inverseCheck = FALSE) {
		parent::__construct($parent, $label);
		$this->setValue($value);
		$this->setId($id.'_checked_group');
		$this->setInverseCheck($inverseCheck);
		$this->addClass('elem-by-check');

		$this->check = new Checkbox($this, $id.'_checked', 'checked');
		$this->check->setLabel($checkText);
		$this->check->setAttribute('data-role', 'dynamic-check-enable');
		$this->inputDiv = new Div($this);
		$this->inputDiv->setAttribute('data-role', 'dynamic-check-input');
		$this->input = $this->createInput($this->inputDiv, $id.'_value', $value);
	}

	public function setInline($value) {
		if ($value) {
			$this->addClass('elem-by-check-inline');
			$this->inputDiv->setStyle('flex', '1 auto')->addClass('ml-2');
		} else {
			$this->removeClass('elem-by-check-inline');
			$this->inputDiv->removeStyle('flex')->removeClass('ml-2');
		}
	}

	public function setInverseCheck($inverseCheck) {
		$this->setAttribute('data-inverse', $inverseCheck ? 'true' : 'false');
	}

	public function setChecked($isChecked) {
		$this->check->setChecked($isChecked);
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}

	protected function createInput($parent, $id, $value) {
		return new Text('createInput() was not implemented');
	}

}
