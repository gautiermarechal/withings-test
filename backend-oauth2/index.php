<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$client_id = 'a16837aaa8f536b229ce20fa8e90a2739885b640ff67de7b84562b6fe0e27513';
$client_secret = '881b7dc5686e38894ef0cb27019ebc44e7daf72cc329fe914a43acee15774782';
$redirect_uri = 'http://localhost:7070';
$token_url = 'https://wbsapi.withings.net/v2/oauth2';
$is_loggedin = false;
$measurements = [];

function convertToKilograms($value, $unit) {
	return $value * pow(10, $unit);
}

function requestAccessToken($authorization_code) {
    global $token_url, $client_id, $client_secret, $redirect_uri;

    $response = file_get_contents($token_url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'action' => 'requesttoken',
                'grant_type' => 'authorization_code',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'code' => $authorization_code,
                'redirect_uri' => $redirect_uri,
            ]),
        ],
    ]));

    return json_decode($response, true);
}

function refreshAccessToken($refresh_token) {
    global $token_url, $client_id, $client_secret;

    $response = file_get_contents($token_url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'action' => 'requesttoken',
                'grant_type' => 'refresh_token',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
            ]),
        ],
    ]));

    return json_decode($response, true);
}

    

if (!isset($_COOKIE['access_token']) || !isset($_COOKIE['access_token_expires_at']) || time() > $_COOKIE['access_token_expires_at']) {
	if (isset($_GET['code'])) {
		$authorization_code = $_GET['code'];
		$token_data = requestAccessToken($authorization_code);
		if (isset($token_data['body']['access_token'])) {
			$access_token = $token_data['body']['access_token'];
			$refresh_token = $token_data['body']['refresh_token'];
			$expires_in = $token_data['body']['expires_in'];

			setcookie("access_token", $access_token, time() + $expires_in, "/");
			setcookie("refresh_token", $refresh_token, time() + (3600 * 24 * 30), "/");
			setcookie("access_token_expires_at", time() + $expires_in, time() + $expires_in, "/");

			$is_loggedin = true;
		} else {
			$is_loggedin = false;
			echo "Failed to retrieve access token.";
		}
	} else {
		$is_loggedin = false;
		echo "Authorization code not found.";
	}
	
	
} else {
	$access_token = $_COOKIE['access_token'];
	$refresh_token = $_COOKIE['refresh_token'];

	$is_loggedin = true;

}


if($is_loggedin) {
	$response = file_get_contents('https://wbsapi.withings.net/measure?meastype=1&action=getmeas&category=1&offset=0&lastupdate=0', false, stream_context_create([
		'http' => [
			'method' => 'GET',
			'header' => 'Authorization: Bearer ' . $access_token,
		],
	]));

	$measurements = json_decode($response, true)['body']['measuregrps'];

	$weights_with_dates = array();

	foreach ($measurements as $entry) {
		if (isset($entry['measures']) && is_array($entry['measures'])) {
			foreach ($entry['measures'] as $measure) {
				if ($measure['type'] == 1) {
					$weight_in_kg = convertToKilograms($measure['value'], $measure['unit']);
					$date = date("Y-m-d H:i:s", $entry['date']);
					$weights_with_dates[] = array('weight' => $weight_in_kg, 'date' => $date);
				}
			}
		}
	}

	
}
?>




<html>
	<head>
		<title>Withings Oauth2</title>
	</head>
	<body>
	<?php if ($is_loggedin): ?>
        <h1>Welcome, User!</h1>
        <p>Here is your data:</p>
		<?php if ($is_loggedin): ?>
			<?php foreach ($weights_with_dates as $item): ?>
            	<li><?php echo htmlspecialchars($item['weight']); ?> kg on <?php echo htmlspecialchars($item['date']); ?></li>
        	<?php endforeach; ?>
		<?php endif; ?>
    <?php else: ?>
		<h1>Sign in to retrieve your personal data</h1>
		<a href="https://account.withings.com/oauth2_user/authorize2?response_type=code&client_id=a16837aaa8f536b229ce20fa8e90a2739885b640ff67de7b84562b6fe0e27513&redirect_uri=http://localhost:7070&state=withings_test&scope=user.metrics&mode=demo" >Sign in</a>
    <?php endif; ?>
	</body>
</html>