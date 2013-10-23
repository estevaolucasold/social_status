<?php

abstract class SocialNetwork {
	public $cache_expire_time = 86400; //24 * 60 * 60;
	
	abstract public function get_data();
	
	public function get_latest_one($cache = true) {
		$response = (array)$this->get_status(1, $cache);

		if ($response) {
			return array_shift(array_values($response));
		}
	}

	public function get_status($limit, $cache = false) {
		if ($cache) {
			return $this->read_cache($limit);
		} else {
			return $this->get_data($limit);
		}
	}

	public function read_cache($limit) {
		$file = __DIR__ . '/' . get_class($this) . '_cache.json';
		
		if (file_exists($file) && (time() - $this->cache_expire_time < filemtime($file))) {
			return json_decode(file_get_contents($file));
		} else {
			$content = $this->get_data($limit);
			file_put_contents($file, json_encode($content));
			
			return json_decode(json_encode($content));
		}
	}
}