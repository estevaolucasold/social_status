<?php 

require_once('SocialNetwork.php');
require_once('sdks/twitter/TwitterAPIExchange.php');

class TwitterStatus extends SocialNetwork {
	public $name = 'twitter';

	public function __construct($options) {
		$this->options = $options;
		$this->instance = new TwitterAPIExchange($this->options['keys']);
	}

	public function get_data($limit = 10) {
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
			$item = $this->parse_message($item);
			
			$filtered[] = (object)array(
				'id'			=> $item->id,
				'type'			=> $this->name,
				'created_time' 	=> strtotime($item->created_at),
				'link'			=> 'http://twitter.com/' . $item->user->screen_name . '/status/' . $item->id_str,
				'text'			=> $item->text,
				'user'			=> (object)array(
					'username'		=> $item->user->screen_name,
					'fullname'		=> $item->user->name,
					'link'			=> 'http://twitter.com/' . $item->user->screen_name
				)
			);
		}

		return $filtered;
	}

	private function parse_message($tweet) {
		$replace_index = array();

		if (!empty($tweet->entities)) {
			foreach ($tweet->entities as $area => $items) {
				switch ($area) {
					case 'hashtags':
						$find = 'text';
						$prefix = '#';
						$url = 'https://twitter.com/search/?src=hash&q=%23';
						break;
					case 'user_mentions':
						$find = 'screen_name';
						$prefix = '@';
						$url = 'https://twitter.com/';
						break;
					case 'media': case 'urls':
						$find = 'display_url';
						$prefix = '';
						$url = '';
						break;
					default: break;
				}

				foreach ($items as $item) {
					$text = $tweet->text;
					$string = $item->$find;
					$href = $url . $string;
					
					if (!(strpos($href, 'http://') === 0)) {
						$href = "http://".$href;
					}

					$replace = substr($text, $item->indices[0], $item->indices[1]-$item->indices[0]);
					$with = "<a href=\"$href\">{$prefix}{$string}</a>";
					$replace_index[$replace] = $with;
				}
			}
			
			foreach ($replace_index as $replace => $with) {
				$tweet->text = str_replace($replace, $with, $tweet->text);
			}
		}

		return $tweet;
	}
}

?>