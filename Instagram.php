<?php 

require_once('SocialNetwork.php');
require_once('sdks/instagram/instagram.class.php');

class InstagramStatus extends SocialNetwork {
	public $name = 'instagram';

	public function __construct($options) {
		$this->options = $options;

		$this->instance = new Instagram($options['keys']);
		$this->instance->getOAuthToken($options['keys']['code']);
		$this->instance->setAccessToken($options['keys']['access_token']);
	}

	public function get_data($limit = 10) {
		$response = $this->instance->getUserMedia($this->options['user_id'], $limit);

		if ($response) {
			return $this->filter_data($response->data);
		}

		return array();
	}

	private function filter_data($data) {
		$filtered = array();

		foreach ($data as $item) {
			$filtered[] = (object)array(
				'type'			=> $this->name,
				'created_time' 	=> $item->created_time,
				'link'			=> $item->link,
				'image'			=> $item->images->thumbnail->url,
				'title'			=> ($item->caption && $item->caption->text) ? $item->caption->text : '',
				'user'			=> $item->user->username
			);
		}

		return $filtered;
	}
}

?>