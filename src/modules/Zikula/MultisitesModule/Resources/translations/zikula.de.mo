��    �      \  �   �
      (  t   )     �     �     �  
   �  	   �     �     �     �          	          ,     <     S  
   e     p     �  �   �  �   .  �   �  B   O  .   �     �  '   �     �     �          (     0     @     \     k     w     �     �     �     �     �     �     �     �                     +  !   =     _     k  2   �  -   �     �  K   �     8  	   E     O     ]  -  c  �   �  �  1  \        h     x  M   �  c   �     ?     H     O  �   W     U     q  
   �  6   �     �     �      �     �          &  
   =     H     Y     e     n     {     �     �     �     �     �     �  "   �     �                0     >     P  <   Y  B   �     �     �     �     
     #     @     S     Z  -   f  
   �  '   �  '   �     �     �        7        F  	   W     a  #   i     �     �     �     �     �     �  
   �     �     �       	     	   %  	   /  
   9     D  
   J  
   U     `  	   i     s     y     �     �  	   �     �  F   �  �   �  6   �   *   �   F   !  L   U!  �   �!  2   >"  2   q"     �"     �"  K   �"     #  I   #     f#     n#  #   �#  $   �#     �#     �#     �#     �#  ,   $  �   -%     �%     �%     �%     �%     &     &     #&     7&     <&     D&     [&     l&     �&     �&     �&     �&     �&  �   �&  �   �'  �   [(  _   )  A   v)     �)  1   �)     �)     �)     *     **     1*      E*     f*     x*     �*     �*     �*     �*     �*     �*     �*     �*     +      +     2+     A+     P+  $   h+     �+     �+  6   �+  3   �+     ,  W   ),     �,     �,     �,     �,  l  �,  �   ,.  .  �.  q   1     y1     �1  \   �1  f   2     l2     u2     |2    �2     �3     �3     �3  <   �3     4     #4  %   04  %   V4     |4     �4  
   �4     �4     �4     �4     �4     �4     �4     5     5     %5     <5     U5  $   [5     �5     �5  '   �5     �5     �5     �5  N   �5  ]   I6     �6     �6  7   �6  .   �6  2   )7     \7     s7     {7  ;   �7  	   �7  '   �7  3   �7     *8     28     ;8  C   H8     �8     �8     �8  3   �8     �8  	   9     9     9     :9     R9     g9     t9  
   �9     �9     �9     �9  
   �9     �9     �9     �9     �9  	   :     :     :  
   :     ):     1:     @:     I:  ]   \:  �   �:  :   �;  5   �;  k   <  T   {<  �   �<  J   �=  J   �=     >     7>  U   O>     �>  V   �>     ?     ?  +   )?  +   U?     �?     �?     �?     �?     �   N       �          X   w                  "   6          �   (   f   I   4       h      b   �   g               ^                   =   {      d   5       �   �         #   �       n   A       ]   �      $           �   	   W       R   K   �      U   �   O       ,           H       T   �       *   F       }   �   .   �   e           G      +          �   Y       q   �   �   )   �   a   �              V                 �   D       �      
   1           J   |   @   �      �       !   <         ;   S   2   L   r   7          >       t       E   0       �              j   p   �       B   P   m   y   Z   v   :      8   �   _       l   s   �           u   -   �   c          ~   �           �   �       Q   \   �   o   �                  i   z   x   �   `   �   �   3      ?   C   �   '   [       �              9   k   %   &          �   �   M      /   �              A site template represents a blueprint for several sites. Each template may be assigned to all or specific projects. Actions Active Active, click to deactivate Add module Add theme Admin details Advanced options All Allowed Allowed extensions Allowed layouts Available tools Back to paginated view Back to site list Basic data Basic site data Cancel Caution: the database is emptied, so choose one which is not used by any other applications. All tables which are defined as excluded in the template are kept though. Caution: this reapplies the template data again to all assigned sites. All database tables except excluded ones will be dropped and recreated. Caution: updating a site causes reapplying the template data again to it. All database tables except excluded ones will be dropped and recreated. Change these values only if database credentials actually changed. Check this if the database does not exist yet. Choose action Clear all cache and compile directories Company Configuration wizard Confirmation prompt Content Create database Create global administrator Create project Create site Create template Creation date Database data Database hosts Database name Database types Default theme Delete Delete content permanently. Delete database Delete options Delete site Delete site files Derive placeholders from template Description Description (internal) Do you really want to delete this site: "%name%" ? Do you really want to reapply this template ? Download Each site is assigned to a project and instance of a certain site template. Edit project Edit site Edit template Email Enter parameter names separated by line breaks. Each parameter represents a variable information which is being replaced by concrete values when creating a new site or reapplying the template on existing sites. The parameter names can be used as placeholders anywhere in the template data accordingly. Enter the folders to be created for new sites separated by line breaks. If you need a folder within another one you can write expressions like "folder/folder". Enter the names of database tables which should be skipped during template reapplications separated by line breaks. With this you can for example avoid overriding your local user table. Note you can use * as a placeholder, like content_* for all Content tables for only * for all tables; ensure to use this if you want to use a template for different sites without any parameters, otherwise you will end up with overriding your data later on when the template is reapplied. Enter values for all parameters specified by the selected template separated by line breaks. Excluded tables Execute sql directly Expert option! Only possible if the database user has sufficient permissions. Expert option! Per default all locales available in the system will be made available for the site. Features Filter Folders If you decouple a site from the template it is not affected by future reapplications of this template anymore, but is configured and maintained independently. If you reassign a decoupled site to a template again this template is reapplied for that site. Inactive, click to activate Individualisation Input data It is going to be added in a later Multisites version. Logo Main domain Manage the modules for this site Manage the themes for this site Manage updates Management information Moderation Multiply queries My projects My sites My templates Name Name (internal) No No projects found. No sites found. No templates found. None None (decouple site from template) None (decoupled) Not allowed Not allowed, click to deactivate Not available Not default theme Not set. Note: the database user must exist already for this to work. Note: you should use semicolon as delimiter and UTF-8 as encoding. Operation mode Options Original site admin email Original site admin name Original site admin password Original site name Output Output data Parameter specification is not available yet. Parameters Placeholder syntax: ###PARAMETERNAME### Please see this issue for more details: Project Projects Projects list Projects serve for grouping sites by clients or topics. Quick navigation Real name Reapply Recover administrators site control Return control Save Send an email Set as default theme Show all entries Show output sql Site alias Site database Site dns Site host or folder Site list Site mode Site name Site tools Sites Sites list Sort by %s Sql file Sql query Start Submit Template Template data Templates Templates list The alias must be a lower case, unique string containing only letters. This ensures that the global administrator exists. Note that if the site admin and the global admin have the same user name, the global admin will override the original site admin. This feature has not been implemented yet (issue #17). This functionality is not implemented yet. This is an expert function! Use it only if you know about the effects. This is the domain or folder name under which this site should be reachable. This removes the first permission rule and inserts the default one instead, ensuring that the original site administrator belongs to the admin group again. This setting is disabled. Click here to enable it. This setting is enabled. Click here to disable it. Toggle module state Toggle theme state Use <strong>`###DBNAME###`</strong> as a placeholder for the database name. User User names can contain letters, numbers, underscores, periods, or dashes. Version View issue at GitHub View sites assigned to this project View sites assigned to this template Visit this site With selected sites Yes or sql file Project-Id-Version: 
POT-Creation-Date: 
PO-Revision-Date: 
Last-Translator: Axel Guckelsberger <info@guite.de>
Language-Team: 
Language: de
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 2.0.1
Plural-Forms: nplurals=2; plural=(n != 1);
 Eine Seitenvorlage repräsentiert eine Blaupause für mehrere Seiten. Jede Vorlage kann allen oder lediglich bestimmten Projekten zugeordnet werden. Aktionen Aktiv Aktiv, klicken zum Deaktivieren Modul hinzufügen Theme hinzufügen Admin-Details Erweiterte Optionen Alle Erlaubt Erlaubte Erweiterungen Erlaubte Layouts Verfügbare Werkzeuge Zurück zur Seitenansicht Zurück zur Seitenliste Basis-Daten Grundlegende Seitendaten Abbruch Achtung: die Datenbank wird geleert, also wählen Sie eine, welche nicht von anderen Anwendungen benutzt wird. Alle Tabellen, die in der Vorlage als ausgeschlossen definiert wurden, werden allerdings aufgehoben. Achtung: dies wendet die Daten der Vorlage auf alle zugeordneten Seiten neu an. Alle Datenbanktabellen außer den ausgeschlossenen werden gelöscht und neu angelegt. Achtung: das Ändern einer Seite verursacht eine Neuanwendung der Daten der Vorlage auf diese Seite. Alle Datenbanktabellen außer den ausgeschlossenen werden gelöscht und neu angelegt. Verändere diese Werte nur, falls sich die Datenbank-Zugangsdaten tatsächlich geändert haben. Kreuze diese Option an, falls die Datenbank noch nicht existiert. Aktion wählen Alle Cache- und Kompilierungsverzeichnisse leeren Firma Konfigurationsassistent Sicherheitsabfrage Inhalt Datenbank erstellen Globalen Administrator erstellen Projekt erstellen Seite erstellen Vorlage erstellen Erstellungsdatum Datenbank-Daten Datenbank-Server Datenbankname Datenbank-Typen Standardtheme Löschen Den Inhalt permanent löschen. Datenank löschen Löschoptionen Seite löschen Seiten-Dateien löschen Platzhalter von der Vorlage ableiten Beschreibung Beschreibung (intern) Soll diese Seite wirklich gelöscht werden: "%name%" ? Soll diese Vorlage wirklich neu angewendet werden ? Herunterladen Jede Seite ist einem Projekt zugeordnet und Ausprägung einer bestimmten Seitenvorlage. Projekt bearbeiten Seite bearbeiten Vorlage bearbeiten E-Mail Geben Sie Parameternamen getrennt durch Zeilenumbrüche ein. Jeder Parameter repräsentiert eine variable Information, die beim Erstellen einer neuen Seite oder dem erneuten Anwenden der Vorlage auf bestehende Seiten durch konkrete Werte ersetzt wird. Die Namen der Parameter können entsprechend als Platzhalter irgendwo in den Daten der Vorlage verwendet werden. Geben Sie Ordner an, die für neue Seiten erstellt werden sollen, getrennt durch Zeilenumbrüche. Auch Unterordner sind möglich durch Ausdrücke wie "ordner/unterordner". Geben Sie die Namen der Datenbanktabellen (getrennt durch Zeilenumbrüche) ein, die während dem Neuanwenden der Vorlage übersprungen werden sollen. Damit lässt sich zum Beispiel ein Überschreiben der lokalen Benutzertabelle vermeiden. Man kann übrigens * als Platzhalter verwenden, etwa content_* für alle Content-Tabellen oder nur * für alle Tabellen; dies sollte benutzt werden, wenn man eine Vorlage für unterschiedliche Seiten ohne jegliche Parameter nutzen möchte, ansonsten werden die Daten später beim Neuanwenden der Vorlage überschrieben. Gebe Werte für alle durch die ausgewählte Vorlage spezifizierten Parameter ein, getrennt durch Zeilenumbrüche. Ausgeschlossene Tabellen SQL direkt ausführen Expertenoption! Nur möglich, falls der Datenbanknutzer ausreichende Berechtigungen besitzt. Expertenoption! Per Standard sind alle im System verfügbaren Sprachen auch für die Seite verfügbar. Features Filter Ordner Wird eine Seite von der Vorlage entkoppelt, ist diese nicht mehr von zukünftigen Neuanwendungen dieser Vorlage betroffen, sondern wird unabhängig konfiguriert und gewartet. Wird eine entkoppelte Seite wieder einer Vorlage zugewiesen, wird diese Vorlage für die Seite neu angewendet. Inaktiv, klicken zum Aktivieren Individualisierung Eingabedaten Sie wird in einer späteren Multisites Version hinzugefügt. Logo Haupt-Domain Die Module für diese Seite verwalten Die Themes für diese Seite verwalten Updates verwalten Verwaltungsinformationen Moderation Abfragen vervielfachen Meine Projekte Meine Seiten Meine Vorlagen Name Interner Name Nein Keine Projekte gefunden. Keine Seiten gefunden. Keine Vorlagen gefunden. Keine Keine (Seite von Vorlage entkoppeln) Keine (entkoppelt) Nicht erlaubt Nicht erlaubt, klicken zum Deaktivieren Nicht verfügbar Standardtheme Nicht festgelegt. Hinweis: der Datenbanknutzer muss bereits existieren, damit dies funktioniert. Hinweis: als Trennzeichen sollte Semikolon und als Zeichensatz sollte UTF-8 verwendet werden. Ausführungsmodus Optionen Ursprüngliche E-Mail-Adresse des Seiten-Administrators Ursprünglicher Name des Seiten-Administrators Ursprüngliches Kennwort des Seiten-Administrators Originaler Seiten-Name Ausgabe Ausgabedaten Die Spezifikation von Parametern ist noch nicht verfügbar. Parameter Platzhalter-Syntax: ###PARAMETERNAME### Für weitere Details bitte dieses Ticket anschauen: Projekt Projekte Projektliste Projekte dienen der Gruppierung von Seiten nach Kunden oder Themen. Schnellnavigation Echter Name Neu anwenden Seitenkontrolle des Administrators wiederherstellen Rücksprungskontrolle Speichern Eine E-Mail senden Als Standardtheme festlegen Alle Einträge anzeigen Ausgabe-SQL anzeigen Seiten-Alias Seiten-Datenbank Seiten-DNS Seiten-Host oder -Ordner Seitenliste Seitenmodus Seitenname Seiten-Werkzeuge Seiten Seitenliste Sortieren nach %s SQL-Datei SQL-Abfrage Start Abschicken Vorlage Vorlagen-Daten Vorlagen Liste der Vorlagen Das Alias muss ein klein geschriebener, eindeutiger String sein, der nur Buchstaben enthält. Dies stellt sicher, dass der globale Administrator existiert. Zu beachten ist, dass, falls der Seiten-Admin und der globale Admin den gleichen Nutzernamen haben, der globale Admin den ursprünglichen Seiten-Admin überschreibt. Dieses Feature wurde noch nicht implementiert (Issue #17). Diese Funktionalität wurde noch nicht implementiert. Dies ist eine Expertenfunktion! Verwenden Sie sie nur, wenn Sie sich über die Auswirkungen im Klaren sind. Dies ist der Domain- oder Verzeichnisname, unter dem die Seite erreichbar sein soll. Dies entfernt die erste Berechtigungsregel und fügt die Standardregel statt dessen ein, was sicherstellt, dass der ursprüngliche Seiten-Administrator wieder zur Admin-Gruppe gehört. Diese Einstellung ist deaktiviert. Klicken Sie hier, um sie zu aktivieren. Diese Einstellung ist aktiviert. Klicken Sie hier, um sie zu deaktivieren. Modul-Status verändern Theme-Status verändern Benutzen Sie <strong>`###DBNAME###`</strong> als Platzhalter für den Datenbanknamen. Benutzer Benutzernamen können Buchstaben, Zahlen, Unterstriche, Punkte oder Striche enthalten. Version Ticket auf GitHub anschauen Diesem Projekt zugeordnete Seiten anschauen Dieser Vorlage zugeordnete Seiten anschauen Diese Seite besuchen Die ausgewählten Seiten Ja oder SQL-Datei 