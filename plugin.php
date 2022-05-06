<?php
// Das Plugin erzeugt Log-Files zur Beitragsstatistik im bl-content/workspace/cmswb-hitliste/...log. Im Backend unter
// der Plugin-Einstellung erscheint die kumulierte Auswertung der Zugriffe.

class pluginHitliste extends Plugin {
  
    //----------------------------------------------------------------------------------------------------------------//
    // Zentraler Einstieg über den HOOK pageEnd(). Bei Bedarf kann der Counter pro Beitrag am Ende des Content
  	// eingeblendet werden, das Setting dazu in der de_DE.json vornehmen.
    //----------------------------------------------------------------------------------------------------------------//
  	public function pageEnd(){
		//global $page;
      	global $L;
      	$this->addVisitor();
      	if ($L->get('display-in-post')) {
			$this->getVisitors(Date::current($L->get('log-file-format')));
        }
    }

    //----------------------------------------------------------------------------------------------------------------//
    // Die Standradbutton Speichern und Abbrechen ausblenden                                                          //
    //----------------------------------------------------------------------------------------------------------------//
    public function init() {
		$this->formButtons = false;
	}
  
    //----------------------------------------------------------------------------------------------------------------//
    // Einen neuen Eintrag im Log-File vornehmen. Das Log-File-Format entscheidet letztendlich darüber, in welchem
  	// Turnus und welcher Häufigkeit das Log-File erzeugt wird. Das Setting in der de_DE.json vornehmen: 
  	//
  	//      Y-m     => ein neues Logfile pro Monat und Jahr z.B.              => 2022-05.log        wenn wenig Traffic
  	//      Y-m-d   => ein neues Logfile pro Tag, Monat und Jahr z.B.         => 2022-05-13.log     *empfohlen
  	//		Y-m-d-H => ein neues Logfile pro Stunde, Tag, Monat und Jahr z.B. => 2022-05-13-16.log  wenn viel Traffic
    //----------------------------------------------------------------------------------------------------------------//
  	public function addVisitor() {
      	global $page;
      	global $L;

        $currentTime = Date::current('Y-m-d H:i:s');
		$ip = TCP::getIP();
        if($page->key()) {
		    $line = json_encode(array($page->key(), $ip, $currentTime, $_SERVER['HTTP_REFERER']));
        } else {
            $line = json_encode(array('<strong>404 &#187; '.$_SERVER['REQUEST_URI'].'</strong>', $ip, $currentTime, $_SERVER['HTTP_REFERER']));
        }
		$currentDate = Date::current($L->get('log-file-format'));
		$logFile = $this->workspace().$currentDate.'.log';
		return file_put_contents($logFile, $line.PHP_EOL, FILE_APPEND | LOCK_EX)!==false;
	}
  
    //----------------------------------------------------------------------------------------------------------------//
    // Im Backend in den Plugin-Einstellungen Statistik zeigen                                                        //
    //----------------------------------------------------------------------------------------------------------------//
  	public function form() {
      	global $L;
      
      	$sum_count = 0;
      	$date = Date::current($L->get('log-file-format'));
		$file = $this->workspace().$date.'.log';
		$lines = @file($file);
		if (empty($lines)) {
			return 'Aktuell liegen noch keine Daten vor.';
		}
      
      	// Ranking berechnen es entsteht: [beitrags-slug-a]=> 3, [beitrags-slug-b]=> 9,...
      	$tmp = array();
        $dict = array();
		foreach ($lines as $line) {
			$data = json_decode($line);
          	$tmp[$data[0]] = $tmp[$data[0]]+1; // Dictionary
		}

        // Ranking anzeigen
      	$pos = 0;
      	$html = '<table cellpadding=4>';
      	$html .= '<tr><th>Nr.</th><th>Hits</th><th>Beitrag Slug</th><th>Aktion</th></tr>';
      	foreach ($tmp as $value=>$key) {
          	$pos = $pos+1;
            $html .= '<tr>';
          	$html .= '<td class="text-muted">'.$pos.'</td>';
          	$html .= '<td>'.$key.'</td>';
          	$html .= '<td>'.$value.'</td>';
          	$html .= '<td><a href="'.Theme::adminUrl().'edit-content/'.$value.'">edit</a></td>';
          	$html .= '</tr>';
            $sum_count = $key + $sum_count;
        }
      	$html .= '</table><br><br>';
        $html  = '<p class="lead my-3">'.$sum_count.' Beitragszugriffe in Datei: <code>'.Date::current($L->get('log-file-format')).'.log</code> gespeichert</p>'.$html; // oben anzeigen statt unten
		return $html;
	}

    //----------------------------------------------------------------------------------------------------------------//
    // Anzeigen wie oft der aktuelle Beitrag, den der User am Monitor gerade betrachtet, gelesen wurde.               //
    //----------------------------------------------------------------------------------------------------------------//
  	public function getVisitors($date) {
      	$count = 0;
      	global $page;

		$file = $this->workspace().$date.'.log';
		$lines = @file($file);
		if (empty($lines)) {
			return 0;
		}

      	// Count des gerade offenen Beitrags anzeigen
		foreach ($lines as $line) {
			$data = json_decode($line);
			if ($data[0] == $page->key()) {
            	$count = $count+1;
            }
		}
		echo '<p>Gelesen: '.$count.'</p>';
	}
  
}

?>