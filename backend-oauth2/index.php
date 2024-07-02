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

if (isset($_GET['code'])) {
    $authorization_code = $_GET['code'];
    
    echo "Authorization code: " . htmlspecialchars($authorization_code) . "<br>";

    if (!isset($_COOKIE['access_token']) || !isset($_COOKIE['access_token_expires_at']) || time() > $_COOKIE['access_token_expires_at']) {
        $token_data = requestAccessToken($authorization_code);
		echo "Token data: " . json_encode($token_data) . "<br>";
        if (isset($token_data['body']['access_token'])) {
            $access_token = $token_data['body']['access_token'];
            $refresh_token = $token_data['body']['refresh_token'];
            $expires_in = $token_data['body']['expires_in'];

            echo "Access token: " . $access_token . "<br>";
            echo "Refresh token: " . $refresh_token . "<br>";

            setcookie("access_token", $access_token, time() + $expires_in, "/");
            setcookie("refresh_token", $refresh_token, time() + (3600 * 24 * 30), "/");
            setcookie("access_token_expires_at", time() + $expires_in, time() + $expires_in, "/");

			$is_loggedin = true;
        } else {
			$is_loggedin = false;
            echo "Failed to retrieve access token.";
        }
    } else {
        $access_token = $_COOKIE['access_token'];
        $refresh_token = $_COOKIE['refresh_token'];

		$is_loggedin = true;

        echo "Access token: " . $access_token . "<br>";
        echo "Refresh token: " . $refresh_token . "<br>";

    }
} else {
	$is_loggedin = false;
    echo "Authorization code not found.";
}

if($is_loggedin) {
	$measurements = file_get_contents('https://wbsapi.withings.net/measure?action=getmeas&category=1&offset=0&limit=1&lastupdate=0', false, stream_context_create([
		'http' => [
			'method' => 'GET',
			'header' => 'Authorization: Bearer ' . $access_token,
		],
	]));

	echo "Response: " . $response . "<br>";
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
		<?php echo htmlspecialchars($measurements); ?>	
		<?php endif; ?>
    <?php else: ?>
		<h1>Sign in to retrieve your personal data</h1>
		<a href="https://account.withings.com/oauth2_user/authorize2?response_type=code&client_id=a16837aaa8f536b229ce20fa8e90a2739885b640ff67de7b84562b6fe0e27513&redirect_uri=http://localhost:7070&state=withings_test&scope=user.metrics&mode=demo" >Sign in</a>
    <?php endif; ?>
	</body>
</html>