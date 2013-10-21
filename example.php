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
	)
));

print_r($social->twitter->get_latest_one());

?>