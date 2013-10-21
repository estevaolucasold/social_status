<?php 

require_once('SocialNetwork.php');
require_once('sdks/twitter/TwitterAPIExchange.php');

class TwitterStatus extends SocialNetwork {
	public $name = 'twitter';

	public function __construct($options) {
		$this->options = $options;
		$this->instance = new TwitterAPIExchange($this->options['keys']);
	}

	public function get_status($limit = 10) {
		$response = $this->instance
			->setGetfield('?' . http_build_query(array(
				'screen_name' 	=> $this->options['user_id'], 
				'count' 		=> $limit
			)))
			->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
			->performRequest();

		return $this->filter_data(json_decode($response));
	}

	private function filter_data($data) {
		$filtered = array();

		foreach ($data as $item) {
			$filtered[] = array(
				'type'			=> $this->name,
				'created_time' 	=> strtotime($item->created_at),
				'link'			=> 'http://twitter.com/' . $item->user->screen_name . '/status/' . $item->id,
				'text'			=> $item->text,
				'user'			=> $item->user->screen_name
			);
		}

		return (object)$filtered;
	}
}

?>