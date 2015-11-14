<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_rnbase::load('tx_cfcleague_util_ServiceRegistry');


tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Stadiums' /* sv key */,
  array(
    'title' => 'T3sports stadium service', 'description' => 'Operations for stadiums', 'subtype' => 'stadiums',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Stadiums.php',
    'className' => 'tx_cfcleague_services_Stadiums',
  )
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Saison' /* sv key */,
	array(
		'title' => 'T3sports saison service', 'description' => 'Operations for saisons', 'subtype' => 'saison',
		'available' => TRUE, 'priority' => 50, 'quality' => 50,
		'os' => '', 'exec' => '',
		'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Saison.php',
		'className' => 'tx_cfcleague_services_Saison',
	)
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Competition' /* sv key */,
  array(
    'title' => 'T3sports stadium service', 'description' => 'Operations for competitions', 'subtype' => 'competition',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Competition.php',
    'className' => 'tx_cfcleague_services_Competition',
  )
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Match' /* sv key */,
  array(
    'title' => 'T3sports match service', 'description' => 'Operations for matches', 'subtype' => 'match',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Match.php',
    'className' => 'tx_cfcleague_services_Match',
  )
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Group' /* sv key */,
  array(
    'title' => 'T3sports group service', 'description' => 'Operations for groups', 'subtype' => 'group',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Group.php',
    'className' => 'tx_cfcleague_services_Group',
  )
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Teams' /* sv key */,
  array(
    'title' => 'T3sports team service', 'description' => 'Operations for teams', 'subtype' => 'teams',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Teams.php',
    'className' => 'tx_cfcleague_services_Teams',
  )
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Profiles' /* sv key */,
  array(
    'title' => 'T3sports profile service', 'description' => 'Operations for profiles', 'subtype' => 'profiles',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Profiles.php',
    'className' => 'tx_cfcleague_services_Profiles',
  )
);

tx_rnbase_util_Extensions::addService($_EXTKEY,  't3sports_profiletype' /* sv type */,  'tx_cfcleague_services_ProfileTypes' /* sv key */,
  array(
    'title' => 'Base profile types', 'description' => 'Defines the base types for profiles like players, coaches...', 'subtype' => 'basetypes',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_cfcleague_services_ProfileTypes.php',
    'className' => 'tx_cfcleague_services_ProfileTypes',
  )
);

