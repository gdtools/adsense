<!DOCTYPE html>
<html>
<head>
	<title>adsense查询</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">  
	<script src="https://cdn.staticfile.org/jquery/2.2.4/jquery.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
            

<?php
// https://github.com/googleads/googleads-adsense-examples/tree/master/php-clientlib-2.x/v2.x
//https://developers.google.com/adsense/management/v1.4/reference#Accounts.reports
// vendor/google/apiclient-services/src/Google/Service/AdSense.php
require_once 'templates/base.php';
session_start();
require_once __DIR__ . '/vendor/autoload.php';

// Autoload example classes.
spl_autoload_register(function ($class_name) {
  include 'examples/' . $class_name . '.php';
});

// Max results per page.
define('MAX_LIST_PAGE_SIZE', 50);
define('MAX_REPORT_PAGE_SIZE', 50);

// Configure token storage on disk.
// If you want to store refresh tokens in a local disk file, set this to true.
define('STORE_ON_DISK', false);
define('TOKEN_FILENAME', 'tokens.dat');

// Set up authentication.
$client = new Google_Client();
$client->addScope('https://www.googleapis.com/auth/adsense.readonly');
$client->setAccessType('offline');

// Be sure to replace the contents of client_secrets.json with your developer
// credentials.
$client->setAuthConfig('client_secrets999.json');

// Create service.
$service = new Google_Service_AdSense($client);

// If we're logging out we just need to clear our local access token.
// Note that this only logs you out of the session. If STORE_ON_DISK is
// enabled and you want to remove stored data, delete the file.
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

// If we have a code back from the OAuth 2.0 flow, we need to exchange that
// with the authenticate() function. We store the resultant access token
// bundle in the session (and disk, if enabled), and redirect to this page.
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  // Note that "getAccessToken" actually retrieves both the access and refresh
  // tokens, assuming both are available.
  $_SESSION['access_token'] = $client->getAccessToken();
  if (STORE_ON_DISK) {
    file_put_contents(TOKEN_FILENAME, $_SESSION['access_token']);
  }
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
  exit;
}

// If we have an access token, we can make requests, else we generate an
// authentication URL.
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else if (STORE_ON_DISK && file_exists(TOKEN_FILENAME) &&
      filesize(TOKEN_FILENAME) > 0) {
  // Note that "setAccessToken" actually sets both the access and refresh token,
  // assuming both were saved.
  $client->setAccessToken(file_get_contents(TOKEN_FILENAME));
  $_SESSION['access_token'] = $client->getAccessToken();
} else {
  // If we're doing disk storage, generate a URL that forces user approval.
  // This is the only way to guarantee we get back a refresh token.
  if (STORE_ON_DISK) {
    $client->setApprovalPrompt('force');
  }
  $authUrl = $client->createAuthUrl();
}

echo pageHeader('AdSense 概览');

echo '<div><div class="request">';
if (isset($authUrl)) {
  echo '<a class="login" href="' . $authUrl . '">Connect Me!</a>';
} else {
  echo '<a class="logout" href="?logout">Logout</a>';
};
echo '</div>';

if ($client->getAccessToken()) {

  // Now we're signed in, we can make our requests.
  makeRequests($service);
  // Note that we re-store the access_token bundle, just in case anything
  // changed during the request - the main thing that might happen here is the
  // access token itself is refreshed if the application has offline access.
  $_SESSION['access_token'] = $client->getAccessToken();

}

echo '</div>';
echo pageFooter(__FILE__);


// Makes all the API requests.
function makeRequests($service) {
  print "\n";
  $accounts = GetAllAccounts::run($service, MAX_LIST_PAGE_SIZE);

  if (isset($accounts) && !empty($accounts)) {
    // Get an example account ID, so we can run the following sample.
    $exampleAccountId = $accounts[0]['id'];
    // GetAccountTree::run($service, $exampleAccountId);
    $adClients = GetAllAdClients::run($service, $exampleAccountId, MAX_LIST_PAGE_SIZE);

    // if (isset($adClients) && !empty($adClients)) {
    //   // Get an ad client ID, so we can run the rest of the samples.
    //   $exampleAdClient = end($adClients);
    //   $exampleAdClientId = $exampleAdClient['id'];

    //   $adUnits = GetAllAdUnits::run($service, $exampleAccountId,
    //       $exampleAdClientId, MAX_LIST_PAGE_SIZE);
    //   if (isset($adUnits) && !empty($adUnits)) {
    //     // Get an example ad unit ID, so we can run the following sample.
    //     $exampleAdUnitId = $adUnits[0]['id'];
    //     GetAllCustomChannelsForAdUnit::run($service, $exampleAccountId,
    //       $exampleAdClientId, $exampleAdUnitId, MAX_LIST_PAGE_SIZE);
    //   } else {
    //     print 'No ad units found, unable to run dependant example.';
    //   }

    //   $customChannels = GetAllCustomChannels::run($service, $exampleAccountId,
    //       $exampleAdClientId, MAX_LIST_PAGE_SIZE);
    //   if (isset($customChannels) && !empty($customChannels)) {
    //     // Get an example ad unit ID, so we can run the following sample.
    //     $exampleCustomChannelId = $customChannels[0]['id'];
    //     GetAllAdUnitsForCustomChannel::run($service, $exampleAccountId,
    //       $exampleAdClientId, $exampleCustomChannelId, MAX_LIST_PAGE_SIZE);
    //   } else {
    //     print 'No custom channels found, unable to run dependant example.';
    //   }

    //   GetAllUrlChannels::run($service, $exampleAccountId, $exampleAdClientId,
    //       MAX_LIST_PAGE_SIZE);
    //   GenerateReport::run($service, $exampleAccountId, $exampleAdClientId);
    //   GenerateReportWithPaging::run($service, $exampleAccountId,
    //       $exampleAdClientId, MAX_REPORT_PAGE_SIZE);
    //   FillMissingDatesInReport::run($service, $exampleAccountId,
    //       $exampleAdClientId);
    //   CollateReportData::run($service, $exampleAccountId, $exampleAdClientId);
    // } else {
    //   print 'No ad clients found, unable to run dependant examples.';
    // }

    $savedReports = GetAllSavedReports::run($service, $exampleAccountId,
        MAX_LIST_PAGE_SIZE);
    if (isset($savedReports) && !empty($savedReports)) {
      // Get an example saved report ID, so we can run the following sample.
    //   $exampleSavedReportId = $savedReports[0]['id'];
    //   GenerateSavedReport::run($service, $exampleAccountId,
    //       $exampleSavedReportId);
          for ($i = 0; $i < count($savedReports); ++$i) {
          GenerateSavedReport::run($service, $exampleAccountId,  $savedReports[$i]['id']);
            }  
    } else {
      print '最好保存一个.';
    }
    
    for ($i = 0; $i < count($adClients); ++$i) {
         GenerateReport::run($service, $exampleAccountId, $adClients[$i]["id"]);
    }

    
    //GetAllSavedAdStyles::run($service, $exampleAccountId, MAX_LIST_PAGE_SIZE);
    // GetAllAlerts::run($service, $exampleAccountId);
  } else {
    'No accounts found, unable to run dependant examples.';
  }

//   GetAllDimensions::run($service);
//   GetAllMetrics::run($service);
}
?>

		</div>
	</div>
</div>

</body>

</html>
