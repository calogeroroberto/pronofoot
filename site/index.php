<?php
// Tampon qui intercepte tous les outputs jusqu'à ob_end_flush
if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start('ob_gzhandler');
else ob_start();

session_start();

require_once('lib/constants.php');
error_reporting(E_ALL | E_STRICT);

// Définition du fuseau horaire
date_default_timezone_set(TIMEZONE);

require_once('lib/utils.php');
require_once('lib/mysql.php');
require_once('lib/joueur.php');

// connexion à la BDD
sql_connect();

// session de l'utilisateur
if(!isset($_SESSION['is_connect'])) {
	$_SESSION['is_connect'] = false;
}

if(!$_SESSION['is_connect']) {
	// connexion depuis le formulaire
	if(isset($_POST['submit-connection'])) {
		$login = clean_str($_POST['log_login']);
		$password = crypt_password($_POST['pass']);
	
		session_connect($login, $password);
	}
}
elseif(isset($_GET['deconnexion'])) {
	// demande de déconnexion
	if(isset($_SESSION['id'])) {
		$deco_id = intval($_SESSION['id']);
		joueur_set_offline($deco_id);
	}
	session_destroy();
	$_SESSION['is_connect'] = false;
}

// Determination de la page à afficher
$page = isset($_GET['p']) ? $_GET['p'] : 'accueil';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title><?php echo TITLE.' | '.ucfirst(strtolower($page)); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="author" content="Julien P." />
	<meta name="robots" content="index, follow" />
	<meta name="google-site-verification" content="3j_58lbwspsTkmMWHCMG-BANQyA09wPdHgWVId5rzcc" />

	<!-- css -->
	<link rel="stylesheet" media="screen" type="text/css" href="css/style.css" />
	
	<!-- website mini-icon -->
	<link rel="icon" href="images/icons/sport_soccer.png" type="image/png" />
	
	<!-- rss -->
	<link rel="alternate" type="application/rss+xml" href="/resources/rss.xml" title="Prono Foot RSS Feed" />
	
	<!-- js dev -->
	<script type="text/javascript" src="javascript/head.load.min.js"></script>
	<script type="text/javascript">
		head.js('javascript/jquery.min.js',
				'javascript/jquery-ui.min.js',
				'javascript/jquery.timers-1.2.min.js',
				'javascript/myjs.min.js');
	</script>
	
	<!-- js online
	<script type="text/javascript" src="javascript/head.load.min.js"></script>
	<script type="text/javascript">
		head.js('javascript/google-analytics.js',
				'https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
				'javascript/jquery.timers-1.2.min.js',
				'javascript/myjs.min.js');
	</script> -->
</head>
<body>
	<div id="title">
		<h1 class="ui-helper-hidden">pronofoot.julienp.fr</h1>
		<a href="/"><img src="images/ban-weblink.png" alt="pronofoot.julienp.fr" /></a>
	</div>
	<div id="global">
		<!-- head -->
		<div id="ban"></div>
		<div id="menu" class="ui-widget ui-widget-header">
			<input id="href" type="hidden" value="<?php echo get_pageval($_SERVER['REQUEST_URI']); ?>" />
			<?php include_once('includes/menu.php'); ?>
		</div>
		
		<!-- middle -->
		<div id="content">
			<?php
			$path = 'pages/'.$page.'.php';
			
			if(in_array($page, $authorized))
				require_once($path);
			elseif(in_array($page, $restricted) && (!$_SESSION['is_connect'] || !in_array($_SESSION['id'], $idadmins)))
				echo '<p class="error">Vous n\'êtes pas autorisé à consulter cette page</p>';				
			elseif(($_SESSION['is_connect'] && file_exists($path)))
				require_once($path);
			elseif(!file_exists($path))
				echo '<p class="error">La page demandée n\'existe pas</p>';
			else
				echo '<p class="error">Vous n\'êtes pas autorisé à consulter cette page</p>';
			?>
		</div>
		<div id="member" class="ui-dialog ui-widget ui-widget-content ui-corner-all">
			<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Membre</div>
			<div class="ui-dialog-content ui-widget-content">
				<?php include_once('includes/member.php'); ?>
			</div>
		</div>
		<div class="ui-dialog ui-widget ui-widget-content ui-corner-all" id="chatbox">
			<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" id="chatbox_title">Chatbox</div>
			<div class="ui-dialog-content ui-widget-content" id="chatbox_content">
				<?php include_once('includes/chatbox.php'); ?>
			</div>
		</div>
		<!-- <div id="pub" class="ui-dialog ui-widget ui-widget-content ui-corner-all">
			<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Publicité</div>
			<div id="adspace" class="center ui-dialog-content ui-widget-content">
				<script type="text/javascript" language="javascript" src="http://pub.oxado.com/insert_ad?pub=249856"></script>
				
			</div>
		</div>-->
		
		<!-- bottom -->
		<div id="footer" class="ui-widget-header">
			<?php include_once('includes/footer.php'); ?>
		</div>
	</div>
</body>
</html>
