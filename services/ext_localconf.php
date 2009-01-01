<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


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