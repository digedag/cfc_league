#
# Table structure for table 'tx_cfcleague_group'
#
CREATE TABLE tx_cfcleague_group (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	shortname varchar(55) DEFAULT '' NOT NULL,
	logo int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_cfcleague_saison'
#
CREATE TABLE tx_cfcleague_saison (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	halftime int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_cfcleague_competition'
#
CREATE TABLE tx_cfcleague_competition (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	tournament tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	short_name varchar(150) DEFAULT '' NOT NULL,
	internal_name varchar(255) DEFAULT '' NOT NULL,
	agegroup varchar(50) DEFAULT '' NOT NULL,
	saison int(11) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	obligation tinyint(4) DEFAULT '1' NOT NULL,
	teams text,
	point_system int(11) DEFAULT '0' NOT NULL,
	match_keys text,
	table_marks text,
	logo int(11) DEFAULT '0' NOT NULL,
	sports varchar(50) DEFAULT '' NOT NULL,
	tablestrategy varchar(50) DEFAULT '' NOT NULL,

	match_parts tinyint(4) DEFAULT '0' NOT NULL,
	addparts tinyint(4) DEFAULT '0' NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,
	extid varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_cfcleague_competition_penalty'
# Vergabe von Strafen für Teams eines Wettbewerbs
# Es können Punkte und Tore vergeben werden
# Es kann eine feste Platzierung festgelegt werden (Entzug der Lizenz)
#
CREATE TABLE tx_cfcleague_competition_penalty (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	competition int(11) DEFAULT '0' NOT NULL,
	team int(11) DEFAULT '0' NOT NULL,
	game int(11) DEFAULT '0' NOT NULL,
	comment text,
	matches int(11) DEFAULT '0' NOT NULL,
	wins int(11) DEFAULT '0' NOT NULL,
	draws int(11) DEFAULT '0' NOT NULL,
	loses int(11) DEFAULT '0' NOT NULL,
	goals_pos int(11) DEFAULT '0' NOT NULL,
	goals_neg int(11) DEFAULT '0' NOT NULL,
	points_pos int(11) DEFAULT '0' NOT NULL,
	points_neg int(11) DEFAULT '0' NOT NULL,
	static_position int(11) DEFAULT '0' NOT NULL,
	correction tinyint(4) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_cfcleague_club'
#
CREATE TABLE tx_cfcleague_club (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	name varchar(150) DEFAULT '' NOT NULL,
	short_name varchar(100) DEFAULT '' NOT NULL,
	logo int(11) DEFAULT '0' NOT NULL,
	address int(11) DEFAULT '0' NOT NULL,
	favorite tinyint(4) DEFAULT '0' NOT NULL,

	www varchar(200) DEFAULT '' NOT NULL,
	email varchar(200) DEFAULT '' NOT NULL,
	street varchar(200) DEFAULT '' NOT NULL,
	zip varchar(10) DEFAULT '' NOT NULL,
	city varchar(200) DEFAULT '' NOT NULL,
	lng tinytext,
	lat tinytext,
	country int(11) DEFAULT '0' NOT NULL,
	countrycode varchar(20) DEFAULT '' NOT NULL,
	shortinfo text,
	info text,
	info2 text,
	stadiums int(11) DEFAULT '0' NOT NULL,
	established date DEFAULT NULL,
	yearestablished int(11) DEFAULT '0' NOT NULL,
	colors varchar(100) DEFAULT '' NOT NULL,
	members int(11) DEFAULT '0' NOT NULL,
	extid varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_cfcleague_teams'
#
CREATE TABLE tx_cfcleague_teams (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	dummy tinyint(4) DEFAULT '0' NOT NULL,
	club int(11) DEFAULT '0' NOT NULL,
	name varchar(100) DEFAULT '' NOT NULL,
	short_name varchar(100) DEFAULT '' NOT NULL,
	tlc varchar(3) DEFAULT '' NOT NULL,
	agegroup int(11) DEFAULT '0' NOT NULL,
	coaches text,
	players text,
	supporters text,
	logo int(11) DEFAULT '0' NOT NULL,
	t3logo int(11) DEFAULT '0' NOT NULL,
	t3images int(11) DEFAULT '0' NOT NULL,

	comment text,
	players_comment text,
	coaches_comment text,
	supporters_comment text,
	link_report tinyint(4) DEFAULT '0' NOT NULL,
	extid varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_cfcleague_games'
#
CREATE TABLE tx_cfcleague_games (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	match_no varchar(5) DEFAULT '' NOT NULL,
	home int(11) DEFAULT '0' NOT NULL,
	guest int(11) DEFAULT '0' NOT NULL,
	sets varchar(254) DEFAULT '' NOT NULL,
	competition int(11) DEFAULT '0' NOT NULL,
	round int(11) DEFAULT '0' NOT NULL,
	round_name varchar(100) DEFAULT '' NOT NULL,
	addinfo varchar(254) DEFAULT '' NOT NULL,
	stadium varchar(200) DEFAULT '' NOT NULL,
	arena int(11) DEFAULT '0' NOT NULL,
	status int(11) DEFAULT '0' NOT NULL,

	referee int(11) DEFAULT '0' NOT NULL,
	assists text,
	videoreferee int(11) DEFAULT '0' NOT NULL,
	videoassists int(11) DEFAULT '0' NOT NULL,
	coach_home int(11) DEFAULT '0' NOT NULL,
	coach_guest int(11) DEFAULT '0' NOT NULL,
	players_home text,
	players_guest text,
	substitutes_home text,
	substitutes_guest text,
	system_home varchar(100) DEFAULT '' NOT NULL,
	system_guest varchar(100) DEFAULT '' NOT NULL,
	players_home_stat text,
	players_guest_stat text,
	substitutes_home_stat text,
	substitutes_guest_stat text,
	scorer_home_stat text,
	scorer_guest_stat text,

	goals_home_1 int(11) DEFAULT '0' NOT NULL,
	goals_guest_1 int(11) DEFAULT '0' NOT NULL,
	goals_home_2 int(11) DEFAULT '0' NOT NULL,
	goals_guest_2 int(11) DEFAULT '0' NOT NULL,
	goals_home_3 int(11) DEFAULT '0' NOT NULL,
	goals_guest_3 int(11) DEFAULT '0' NOT NULL,
	goals_home_4 int(11) DEFAULT '0' NOT NULL,
	goals_guest_4 int(11) DEFAULT '0' NOT NULL,
	score_home int(11) DEFAULT '0' NOT NULL,
	score_guest int(11) DEFAULT '0' NOT NULL,

	date int(11) DEFAULT '0' NOT NULL,
	link_report tinyint(4) DEFAULT '0' NOT NULL,
	link_ticker tinyint(4) DEFAULT '0' NOT NULL,
	game_report_author varchar(100) DEFAULT '' NOT NULL,
	game_report text,
	liveticker_author varchar(100) DEFAULT '' NOT NULL,
	visitors int(11) DEFAULT '0' NOT NULL,

	dam_media int(11) DEFAULT '0' NOT NULL,
	dam_media2 int(11) DEFAULT '0' NOT NULL,
	t3images int(11) DEFAULT '0' NOT NULL,

	is_extratime tinyint(3) DEFAULT '0' NOT NULL,
	goals_home_et int(11) DEFAULT '0' NOT NULL,
	goals_guest_et int(11) DEFAULT '0' NOT NULL,

	is_penalty tinyint(3) DEFAULT '0' NOT NULL,
	goals_home_ap int(11) DEFAULT '0' NOT NULL,
	goals_guest_ap int(11) DEFAULT '0' NOT NULL,
	extid varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_cfcleague_profiles'
#
CREATE TABLE tx_cfcleague_profiles (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	gdpr tinyint(4) DEFAULT '0' NOT NULL,
	gender tinyint(4) DEFAULT '0' NOT NULL,
	stage_name varchar(70) DEFAULT '' NOT NULL,
	first_name varchar(50) DEFAULT '' NOT NULL,
	last_name varchar(70) DEFAULT '' NOT NULL,
	link_report tinyint(4) DEFAULT '0' NOT NULL,
	t3images int(11) DEFAULT '0' NOT NULL,
	birthday int(11) DEFAULT '0' NOT NULL,
	dayofdeath int(11) DEFAULT '0' NOT NULL,
	died tinyint(4) DEFAULT '0' NOT NULL,
	home_town varchar(150) DEFAULT '' NOT NULL,
	native_town varchar(150) DEFAULT '' NOT NULL,
	nationality varchar(100) DEFAULT '' NOT NULL,
	height int(11) DEFAULT '0' NOT NULL,
	weight int(11) DEFAULT '0' NOT NULL,
	position varchar(150) DEFAULT '' NOT NULL,
	duration_of_contract int(11) DEFAULT '0' NOT NULL,
	start_of_contract varchar(150) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	nickname varchar(150) DEFAULT '' NOT NULL,
	summary text,
	description text,
	types varchar(150) DEFAULT '' NOT NULL,
	extid varchar(255) DEFAULT '' NOT NULL,
	relitems int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_cfcleague_profiletypes_mm'
# uid_local used for profiletype
#
CREATE TABLE tx_cfcleague_profiletypes_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(50) DEFAULT '' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_cfcleague_profiletypes_mm'
# uid_local used for fixture
#
CREATE TABLE tx_cfcleague_profiles_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(50) DEFAULT '' NOT NULL,
	fieldname varchar(50) DEFAULT '' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_cfcleague_saison'
#
CREATE TABLE tx_cfcleague_match_notes (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	second tinyint(4) DEFAULT '0' NOT NULL,
	minute int(11) DEFAULT '0' NOT NULL,
	extra_time tinyint(4) DEFAULT '0' NOT NULL,
	game int(11) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	player_home int(11) DEFAULT '0' NOT NULL,
	player_guest int(11) DEFAULT '0' NOT NULL,
	comment text,

	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY idx_game (game)
);

CREATE TABLE tx_cfcleague_team_notes (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	team int(11) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	mediatype int(11) DEFAULT '0' NOT NULL,
	player int(11) DEFAULT '0' NOT NULL,
	comment text,
	media int(11) DEFAULT '0' NOT NULL,
	number int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY idx_team (team)
);

CREATE TABLE tx_cfcleague_note_types (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,

	label varchar(50) DEFAULT '' NOT NULL,
	marker varchar(20) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_cfcleague_stadiums'
#
CREATE TABLE tx_cfcleague_stadiums (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	altname varchar(255) DEFAULT '' NOT NULL,
	capacity int(11) DEFAULT '0' NOT NULL,
	description text,
	description2 text,

	street varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	zip varchar(150) DEFAULT '' NOT NULL,
	country int(11) DEFAULT '0' NOT NULL,
	countrycode varchar(20) DEFAULT '' NOT NULL,
	lng tinytext,
	lat tinytext,

	logo int(11) DEFAULT '0' NOT NULL,
	pictures int(11) DEFAULT '0' NOT NULL,
	clubs int(11) DEFAULT '0' NOT NULL,
	address int(11) DEFAULT '0' NOT NULL,
	extid varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_cfcleague_stadiums_mm'
# uid_local used for stadium
#
CREATE TABLE tx_cfcleague_stadiums_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(50) DEFAULT '' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
