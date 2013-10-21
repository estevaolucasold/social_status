<?php

require_once('Twitter.php');
require_once('Instagram.php');

class SocialStatus {
	public $cache_file = 'cache.json';
	public $cache_expire_time = 86400; //24 * 60 * 60;
	public $cache_enabled = false;
	public $timezone = 'America/Sao_Paulo';

	private $classes_name = array(
		'twitter' 		=> 'TwitterStatus',
		'instagram'		=> 'InstagramStatus'
	);

	public function __construct($options = array()) {
		$this->networks = array();
		$this->options = $options;
		
		$this->set_timezone($this->timezone);
		$this->loadSDKs();

		if (!count($this->networks)) {
			throw new Exception('At least one network is needed.');
		}
	}

	public function get_all_status($limit = 10) {
		$file = $this->cache_file;
		
		if ($this->cache_enabled && file_exists($file) && (time() - $this->cache_expire_time < filemtime($file))) {
			return json_decode(file_get_contents($file));
		} else {
			$content = $this->get_data($limit);
			file_put_contents($file, json_encode($content));
			
			return $content;
		}
	}

	private function get_data($limit) {
		$responses = array();

		foreach ($this->networks as $network => $instance) {
			if ($status = $instance->get_status($limit)) {
				$responses = array_merge($status, $responses);
			}
		}

		return $this->sort($responses);
	}

	public function sort($data, $property = 'created_time') {
		$sort = (array)$data;

		usort($sort, function($a, $b) {
		    if ($a[$property] == $b[$property]) {
		        return 0;
		    }

		    return ($a[$property] > $b[$property]) ? -1 : 1;
		});

		return (object)$sort;
	}

	public function set_timezone($timezone) {
		date_default_timezone_set($timezone);
	}

	private function loadSDKs() {
		foreach ($this->options as $name => $options) {
			if (array_key_exists($name, $this->classes_name)) {
				$instance = $this->$name = new $this->classes_name[$name]($options);
				$this->networks[] = $instance;
			}
		}
	}
}

?>