<?php
  $app_id = "555197614568021";
  $app_secret = "416dde5c28d61b452850bfc30a02df77"; 
  $my_url = "http://localhost/social_status/facebook.php";

  // known valid access token stored in a database 
  $access_token = "555197614568021|iFrWSHKnEEt0JKRUf4svTH_y2RM";

  $code = isset($code) ? $_GET["code"] : false;
    
  // If we get a code, it means that we have re-authed the user 
  //and can get a valid access_token. 
  if ($code) {
    $token_url="https://graph.facebook.com/oauth/access_token?client_id="
      . $app_id . "&redirect_uri=" . urlencode($my_url) 
      . "&client_secret=" . $app_secret 
      . "&code=" . $code . "&display=popup";
    $response = file_get_contents($token_url);
    $params = null;
    parse_str($response, $params);
    $access_token = $params['access_token'];
  }

        
  // Attempt to query the graph:
  $graph_url = "https://graph.facebook.com/me?"
    . "access_token=" . $access_token;
  $response = curl_get_file_contents($graph_url);
  $decoded_response = json_decode($response);
    
  // //Check for errors 
  // if ($decoded_response->error) {
  // // check to see if this is an oAuth error:
  //   if ($decoded_response->error->type== "OAuthException") {
  //     // Retrieving a valid access token. 
  //     $dialog_url= "https://www.facebook.com/dialog/oauth?"
  //       . "client_id=" . $app_id 
  //       . "&redirect_uri=" . urlencode($my_url);
  //     echo("<script> top.location.href='" . $dialog_url 
  //     . "'</script>");
  //   }
  //   else {
  //     echo "other error has happened";
  //   }
  // } 
  // else {
  // success

  if (!$decoded_response->error) {
    echo("success" . $decoded_response->name);
    echo($access_token);
  }
  // }

  // note this wrapper function exists in order to circumvent PHP’s 
  //strict obeying of HTTP error codes.  In this case, Facebook 
  //returns error code 400 which PHP obeys and wipes out 
  //the response.
  function curl_get_file_contents($URL) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
    curl_close($c);
    if ($contents) return $contents;
    else return FALSE;
  }
?>