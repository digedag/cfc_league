
Änderungen
==========

## v1.7.0 (??.04.2021)
 * BREAKING CHANGE: all SearchClasses support new querybuilder API. Update hooks on search classes.
 * tx_cfcleague_search_Builder moved to PSR-4

## v1.6.0 (23.11.2020)
 * change label for match status "invalid" to cancelled
 * #71 mark teams as "out of competition". That means results will not be scored in league table.
 * #72 allow to configure league table strategy in competition
 * some code refactoring

## v1.5.2 (31.08.2020)
 * Fix db issues with datetime columns
 * add constants for match status
 * add hook to competition search

## v1.5.1 (14.06.2020)
 * Language file moved to resource folder

## v1.5.0 (06.06.2020)
 * Support for TYPO3 10.4 LTS
 * Some bugfixes

## v1.4.2 (30.05.2020)
 * Refactoring classes to PSR-4
 * Folder icon fixed
 * Fixed some TCA issues

## v1.4.1 (02.05.2020)
 * Support for TYPO3 9.5 LTS
 * Support for TYPO3 6.2 LTS dropped
 * Show logo preview in team record
 * Some new fields in profiles useful for GDPR

## v1.3.0 (11.06.2019)
 * better support for TYPO3 8.7 LTS
 * some final modifications for TYPO3 6.2 in BE layout
 * many PHP warnings fixed

## v1.2.0 (11.07.2018)
 * Support for TYPO3 4.5 LTS dropped
 * Modifications for TYPO3 8.7
 * Many Bugfixes for TYPO3 7.6
 * Support for PHP 7.x
 * Code cleanup
 * digedag/cfc_league_fe#41 New feature: Match fixture sync with DFBnet
 * digedag/cfc_league_fe#25 Support for handball, but not yet finished
 * Many thanks to Mario Näther for contributions!

## v1.1.1 (04.01.2017)
 * composer.json added
 * Icons and language files moved to Resources folder

## v1.1.0 (23.12.2015)
 * Modifications for TYPO3 7.6
 * #23 Ticker-Module updated by Mario Näther
 * BE modules refactored

## v1.0.2 (03.02.2015)
 * Many bugfixes for TYPO3 6.2
 * Up to 60 players allowed as team member
 * Bugfix for match status in ticker form

## v1.0.1 (06.09.2014)
 * Team schedule view in BE for TYPO3 6.2 fixed

## v1.0.0 (26.04.2014)
 * Support for TYPO3 6.2
 * Team formations extensible by configuration
 * Changes to apply code conventions

## v0.9.1 (01.06.2013)
 * Support for TYPO3 6.x with FAL

## v0.9.0 (12.01.2013)
 * Refactoring in BE module
 * New method isSetBased in ISport
 * Displaying set results field in BE module

## v0.8.4 (08.12.2012)
 * #59: Make it possible to create matches for second half of saison only
 * new image fields for agegroups
 * models_Match: new method loadMatchNotes()
 * srv_Profile: new method loadProfiles()
 * Competition: Tournament field added but still not used

## v0.8.3 (14.01.2012)
 * Competition record: sports is now an update field

## v0.8.2 (21.12.2011) (not released)
 * New field in competition for sports selection
 * Avoid t3lib_SpriteManager for TYPO4 4.3 and older.
 * TCA config for point_system changed from radio to select, since radio can't handle itemsprocfunc.
 * tx_cfcleague_search_Match: ignore deleted and hidden competitions and teams

## v0.8.1 (17.10.2010)
 * New hook in merge profiles
 * #46: Remove profiles from team
 * #47: It is possible to calculate end result from results of match parts.
 * Some new fields for clubs
 * #19: New BE form to manually create matches
 * #19: New team mode to edit matches in BE
 * New video field if extension rgmediaimages is installed
 * Avoid deletion of profiles with references to other records
 * New BE module to manage clubs and stadiums

## v0.8.0 (21.10.2010)
 * BE module refactoring

## v0.7.7 (26.09.2010)
 * #39: Sort order of clubs can be changed to name

## v0.7.6 (16.09.2010)
 * New method getInstance in tx_cfcleague_models_Profile
 * Register new match notes with tx_cfcleague_util_Misc::registerMatchNote()

## v0.7.5 (13.09.2010) (not released)
 * Models and services extended for statistics integration
 * Requirement of lib/div removed

## v0.7.4 (03.09.2010)
 * Quick input field for liveticker in TYPO3 4.4 works again
 * TeamNotes for coaches and supporters possible
 * Bugfix tx_cfcleague_models_Competition::getGroup()

