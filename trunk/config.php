<?php

if(!defined("OVH_MAILLIST")) {
	echo "Access denied!";
	exit();
}

// Identifiant OVH (NIC-handle sous la forme XXXXX-ovh)
$config['nic'] = '';

// Mot de passe du compte OVH
$config['pass'] = '';

// Nom de domaine associe a la mail-list
$config['domain'] = '';

// Nom de la mail-list
$config['ml'] = '';

// URL du script
$config['url'] = 'http://www.votre-domaine.tld/ovh-mailing-list';

// Messages d'erreurs et de confirmations
$messages['homepage'] = '<form action="index.php" method="post" id="maillist">
<div>
<label for"email">Email</label>
<input type="text" name="email" id="email" size="30" /><br />
<input type="radio" name="action" id="action_sub" value="subscribe" checked="checked" />Inscription
<input type="radio" name="action" id="action_unsub" value="unsubscribe" />D&eacute;sinscription<br />
<input type="submit" value="Valider" />
</div>
</form>';

$messages['unvalid_email'] = '<span style="color: red">L\'email <strong>%s</strong> ne semble pas &ecirc;tre valide.</span>';

$messages['error_subscribe'] = '<span style="color: red">Une erreur est survenue lors de l\'inscription de l\'email <strong>%s</strong>.</span>';
$messages['confirm_subscribe'] = 'Votre demande d\'inscription a bien &eacute;t&eacute; enregistr&eacute;e pour l\'email <strong>%s</strong>.<br /><br />Vous allez recevoir un message de demande de confirmation afin de valider votre email.';

$messages['error_unsubscribe'] = '<span style="color: red">Une erreur est survenue lors de la d&eacute;sinscription de l\'email <strong>%s</strong>.</span>';
$messages['confirm_unsubscribe'] = 'Votre demande de d&eacute;sinscription a bien &eacute;t&eacute; enregistr&eacute;e pour l\'email <strong>%s</strong>.';

$messages['error_validate'] = '<span style="color: red">Une erreur est survenue lors de la confirmation de votre email. Merci de renouveller votre demande ou de contacter l\'administrateur du site.</span>';
$messages['confirm_validate'] = "Votre demande a bien &eacute;t&eacute; valid&eacute;e pour l'email <strong>%s</strong>.";

$confirm_email['sub_subject'] = 'Confirmez votre demande d\'inscription';
$confirm_email['sub_body'] = "Une inscription à la maillist a été demandée pour l'email %s.\n\n"
	."Pour confirmer cette inscription, merci de vous rendre à cette adresse :\n%s";

$confirm_email['unsub_subject'] = 'Confirmez votre demande de désinscription';
$confirm_email['unsub_body'] = "Une désinscription à la maillist a été demandée pour l'email %s.\n\n"
	."Pour confirmer cette désinscription, merci de vous rendre à cette adresse :\n%s";

?>