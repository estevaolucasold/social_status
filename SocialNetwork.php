<?php

abstract class SocialNetwork {
	public $cache_expire_time = 86400;
	public $max_cache_itens_size = 100;

	abstract public function get_data();

	public function __construct($options = array()) {
		$basedir = __DIR__;

		$this->cache_file = $basedir . '/' . get_class($this) . '_cache.json';

		if (!is_writable($basedir)) {
			chmod($basedir, 755);
		}
	}

	public function get_latest_one($cache = true) {
		$response = (array)$this->get_status(1, $cache);

		if ($response) {
			return array_shift(array_values($response));
		}
	}

	public function get_status($limit, $cache = true) {
		return $this->sort($cache ? $this->read_cache($limit) : $this->get_data($limit));
	}

	public function read_cache($limit) {
		$content = @file_get_contents($this->cache_file);

		if (file_exists($this->cache_file) && (time() - $this->cache_expire_time < @filemtime($this->cache_file)) && !empty($content)) {
			return $this->unique(json_decode($content));
		} else {
			$content = $this->get_data($limit);
			file_put_contents($this->cache_file, json_encode($content));

			return json_decode(json_encode($content));
		}
	}

	public function update_cache() {
		$cached = array();
		$params = array();

		if (file_exists($this->cache_file)) {
			$cached = json_decode(file_get_contents($this->cache_file));

			if (count($cached) && property_exists($this, 'min_id')) {
				$params = array($this::$min_id => $cached[0]->id);
			}
		}

		$status = $this->get_data(10, $params);

		if (count($status)) {
			$content = array_merge($this->sort($status), $this->sort($cached));
			file_put_contents($this->cache_file, json_encode(array_splice($this->unique($content), 0, $this->max_cache_itens_size)));
			touch($this->cache_file, time());
		}
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

	public function clear_cache() {
		return unlink($this->cache_file);
	}

	public function unique($data) {
		return array_map('unserialize', array_unique(array_map('serialize', $data)));
	}
}