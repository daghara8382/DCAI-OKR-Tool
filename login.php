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
