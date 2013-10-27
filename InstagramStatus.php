<?php

require_once('SocialNetwork.php');
require_once('sdks/instagram/instagram.class.php');

class InstagramStatus extends SocialNetwork {
	public static $name   = 'instagram';
	public static $min_id = 'min_id';

	public function __construct($options) {
		$this->options = $options;

		$this->instance = new Instagram($options['keys']);
		$this->instance->getOAuthToken($options['keys']['code']);
		$this->instance->setAccessToken($options['keys']['access_token']);

		parent::__construct($options);
	}

	public function get_data($limit = 10, $params = array()) {
		$response = $this->instance->getUserMedia($this->options['user_id'], $limit, $params);

		if ($response) {
			return $this->filter_data($response->data);
		}

		return array();
	}

	private function filter_data($data) {
		$filtered = array();

		foreach ($data as $item) {
			$filtered[] = (object)array(
				'id'			=> $item->id,
				'type'			=> $this::$name,
				'created_time' 	=> $item->created_time,
				'link'			=> $item->link,
				'images'		=> $item->images,
				'text'			=> $this->convert_links(($item->caption && $item->caption->text) ? $item->caption->text : ''),
				'user'			=> (object)array(
					'username'		=> $item->user->username,
					'fullname'		=> $item->user->full_name,
					'link'			=> 'http://instagram.com/' . $item->user->username
				)
			);
		}

		return $filtered;
	}

	private function convert_links($text) {
		$text = preg_replace("/@(\w+)/", "<a href='http://instagram.com/$1' target='_blank'>@$1</a>", $text);

		return $text;
	}
}

?>