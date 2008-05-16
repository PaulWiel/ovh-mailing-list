<?php

// OVH-Maillist 1.0b by la Blonde (http://blog.lablonde.fr)
// Le present script est la pleine et entiere propriete intellectuelle de l'auteur. 

// Ce script permet de gerer les inscription et desinscription aux maillist d'OVH
// directement par les visiteurs par l'intermediaire d'un formulaire web.


define('OVH_MAILLIST', true);
include('./config.php');
include('./api-ovh.php');

$msg = $messages['homepage'];

if(isset($_REQUEST['email'])) {
	$email = $_REQUEST['email'];
	if (!$email || !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$", $email)) {
		$msg = sprintf($messages['unvalid_email'], $email);
		unset($email);
	}
}
if(isset($email) || isset($_GET['key'])) {
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	switch($action) {
		case 'subscribe':
			$handle = fopen('./temp_sub.php', 'r');
			$temp_file = fread($handle, filesize('./temp_sub.php'));
			fclose($handle);
			$handle = fopen('./temp_sub.php', 'w');
			$new_temp_file = str_replace(");", "'".md5($email)."' => '".$email."',\n);", $temp_file);
			@fwrite($handle, $new_temp_file);
			fclose($handle);

			$headers .= "From: ".$config['ml']." <".$config['ml']."-help@".$config['domain'].">\n";
			$headers .= "X-Mailer: PHP/".phpversion()."\n";

			if(mail($email, $confirm_email['sub_subject'], sprintf($confirm_email['sub_body'], $email, $config['url'].'/index.php?action=validate_sub&key='.md5($email)), $headers)) {
				$msg = sprintf($messages['confirm_subscribe'], $email);
			} else {
				$msg = sprintf($messages['error_subscribe'], $email);
			}
			break;
		case 'unsubscribe':
			$handle = fopen('./temp_unsub.php', 'r');
			$temp_file = fread($handle, filesize('./temp_unsub.php'));
			fclose($handle);
			$handle = fopen('./temp_unsub.php', 'w');
			$new_temp_file = str_replace(");", "'".md5($email)."' => '".$email."',\n);", $temp_file);
			@fwrite($handle, $new_temp_file);
			fclose($handle);

			$headers .= "From: ".$config['ml']." <".$config['ml']."-help@".$config['domain'].">\n";
			$headers .= "X-Mailer: PHP/".phpversion()."\n";

			if(mail($email, $confirm_email['unsub_subject'], sprintf($confirm_email['unsub_body'], $email, $config['url'].'/index.php?action=validate_unsub&key='.md5($email)), $headers)) {
				$msg = sprintf($messages['confirm_unsubscribe'], $email);
			} else {
				$msg = sprintf($messages['error_unsubscribe'], $email);
			}
			break;
		case 'validate_sub':
			include('./temp_sub.php');
			$key = $_GET['key'];
			if(isset($temp[$key])) {
				$ssid = ovh('Login', array ('nic' => $config['nic'], 'password' => $config['pass'])); 
				$result = ovh('MailingListSub', $ssid['value'], array('domain' => $config['domain'], 'ml' => $config['ml'], 'email' => $temp[$key]));
				if($result) {
					$msg = sprintf($messages['confirm_validate'], $temp[$key]);
				} else {
					$msg = $messages['error_validate'];
					$msg .= ($ovh_status) ? '<br />'.$ovh_status : '';
				}
				ovh('Logout');
				
				$temp_file = file_get_contents('./temp_sub.php');
				$new_temp_file = str_replace("'".$key."' => '".$temp[$key]."',\n", "", $temp_file);
				$handle = fopen('./temp_sub.php', 'w');
				@fwrite($handle, $new_temp_file);
				fclose($handle);
			} else {
				$msg = $messages['error_validate'];
			}
			break;
		case 'validate_unsub':
			include('./temp_unsub.php');
			$key = $_GET['key'];
			if(isset($temp[$key])) {
				$ssid = ovh('Login', array ('nic' => $config['nic'], 'password' => $config['pass'])); 
				$result = ovh('MailingListUnsub', $ssid['value'], array('domain' => $config['domain'], 'ml' => $config['ml'], 'email' => $temp[$key]));
				if($result) {
					$msg = sprintf($messages['confirm_validate'], $temp[$key]);
				} else {
					$msg = $messages['error_validate'];
					$msg .= ($ovh_status) ? '<br />'.$ovh_status : '';
				}
				ovh('Logout');
				
				$temp_file = file_get_contents('./temp_unsub.php');
				$new_temp_file = str_replace("'".$key."' => '".$temp[$key]."',\n", "", $temp_file);
				$handle = fopen('./temp_unsub.php', 'w');
				@fwrite($handle, $new_temp_file);
				fclose($handle);
			} else {
				$msg = $messages['error_validate'];
			}
			break;
		default:
			die('Access denied!');
	}
}

include('confirmation.php');

?>