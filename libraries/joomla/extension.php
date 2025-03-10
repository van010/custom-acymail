<?php

use AcyMailing\Classes\PluginClass;
use Joomla\CMS\Component\ComponentHelper;

function acym_isExtensionActive($extension)
{
    return ComponentHelper::isInstalled($extension) && ComponentHelper::isEnabled($extension, true);
}

function acym_getPluginsPath($file, $dir)
{
    return rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS;
}

function acym_getPluginPath($plugin)
{
    return ACYM_ADDONS_FOLDER_PATH.$plugin.DS.'plugin.php';
}

function acym_coreAddons()
{
    acym_loadLanguageFile('com_modules', JPATH_ADMINISTRATOR);

    return [
        (object)[
            'title' => acym_translation('ACYM_ARTICLE'),
            'folder_name' => 'article',
            'version' => '9.4.1',
            'active' => '1',
            'category' => 'Content management',
            'level' => 'starter',
            'uptodate' => '1',
            'description' => '- Insert Joomla articles in your emails<br/>- Insert the latest articles of a category in an automatic email',
            'latest_version' => '9.4.1',
            'type' => 'CORE',
        ],
        (object)[
            'title' => acym_translation('ACYM_CREATE_USER'),
            'folder_name' => 'createuser',
            'version' => '9.4.1',
            'active' => '1',
            'category' => 'User management',
            'level' => 'starter',
            'uptodate' => '1',
            'description' => '- Automatically creates a site user when an AcyMailing subscriber is created',
            'latest_version' => '9.4.1',
            'type' => 'CORE',
        ],
        (object)[
            'title' => acym_translation('COM_MODULES_MODULE'),
            'folder_name' => 'module',
            'version' => '9.4.1',
            'active' => '1',
            'category' => 'Content management',
            'level' => 'starter',
            'uptodate' => '1',
            'features' => '[]',
            'description' => '- Insert Joomla modules in your emails',
            'latest_version' => '9.4.1',
            'type' => 'CORE',
        ],
    ];
}

function acym_isTrackingSalesActive()
{
    return false;
}

function acym_loadPlugins()
{
    $dynamicsLoadedLast = ['managetext'];
    $dynamics = acym_getFolders(ACYM_BACK.'dynamics');

    $pluginClass = new PluginClass();
    $plugins = $pluginClass->getAll('folder_name');

    foreach ($dynamics as $key => $oneDynamic) {
        if (!empty($plugins[$oneDynamic]) && 0 === intval($plugins[$oneDynamic]->active)) {
            unset($dynamics[$key]);
        }

        if ('managetext' === $oneDynamic) {
            unset($dynamics[$key]);
        }
    }

    $pluginsLoadedLast = ['tableofcontents'];
    foreach ($plugins as $pluginFolder => $onePlugin) {
        if (in_array($pluginFolder, $dynamics) || 0 === intval($onePlugin->active)) {
            continue;
        }

        if (in_array($pluginFolder, $pluginsLoadedLast)) {
            array_unshift($dynamicsLoadedLast, $pluginFolder);
        } else {
            $dynamics[] = $pluginFolder;
        }
    }

    $dynamics = array_merge($dynamics, $dynamicsLoadedLast);

    global $acymPlugins;
    global $acymAddonsForSettings;
    foreach ($dynamics as $oneDynamic) {
        $dynamicFile = acym_getPluginPath($oneDynamic);
        $className = 'plgAcym'.ucfirst($oneDynamic);

        if (isset($acymPlugins[$className]) || !file_exists($dynamicFile) || !include_once $dynamicFile) {
            continue;
        }

        if (!class_exists($className)) {
            continue;
        }

        $plugin = new $className();
        if (in_array($plugin->cms, ['all', 'Joomla'])) {
            $acymAddonsForSettings[$className] = $plugin;
        }

        if (!in_array($plugin->cms, ['all', 'Joomla']) || !$plugin->installed) {
            continue;
        }

        $acymPlugins[$className] = $plugin;
    }
}
