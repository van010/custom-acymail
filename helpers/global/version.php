<?php

function acym_level($neededLevel)
{
    $levels = [
        'Starter' => 0,
        'Essential' => 1,
        'Enterprise' => 2,
    ];

    $config = acym_config();
    $currentLevel = $config->get('level');

    $currentLevelNumber = in_array($currentLevel, array_keys($levels)) ? $levels[$currentLevel] : 0;

    return $currentLevelNumber >= $neededLevel;
}

function acym_upgradeTo(string $version, string $utmMedium)
{
    $link = ACYM_ACYMAILING_WEBSITE.'pricing?utm_source=acymailing_plugin&utm_medium='.$utmMedium.'&utm_campaign=purchase';
    $text = $version === 'essential' ? 'AcyMailing Essential' : 'AcyMailing Enterprise';
    echo '<div class="acym__upgrade cell grid-x text-center align-center">
            <h2 class="acym__listing__empty__title cell">'.acym_translationSprintf('ACYM_USE_THIS_FEATURE', '<span class="acym__color__blue">'.$text.'</span>').'</h2>
            <a target="_blank" href="'.$link.'" class="button  cell shrink">'.acym_translation('ACYM_UPGRADE_NOW').'</a>
          </div>';
}

function acym_existsAcyMailing59()
{
    $allTables = acym_getTables();
    if (!in_array(acym_getPrefix().'acymailing_config', $allTables)) return false;

    $version = acym_loadResult('SELECT `value` FROM #__acymailing_config WHERE `namekey` LIKE "version"');

    return version_compare($version, '5.9.0', '>=');
}

function acym_buttonGetProVersion($class = 'cell shrink', $text = 'ACYM_GET_PRO_VERSION')
{
    return '<a href="'.ACYM_ACYMAILING_WEBSITE.'pricing" target="_blank" class="button '.$class.'">'.acym_translation($text).'</a>';
}
