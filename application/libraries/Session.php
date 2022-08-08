<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include BASEPATH.'libraries/Session/Session.php';

class Session extends CI_Session {

	public function userdata($key = NULL) {
		$CI =& get_instance();
		$userdata = array();

		if (isset($key)) {
			if (is_array($key)) {
				$sessionKey = array_keys($_SESSION);
				foreach ($key as $index) {
					if (in_array($index, $sessionKey, TRUE)) {
						$userdata[$index] = $_SESSION[$index];
					}
				}

				return $userdata;
			} else return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;

		} elseif (empty($_SESSION)) {
			return array();
		}

		$_exclude = array_merge(
			array('__ci_vars'),
			$CI->session->get_flash_keys(),
			$CI->session->get_temp_keys()
		);

		foreach (array_keys($_SESSION) as $key) {
			if (!in_array($key, $_exclude, TRUE)) {
				$userdata[$key] = $_SESSION[$key];
			}
		}

		return $userdata;
	}
}

/* End of file Session.php */
/* Location: ./application/libraries/Session.php */
