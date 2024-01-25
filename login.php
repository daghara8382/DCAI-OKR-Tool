<!DOCTYPE html>
<?php
include 'includes/config/_config.php';

if ($_SESSION['id'] != "") {
	if (!empty($redirect_url)) {
		$redirect_url1 = urldecode($redirect_url);
		header("location:$redirect_url1");
	} else {
		header("location:imet_home.php");
	}
}
else{
	if (!isset($_GET["code"]) and !isset($_GET["error"]) and $_SESSION['id']=='' ) {  //Real authentication part begins
		//First stage of the authentication process; This is just a simple redirect (first load of this page)
		echo "First stage of the authentication process";
		$url = "https://login.microsoftonline.com/" . TENANT_ID . "/oauth2/v2.0/authorize?";
		$url .= "state=" . session_id();  //This at least semi-random string is likely good enough as state identifier
		$url .= "&scope=profile+openid+email+offline_access+User.Read+User.Read.All";  //This scope seems to be enough, but you can try "&scope=profile+openid+email+offline_access+User.Read" if you like
		$url .= "&response_type=code";
		$url .= "&approval_prompt=auto";
		$url .= "&client_id=" . APP_CLIENT_ID;
		$url .= "&redirect_uri=" . urlencode(REDIRECT_URL);
		header("Location: " . $url);  //So off you go my dear browser and welcome back for round two after some redirects at Azure end
	} 
	elseif (isset($_GET["error"])) {  //Second load of this page begins, but hopefully we end up to the next elseif section...
		echo "Error handler activated:\n\n";
		var_dump($_GET);  //Debug print
		errorhandler(array("Description" => "Error received at the beginning of second stage.", "\$_GET[]" => $_GET, "\$_SESSION[]" => $_SESSION), $error_email);
	} 
	elseif (strcmp(session_id(), $_GET["state"]) == 0) {

		$code = $_GET['code'];
		#Generate the authentication token
		$ch_string = "curl -X POST -H 'Content-Type: application/x-www-form-urlencoded' -d 'client_id=" . APP_CLIENT_ID . "&code=".$code."&redirect_uri=".REDIRECT_URL."&scope=https%3A%2F%2Fgraph.microsoft.com%2F.default&client_secret=" . CLIENT_SECRET . "&grant_type=authorization_code' 'https://login.microsoftonline.com/" . TENANT_ID . "/oauth2/v2.0/token'";
		$output = shell_exec($ch_string);

		if ($output === false || array_key_exists("error",json_decode($output))) {
			print_r("Something went wrong. Please contact admin.");
			//print_r($output);
		} else {
			$authdata = json_decode($output, true);
			//print_r($authdata);
			//exit();
		}

		#Verify authentication token
		$ch_string2 = 'curl -X GET -H "Authorization: Bearer '.$authdata['access_token'].'" "https://graph.microsoft.com/v1.0/me"';
		//print_r($ch_string2);
		$output2 = shell_exec($ch_string2);
		if ($output2 === false || array_key_exists("error",json_decode($output2)) ) {
			print_r("Something went wrong. Please contact admin.");
			//print_r(json_decode($output2));  
		} else {
			$userdata = json_decode($output2, true);
			// print_r($userdata);  
			// exit();
			$wwid = $userdata['jobTitle'];
			setLoginwwid($wwid);
		}
	} else {
		print_r("Error");
	}
}

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Login</title>
</head>
<body>
	<span style='color:#0071c5;font-size:20px;'>Authentication in progress, please wait...</span>
</body>

</html>
