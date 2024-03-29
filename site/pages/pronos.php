<h2>Tous les pronostics</h2>
<?php
require_once('lib/prono.php');
require_once('lib/journee.php');
require_once('lib/match.php');
require_once('lib/utils.php');

$journee = mysql_fetch_assoc(journee_get_current());
$matchs = match_get_by_journee($journee['id']);
$pronos = prono_get_by_journee($journee['id']);

if(mysql_num_rows($pronos) && time() > $journee['date']) {
	$nickname = ''; // Impossible d'utiliser $pseudo, cela créer un conflit avec la session en prod
	$count = 0;
	$nbmatchs = mysql_num_rows($matchs);
	while($prono = mysql_fetch_assoc($pronos)) {
		if($nickname == '') {
			echo '<h3>Tous les pronostics pour la '.display_number($journee['numero']).' journée</h3>
				  <p> Les équipes sont représentées par leurs initiales. Vous pouvez consulter la liste des équivalences en bas de page.</p>
				  <table class="table-contain"><thead class="ui-state-default"><tr><th></th>';
			while($match = mysql_fetch_assoc($matchs))
				echo '<th>'.team_shortname($match['equipe1']).'<br> - <br>'.team_shortname($match['equipe2']).'</th>';
			echo '</tr></thead><tbody><tr><td class="strong left">'.$prono['pseudo'].'</td><td>'.$prono['score'].'</td>';
			$nickname = $prono['pseudo'];
			++$count;
			continue;
		}
		if($prono['pseudo'] != $nickname) {
			if($count < $nbmatchs) {
				for($i = $count ; $i < $nbmatchs ; ++$i)
					echo '<td>X</td>';
			}
			echo '</tr><tr><td class="strong left">'.$prono['pseudo'].'</td><td>'.$prono['score'].'</td>';
			$count = 1;
		}
		else {
			echo '<td>'.$prono['score'].'</td>';
			++$count;
		}
		$nickname = $prono['pseudo'];
	}
	echo '</tr></tbody></table>';
	echo'<h3>Noms des équipes :</h3>
	<p><strong>ACA</strong> = Arles-Avignon; <strong>AJA</strong> = Auxerre; <strong>ASM</strong> = Monaco; <strong>ASNL</strong> = Nancy;
	<strong>ASSE</strong> = Saint-Etienne; <strong>FCGB</strong> = Bordeaux; <strong>FCL</strong> = Lorient; <strong>FCSM</strong> = Sochaux;
	<strong>LOSC</strong> = Lille; <strong>MHSC</strong> = Montpellier; <strong>OGCN</strong> = Nice; <strong>OL</strong> = Lyon;
	<strong>OM</strong> = Marseille; <strong>PSG</strong> = Paris; <strong>RCL</strong> = Lens; <strong>SB29</strong> = Brest;
	<strong>SMC</strong> = Caen; <strong>SRFC</strong> = Rennes; <strong>TFC</strong> = Toulouse; <strong>VAFC</strong> = Valenciennes</p>';
}
elseif(time() < $journee['date']) {
	echo '<span class="warning">Les pronostics de la prochaine journée ne sont pas encore consultables</span>';
}
else {
	echo '<span class="error">Il n\'y a pas encore de pronostics effectués pour la prochaine journée</span>';
}
