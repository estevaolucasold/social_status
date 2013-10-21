<?php

require_once('Twitter.php');
require_once('Instagram.php');

date_default_timezone_set('America/Sao_Paulo');

class SocialStatus {
	public $cache_file = 'cache.json';
	public $expire_time = 86400; //24 * 60 * 60;
	public $cache_enabled = false;

	public function __construct($options = array()) {
		$this->twitter 		= new TwitterStatus($options['twitter']);
		$this->instagram 	= new InstagramStatus($options['instagram']);

		$this->networks = array($this->twitter, $this->instagram);
	}

	public function get_all_status() {
		$file = $this->cache_file;
		
		if ($this->cache_enabled && file_exists($file) && (time() - $this->expire_time < filemtime($file))) {
			return json_decode(file_get_contents($file));
		} else {
			$content = $this->get_data();
			file_put_contents($file, json_encode($content));
			
			return $content;
		}
	}

	private function get_data() {
		$responses = array();

		foreach ($this->networks as $network => $instance) {
			if ($status = $instance->get_status()) {
				$responses = array_merge($status, $responses);
			}
		}

		$responses = $responses;

		return (object)$this->sort($responses);
	}

	public function sort($data) {
		usort($data, function($a, $b) {
		    if ($a['created_time'] == $b['created_time']) {
		        return 0;
		    }
		    return ($a['created_time'] > $b['created_time']) ? -1 : 1;
		});

		return $data;
	}
}

?>