<?php 

require_once('SocialNetwork.php');
require_once('sdks/youtube/youtube.lib.php');

class YouTubeStatus extends SocialNetwork {
	public static $name = 'youtube';

	public function __construct($options) {
		$this->options = $options;
		$this->instance = new Youtube(array('user' => $this->options['user_id']));

		parent::__construct($options);
	}

	public function get_data($limit = 10) {
		$data = $this->instance->apiCall(array('limit' => $limit));
		$response = $this->instance->getVideosFromData($data);

		return $this->filter_data($response);
	}

	private function filter_data($data) {
		$filtered = array();

		foreach ($data as $item) {
			$item 		= (object)$item;
			$author 	= $item->author['name'];
			$published	= new DateTime($item->published);

			$filtered[] = (object)array(
				'type'			=> $this::$name,
				'created_time' 	=> $published->getTimestamp(),
				'link'			=> $item->link,
				'text'			=> $item->title,
				'description'	=> $item->description,
				'image'			=> $item->thumbnail,
				'views'			=> $item->views,
				'duration'		=> $item->duration,
				'video'			=> $item->video,
				'user'			=> (object)array(
					'username'		=> $author,
					'link'			=> 'https://youtube.com/' . $author
				)
			);
		}

		return $filtered;
	}
}

?>