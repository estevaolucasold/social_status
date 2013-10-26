<?php 

require_once('SocialNetwork.php');
require_once('sdks/facebook/src/facebook.php');

class FacebookStatus extends SocialNetwork {
	public static $name = 'facebook';

	public function __construct($options) {
		$this->options = $options;
		$this->instance = new Facebook($this->options['keys']);

		$this->extendedAccessToken();

		$this->permissions = array(
			'scope' => 'read_stream, offline_access'
		);

		parent::__construct($options);
	}

	public function get_data($limit = 10) {
		 try {
			$user_profile = $this->instance->api('/me','GET');
			$user = $this->instance->getUser();

			if ($user) {
				try {
					$user = $this->instance->api('/' . $this->options['user_id']);
					$response = $this->instance->api('/' . $this->options['user_id'] . '/feed?fields=message,id,name,link&limit=' . $limit);

					return $this->filter_data($response['data'], $user);

				} catch (FacebookApiException $e) {
					error_log($e);
				}
			} else {
				$loginUrl = $this->instance->getLoginUrl($this->permissions);
				echo "<script>top.location.href = '" . $loginUrl . "';</script>";
			}      
		} catch (Exception $e) {
			$loginUrl = $this->instance->getLoginUrl($this->permissions);
			echo "<script>top.location.href = '" . $loginUrl . "';</script>";
		}
	}

	private function filter_data($data, $author) {
		$filtered = array();
		$author = (object)$author;

		foreach ($data as $item) {
			$item = (object)$item;
			$published	= new DateTime($item->created_time);

			if (!property_exists($item, 'message') && !property_exists($item, 'name')) {
				continue;
			}

			$filtered[] = (object)array(
				'id'			=> $item->id,
				'type'			=> $this::$name,
				'created_time' 	=> $published->getTimestamp(),
				'link'			=> $item->link,
				'text'			=> property_exists($item, 'message') ? $item->message : $item->name,
				'user'			=> (object)array(
					'username' 	=> $author->username,
					'link'		=> $author->link,	
					'name'		=> $author->name
				)
			);
		}

		return $filtered;
	}

	private function extendedAccessToken() {
		$access_token_file = __DIR__ . '/faceboo_access_token_cache.json';
		$session_key = 'fb_' . $this->options['keys']['appId'] . '_access_token';

		if (file_exists($access_token_file)) {
			$access_token = file_get_contents($access_token_file);
		} else if (isset($_SESSION) && isset($_SESSION[$session_key])) {
			$access_token = $_SESSION[$session_key];
			file_put_contents($access_token_file, $access_token);
		}

		if ($access_token) {
			$this->instance->setExtendedAccessToken();
			$this->instance->setAccessToken($access_token);
			$accessToken = $this->instance->getAccessToken();
		}	
	}
}

?>