<?php
require_once 'common.php';
require_once 'url.php';

/*
 helper_form
 HTML form helpers
 mReschke 2012-10-29
*/
class helper_form {
	public $post;
	public $get;

	public function __construct() {
		$this->post = (object) $_POST;
		$this->get = (object) $_GET;
	}


	public function text($name, $value=null, $id=null, $attributes=null) {
		if (is_null($value)) $value = $_POST[$name];
		if (!$id) $id = $name;
		return "<input type='text' name='$name' id='$id' value='".htmlentities($value)."' $attributes />";
	}

	public function textarea($name, $value=null, $id=null, $attributes=null) {
		if (is_null($value)) $value = $_POST[$name];
		if (!$id) $id = $name;
		return "<textarea name='$name' id='$id' $attributes>".htmlentities($value)."</textarea>";
	}

	public function password($name, $value=null, $id=null, $attributes=null) {
		if (is_null($value)) $value = $_POST[$name];
		if (!$id) $id = $name;
		return "<input type='password' autocomplete='off' name='$name' id='$id' value='".htmlentities($value)."' $attributes />";
	}

	public function checkbox($name, $value=null, $id=null, $attributes=null) {
		if (!$id) $id = $name;
		return "<input type='checkbox' name='$name' id='$id'$attributes />$value";
	}

	public function select($name, $options, $selected=null, $id=null, $attributes=null) {
		if (is_null($selected)) $selected = $_POST[preg_replace('"\[|\]"', '', $name)];
		if (!$id) $id = $name;
		$out = "<select name='$name' id='$id' $attributes>";
		$is_assoc = array_keys($options) !== range(0, count($options) -1);
		foreach ($options as $key=>$value) {
			if (!$is_assoc) $key = $value;
			if ((is_array($selected) && in_array($key, $selected)) || $key == $selected) {
				$out .= "<option selected='selected' value='$key'>$value</option>";
			} else {
				$out .= "<option value='$key'>$value</option>";	
			}
		}
		$out .= "</select>";
		return $out;
	}

	public function submit($name, $value, $id=null, $attributes=null) {
		if (!$id) $id = $name;
		return "<input type='submit' name='$name' id='$id' value='$value' $attributes />";
	}

	public function button($name, $value, $id=null, $attributes=null) {
		if (!$id) $id = $name;
		return "<input type='button' name='$name' id='$id' value='$value' $attributes />";
	}

	public function link($url, $value, $id=null, $attributes=null) {
		if (!$id) $id = $name;
		return "<a href='$url' id='$id' $attributes>$value</a>";

	}

}