## v0.7.3 (17.07.2010)
 * BE modul CSS styles fixed for TYPO3 4.4
 * Matches: New field sets for set results

## v0.7.2 (04.07.2010)
 * Quick input field for liveticker in TYPO3 4.4 deactivated (JS problems)
 * Country fields for stadiums and clubs

## v0.7.1 (03.07.2010)
 * Model classes extended
 * Prev/Next-Buttons in Round-Selector
 * search_Teams: Relation to competition is possible
 * Clubs: New fields for geo coordinates

## v0.7.0 (17.05.2010)
 * Manage Teams: SQL error fixed

## v0.6.5 (11.05.2010) (not released)
 * All dependencies to tx_div removed
 * Clubs: Main sorting field for Selectbox is now city and then name. 
 * Team: Thumbnail for logo in TCA-Form
 * Dependency to DAM is optional now. But there are some features lost without DAM!

## v0.6.3 (02.01.2010)
 * #25: Quick input field for liveticker 

## v0.6.2 (22.11.2009)
 * #17: New relation stadiums to tt_address 

## v0.6.1 (08.11.2009)
 * #12 old sub-module for matchtable creation removed

## v0.6.0 (07.11.2009)
 * New class tx_cfcleague_util_Cache
 * New methods in tx_cfcleague_models_Club

## v0.5.6 (06.11.2009) (not released)
 * Multiple agegroups for competitions
 * Age group field for teams
 * New service class for clubs
 * Matchnote types can be extended without PHP code

## v0.5.5 (18.10.2009) (not released)
 * Performance-Issue in Match-Record solved. Unnecessary lookup of profiles removed.
 * Logo for competition added
 * BE-Modules: Create competition from BE-Module
 * BE-Modules: Module competition->teams extended. It is possible to add teams from current page to a competition.
 * BE-Modules: orderby in profile search fixed
 * Handling of logos changed. Club records can handle several logos. There is a new selectbox in team record to select one of these club logos.
 * BE-Modules: Module competition->match table rewritten. Opponents can be changed before match creation. Automatic lookup for keystrings.
 * BE-Modules: Module "Manage Team" rewritten.

## v0.5.4 (25.01.2009)
 * Bugfix: Mayday message occurred when new profiles were created

## v0.5.3 (06.01.2009)
 * Target page for new team notes is always taken from team
 * Missing include of tx_div fixed

## v0.5.2 (06.01.2009) (not released)
 * New field to set profile types
 * Profile merge supports team notes
 * New History-Button in profile search
 * New BE module to manage team notes
 * It is now possible to create new matches from BE modul
 * New datatype stadium with relations to club und games

## v0.5.1 (25.10.2008)
 * Compatible with TYPO3 4.2.2
 * New field shortname in group record

## v0.5.0 (07.09.2008)
 * New BE Layout for TYPO3 4.2. Many thanks to Thomas Maroschik!
 * New field shortname in group record

## v0.4.7 (24.08.2008)
 * Additional fields in club record like address, description or email

## v0.4.6 (31.07.2008)
 * BE module finished to join players to team
 * Profiles are sorted by lastname in backend 

## v0.4.5 (20.07.2008)
 * Small modification for t3sportsbet

## v0.4.4 (12.07.2008)
 * New submodul to create teams for a competition.
 * date2cal works now

## v0.4.3 (11.07.2008) (not released)
 * New structure for modules in backend. 
 * Datetime field in module match edit changed. Maybe this will solve some problems with wrong hours.
 * New field addinfo in match record. Can be used for extra info in matchtable.
 * Club selection in team record optionally changed from group type to select type


## v0.4.2 (06.07.2008)
 * New text fields in match record to setup lineups, substitutes and scorers as plain text.

## v0.4.1 (30.06.2008)
 * TCA setup for match with tab dividers now.
 * Team notes completed

## v0.4.0 (08.06.2008)
 * new TER release

## v0.3.4 (06.06.2008) (not released)
 * Minute field is automatically filled from running stop watch in liveticker view (BE)

## v0.3.3 (21.05.2008) (not released)
 * Simple stop watch in liveticker view (BE)
 * Bugfix: Profile search found deleted profiles

## v0.3.2 (17.05.2008) (not released)
 * Some minor changes

## v0.3.1 (07.05.2008) (not released)
 * New data types "Team note" and "Note type". With these types it is possible to add team relative data in a generic way. What does it mean? So you can add jersey number or position of a player in a specific team.

## v0.3.0 (02.05.2008) (not released)
 * Mostly internal changes. This project depends now on rn_base. Make sure to load the latest version!
 * New field liveticker author
 * Value for competition type other changed from 0 to 3. You must resave these competitions.

