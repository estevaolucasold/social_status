<?php 

require_once('SocialStatus.php');

$social = new SocialStatus(array(
	'twitter' => array(
		'user_id'	=> 'estevao_lucas',
		'keys'		=> array(
			'oauth_access_token' 		=> '8487272-PZMH1m4vnB6lnPe6IQAESW0DTdNY6L2GGTCSiKLPY',
			'oauth_access_token_secret'	=> 'daswEbNxpUtoWSXxp9wgDZT1MTwQVhidbhpGoeE5wgE',
			'consumer_key'				=>	'nHmBrgVRxGrdw6OdOl3Cw',
			'consumer_secret'			=> 'tSnLIC2Ckur4FoQ4JUNrioargOE7DwWbYzKVWT02qY8'
		)
	),
	'facebook' => array(
		'keys' => array(
			'appId'  		=> '555197614568021',
			'secret' 		=> '416dde5c28d61b452850bfc30a02df77',
			'access_token'	=> '555197614568021|iFrWSHKnEEt0JKRUf4svTH_y2RM',
			'cookie' 		=> true
		)
	),
	'instagram' => array(
		'user_id'	=>	716240,
		'keys'		=> array(
			'apiKey'  		=> '3e9050add4294715a4b36ddf6cc8412e',
			'apiSecret' 	=> '5aede6734a2544ab82940a52c5c40d43',
			'apiCallback' 	=> 'http://localhost/social_status/teste.php',
			'code'			=> '2d21e8df950c47b9916b598aaf280806',
			'access_token'	=> '716240.3e9050a.fdc1258b216b4446ae94c7a8ef5e70d2'
		)
	)
));

print_r($social->twitter->get_status(1));

?>