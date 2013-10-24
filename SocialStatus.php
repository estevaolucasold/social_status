<?php

require_once('TwitterStatus.php');
require_once('InstagramStatus.php');
require_once('YouTubeStatus.php');

class SocialStatus {
	private static $instance = null;

	public $cache_enabled = false;
	public $timezone = 'America/Sao_Paulo';

	private $classes_name = array(
		'twitter' 		=> 'TwitterStatus',
		'instagram'		=> 'InstagramStatus',
		'youtube'		=> 'YouTubeStatus'
	);

	public static function get_instance($options = array()) {
		if (null == self::$instance) {
			self::$instance = new self($options);
		}
 
		return self::$instance;
	}
	
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
		return $this->get_data($limit);
	}

	private function get_data($limit) {
		$responses = array();

		foreach ($this->networks as $network => $instance) {
			if ($status = $instance->get_status($limit, $this->cache_enabled)) {
				$responses = array_merge($status, $responses);
			}
		}

		return array_splice($this->sort($responses), 0, $limit);
	}

	public function sort($data) {
		usort($data, function($a, $b) {
			if ($a->created_time == $b->created_time) {
		        return 0;
		    }

		    return ($a->created_time > $b->created_time) ? -1 : 1;
		});

		return $data;
	}

	public function set_timezone($timezone) {
		date_default_timezone_set($timezone);
	}

	public function update_cache() {
		foreach ($this->networks as $network) {
			$network->update_cache();
		}
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