## v0.2.9 (30.03.2008) (not released)
 * Profile search remembers last search string
 * New field correction in competition penalty
 * New field for liveticker author in match
 * Key value for competition type "single matches" changed from 0 to 3. You should resave that competitions.

## v0.2.8 (23.03.2008)
 * Possible to change match status in ticker form
 * New field stage_name for profiles
 * Online help for profiles
 * flag for favorite club added
 * Some optical improvements in backend forms
 * Include dam_media_field in tca.php

## v0.2.7 (18.02.2008)
 * Neues Feld Obligation um Pflichtwettbewerbe zu markieren. Auswertung kann im Frontend interessant sein.
 * Spielplanerstellung ist nicht mehr so restriktiv. Es können auch zu bestehenden Wettbewerben weitere Spiele hinzugefügt werden.

## v0.2.6 (29.01.2008)
 * Vereinen kann Adresse zugeordnet werden (tt_address notwendig)
 * Neuer Spielstatus "verlegt"
 * In Personensuche gibt es nur noch ein Suchfeld. Es wird automatisch in Vor- und Nachname gesucht.
 * Profile können jetzt einfach zusammengeführt werden. Über die Personensuche können dazu zwei Spieler ausgewählt werden. Einer der beiden erhält dann alle Referenzen des anderen Spielers. Letzterer kann anschließend gelöscht werden.

## v0.2.5 (31.10.2007)
 * Die Anzahl der Eingabefelder im Tickermodul kann per TS im PageSetup festgelegt werden: tx_cfcleague.matchTickerCfg.numberOfInputFields = 4


## v0.2.4 (30.10.2007)
 * Bei Wettbewerbsstrafe kann in allen Werten jetzt auch eine negative Zahl angegeben werden

## v0.2.3 (20.10.2007)
 * Funktion "Spieler anlegen" ist jetzt voll TCA-kompatibel
 * In Funktion "Spieler anlegen" können jetzt auch Betreuer angelegt werden
 * Experimentell: bis zu 4 Spielabschnitte werden unterstützt (Eishockey, Basketball...)

## v0.2.2 (09.10.2007)
 * CSH für Wettbewerb erstellt.
 * Bugfix: Geburtstag kann jetzt wieder über Personensuche geändert werden

## v0.2.1 (29.09.2007)
 * Zuschauerzahl jetzt maximal 6-stellig

## v0.2.0 (27.09.2007)
 * Wettbewerbsstrafe zeigt nur noch Wettbewerbe der aktuellen Seite
 * Wettbewerbsstrafe wurde um zusätzliche Optionen erweitert
 * Zusätzliche Übersetzungen in der Personensuche
 * "Ligaerstellung" und "Spiele bearbeiten" berücksichtigen jetzt das Team "Spielfrei"
 * Dokumentation aktualisiert

## v0.1.5 (17.09.2007)
 * Layoutänderung im Ticker/Statistik-Modul. Die Bemerkungsbox steht jetzt unter den Auswahlboxen der Spieler
 * im Ticker/Statistik-Modul kann jetzt auch direkt das Ergebnis des Spiel geändert werden. Dies ist vor allem bei Betrieb eines Liveticker von Vorteil, da für die Ergebnisänderung der View nicht mehr gewechselt werden muss.
 * die Integration der Modulfunktionen wurde intern auf den TYPO3-Standard umgestellt. Dadurch können jetzt auch Module von anderen Extensions integriert werden.
 * In der Funktion "Spiele bearbeiten" wurde der Update der Ergebnisse intern auf den TYPO3-Standard umgestellt.
 * Es wurden einige neue Datenbankfelder integriert. Diese werden aber noch nicht genutzt!


## v0.1.4 (28.08.2007)
 * Der Teamdatensatz hat ein neues Feld zur Verlinkung auf die Teamseite. Dieses wird im FE verwendet, um
direkte Links auf die Teamseiten aus Spielplan und Ligatabelle zu ermöglichen oder zu verhindern.

## v0.1.3 (23.08.2007)
 * Im Spielticker können jetzt Nachspielzeiten gesondert eingegeben werden. Fällt zum Beispiel ein 
Tor in der 47. Minute der ersten Halbzeit, dann sollte man 45+2 im Ticker eingeben. Dadurch kann 
im Frontend die Tickermeldung korrekt ausgegeben werden. Der Anstoss zur zweiten Halbzeit in der 
46. Minute steht dann nach der Tormeldung.


## v0.1.2 (16.08.2007)
 * Bugfix: Team-Logo hatte fehlerhaften TCA Eintrag. Dadurch wurde das Team-Foto angezeigt.
