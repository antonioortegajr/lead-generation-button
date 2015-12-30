<html>
<head>
</head>
  <body>
<?php
//get api key from url
$APIKey = $_GET["apikey"];
//check it's 22 characters
$check = strlen($APIKey);

if ($check == 22){

//get random fake name and email from Faker endpoint
$ch = curl_init('http://faker.hook.io?property=name.findName');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$name = curl_exec($ch);
curl_close($ch);
//get rid of quotation marks
$name = str_replace('"', '', $name);
$names = explode(" ", $name);
$firstname = $names[0];
$lastname = $names[1];


/* using cURL again. normally since we are using this three times you would want
   a create a function, but I am just showing each step here. So I an not following
   good coding practices.
  */
$ch = curl_init('http://faker.hook.io?property=internet.email');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$email = curl_exec($ch);
$email = str_replace('"', '', $email);
curl_close($ch);


// PUT lead in to IDX Broker
$url = 'https://api.idxbroker.com/leads/lead';
$data = array(
	'firstName'=>$firstname,
	'lastName'=>$lastname,
	'email'=>$email
);
$data = http_build_query($data); // encode and & delineate
$method = 'PUT';

// headers (required and optional)
$headers = array(
	'Content-Type: application/x-www-form-urlencoded', // required
	'accesskey: '.$APIKey, // required - replace with your own
	'outputtype: json' // optional - overrides the preferences in our API control page
);

// set up even more cURL
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $url);
curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

if ($method != 'GET')
	curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
// send the data
curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

// exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
$response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if ($code >= 200 || $code < 300){
	$response = json_decode($response,true);
//Show the succcessful addition
echo $code.' Lead '.$email.' was added to your IDX Broker account.<br><br>
<img src="assets/leads.gif">';
  }
else{
	$error = $code;
  echo $code.' Lead Not Added';

  }

}
else{
echo '<form action="">
      <h1>Generate Lead</h1>
      <input type="text" name="apikey" placeholder="API Key"><br/><br/>
      <button type="submit">
      <img src="assets/push.jpg" />
      </button>
    </form>';

}
 ?>
  </body>
</html>
