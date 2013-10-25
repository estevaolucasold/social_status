Social Status
=============

A simple PHP class to get the latest status update from Twitter and Instagram (Facebook soon), sorted by time. It's a wrapper using Twitter and Instagram's SKDs, with OAuth authentication.

Example:

	$status = new SocialStatus(array(
		'twitter' 		=> array(
			'user_id'	=> 'estevao_lucas',
			'keys'		=> array(
				'oauth_access_token' 		=> 'token',
				'oauth_access_token_secret'	=> 'token',
				'consumer_key'				=> 'token',
				'consumer_secret'			=> 'token'
			)
		),
		'instagram' => array(
			'user_id'	=>	'self',
			'keys'		=> array(
				// the same needs array param to Instagram class
				'apiKey'  		=> 'token',
				'apiSecret' 	=> 'token',
				'code'			=> 'token',
				'access_token'	=> 'token'
			)
		)
	);					
	
	
## Available methods


`get_all_status(<number>)`

`number` limit number that will be returned

    $status->get_all_status();
    
    
`<socialnetwork>->get_status(<number>)`

	$latest_tweet = $status->twitter->get_status(1);
	
`update_cache`

Useful to use with a cron job to update the network's cache file


## Credits

* [twitter-api-php](https://github.com/J7mbo/twitter-api-php.git)
* [Instagram PHP API](https://github.com/cosenary/Instagram-PHP-API.git)
