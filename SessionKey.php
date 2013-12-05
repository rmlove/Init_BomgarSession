<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us">
<head>
<?php	
	date_default_timezone_set('US/Central');
	
	$ticket = $_REQUEST['ticket'];  //get value for external_key 
	$repname = $_REQUEST['repname']; //get name of this rep (email addr) 

	//Bomgar Login Info...
	$bomgar_site = "https://example.bomgar.com";
	$bomgar_username = "User";
	$bomgar_password = "password";
	
		//  Post the necessary info to query Bomgar for the session details using the Bomgar Reporting API...
	$postData = array
	(	
		'username'			=>	$bomgar_username, //account  with admin access to the Bomgar portal
		'password'			=>	$bomgar_password, //password for this account
		'action'	=>	"generate_session_key", // generate a session key
		'type'		=>	"support",
		'queue_id'				=>	"rep_username:" . $repname, // Bomgar ID of the rep requesting the session key
		'external_key'		=> $ticket //ticket number
	);	

 

	// Use curl to invoke the Bomgar API to generate a session key...
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $bomgar_site . "/api/command.ns?");
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	// Assign the output from the URL to $output.
	$output = curl_exec($curl);

	// close curl resource, and free up system resources
	curl_close($curl);
	
	// Use DOM to parse the XML output of the reporting API to query the necessary information
	$dom = new DOMDocument();
	$dom->loadXML($output);
		
	if (!$dom)
	{
		// Handle the error
		return;
	}
		
	//Load returned XML for this session into the $xml varialble.
	$xml = simplexml_import_dom($dom);
	
	$keyExpires = date("D, M j, Y G:i:s T", intval($xml->expires));  //when this session key expires
	$mailsubj = rawurlencode("Bomgar Remote Support Invitation");
	$mailbody1 = $xml->mail_body;
	$short_key = $xml->short_key;  //read to the customer
 	$directLink = $xml->key_url;
	$mailbody1 = "Your representative would like to start a support session with you. ";
	$mailbody1 .= "To do so, please click on the link below and follow the online instructions.\n\n";
	$mailbody1 .= $directLink;
	$mailbody1 .= "\n\nBomgar enables a support representative to view your screen in "; 
	$mailbody1 .= "order to assist you. Session traffic is fully encrypted ";
	$mailbody1 .= "to protect your system's data. Once a session has ";
	$mailbody1 .= "begun, you will be able to end it at any time.\n\n";
	$mailbody1 .= "-- \nPowered by Bomgar \n http://www.bomgar.com/ ";
	$mailbody1  = rawurlencode($mailbody1);
	
	$mailtoLink = '<a href=mailto:?Subject=' . $mailsubj .  '&Body=' . $mailbody1 . '>Send this information in an Email' . '</a>' . '<br />';
	
?>	

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="<?php echo $bomgar_site . '/content/style.css" rel="stylesheet" type="text/css"'; ?> />
	<script type="text/javascript" src="<?php echo $bomgar_site . '/content/nsjs.js"'; ?> ></script>
	<script type="text/javascript" src="<?php echo $bomgar_site . '/content/appliance.js"'; ?> ></script>
	<script type="text/javascript" src="<?php echo $bomgar_site . '/content/form_functions.js"'; ?> ></script>
	<meta http-equiv="refresh" content="605" />
	<link rel="shortcut icon" href="<?php echo $bomgar_site . '/favicon.ico" type="image/x-icon"'; ?> />
	<meta name="Bomgar-Version" content="10.4.5"/>
	<title>Bomgar</title>
</head>
<body>
<div id="container" style="width: 700px;">

<div id="header" class="contentBox">
	<table class="wide"><tr>
		<td>
			<a href="http://www.bomgar.com" target="_blank">

				<img src=<?php echo $bomgar_site . "/content/bomgar177.jpg"; ?> alt="Bomgar" title="Remote Support Solutions from Bomgar" width="177" height="30" />
			</a>
		</td>
		<td align="right" style="white-space:nowrap">
			<div class="pageTitle">LOGIN</div>
			<div><img style="vertical-align:bottom" src=<?php echo $bomgar_site . "/content/globe.gif" ?> alt="Active Language"/> <span class="language_selection">
		English (US)
</span></div>

		</td>
	</tr></table>
</div>


<!--/header-->
<div class="contentBox">
	<h1>Session Key</h1>

	<p>
		A new Session Key has been generated for: <b><?php echo $repname . "</b> related to ticket number: <b>". $ticket?></b>

	</p>
	<p>
		It is set to expire on: <b><?php echo $keyExpires; ?></b>
	</p>
	<p>
		Session Key: <b><?php echo $short_key; ?></b>
	</p>

	<p>
		URL: <?php echo $directLink ?>
	</p>
	<p>
		<?php echo $mailtoLink; ?>
	</p>
</div>

<!--footer-->
<div id="footer" class="contentBox">
	Copyright &copy; 2002-2010 Bomgar Corporation. Redistribution Prohibited. All Rights Reserved.</div>

</div>


</body>
<!--/footer-->
</html>
