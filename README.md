# bludit-plugin-hitliste

Erzeugt eine Auswertung wie oft Beiträge eines Bludit CMS gelesen werden. Zudem werden auch fehlerhafte Zugriffe (404) oder Angriffsversuche protokolliert.

## Installation ##

Kopieren und entpacken Sie das ZIP-File in das Pluginverzeichnis des Bludit (`bl-plugin`). Im Backend aktivieren Sie bitte das Plugin und in den Einstellungen 
sehen Sie die summierten Zugriffe je Artikel des Blog als einfache Liste.

## Log-Format ##

Als Default ist `Y-m-d.log` als Dateiformat vorgegeben und kann in der de_DE.json angepasst werden.

>Aktuell werden alte Log-Files nicht automatisch gelöscht, dies kommt in einer späteren Version. Sie müssen daher in einem beliebigen Turnus
>alte Log-Files manuell aus `bl-content/workspace/cmswb-hitlist/...` löschen. Ein bequemes Setting im Backend fehlt aktuell noch, daher können die (nur)
>zwei Parameter in der `de_DE.json` manuell eingestellt werden.

https://www.cmsworkbench.de
