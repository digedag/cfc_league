<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');
tx_div::load('tx_cfcleague_util_ServiceRegistry');


t3lib_extMgm::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Stadiums' /* sv key */,
  array(
    'title' => 'T3sports stadium service', 'description' => 'Operations for stadiums', 'subtype' => 'stadiums',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => t3lib_extMgm::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Stadiums.php',
    'className' => 'tx_cfcleague_services_Stadiums',
  )
);

t3lib_extMgm::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Teams' /* sv key */,
  array(
    'title' => 'T3sports team service', 'description' => 'Operations for teams', 'subtype' => 'teams',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => t3lib_extMgm::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Teams.php',
    'className' => 'tx_cfcleague_services_Teams',
  )
);

t3lib_extMgm::addService($_EXTKEY,  't3sports_srv' /* sv type */,  'tx_cfcleague_services_Profiles' /* sv key */,
  array(
    'title' => 'T3sports profile service', 'description' => 'Operations for profiles', 'subtype' => 'profiles',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => t3lib_extMgm::extPath($_EXTKEY).'services/class.tx_cfcleague_services_Profiles.php',
    'className' => 'tx_cfcleague_services_Profiles',
  )
);

t3lib_extMgm::addService($_EXTKEY,  't3sports_profiletype' /* sv type */,  'tx_cfcleague_services_ProfileTypes' /* sv key */,
  array(
    'title' => 'Base profile types', 'description' => 'Defines the base types for profiles like players, coaches...', 'subtype' => 'basetypes',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => t3lib_extMgm::extPath($_EXTKEY).'services/class.tx_cfcleague_services_ProfileTypes.php',
    'className' => 'tx_cfcleague_services_ProfileTypes',
  )
);


?>