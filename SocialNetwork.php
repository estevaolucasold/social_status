<?php

abstract class SocialNetwork {
	abstract public function get_status();
	
	public function get_latest_one() {
		$response = $this->get_status(1);

		if ($response) {
			$response = (array)$response;
			$response = $response[0];

			return (object)$response;
		}
	}
}