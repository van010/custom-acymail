<?php

function acym_escape($text, $addSlashes = true)
{
    if (is_array($text) || is_object($text)) {
        if ($addSlashes) {
            $text = str_replace('\\', '\\\\', json_encode($text));
        } else {
            $text = json_encode($text);
        }
    }

    if (empty($text)) {
        return $text;
    }

    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function acym_arrayToInteger(&$array)
{
    if (is_array($array)) {
        $array = @array_map('intval', $array);
    } else {
        $array = [];
    }
}

function acym_getIP()
{
    $map = [
        'HTTP_X_FORWARDED_IP',
        'X_FORWARDED_FOR',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ];

    $ipAddress = '';
    foreach ($map as $oneAttribute) {
        if (empty($_SERVER[$oneAttribute]) || strlen($_SERVER[$oneAttribute]) < 7) continue;

        $ipAddress = $_SERVER[$oneAttribute];
        break;
    }

    if (strstr($ipAddress, ',') !== false) {
        $addresses = explode(',', $ipAddress);
        $ipAddress = trim(end($addresses));
    }

    return strip_tags($ipAddress);
}

function acym_generateKey($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    $max = strlen($characters) - 1;
    for ($i = 0 ; $i < $length ; $i++) {
        $randstring .= $characters[mt_rand(0, $max)];
    }

    return $randstring;
}

function acym_isRobot(): bool
{
    if (empty($_SERVER)) {
        return false;
    }
    if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'spambayes') !== false) {
        return true;
    }
    if (!empty($_SERVER['REMOTE_ADDR']) && version_compare($_SERVER['REMOTE_ADDR'], '64.235.144.0', '>=') && version_compare($_SERVER['REMOTE_ADDR'], '64.235.159.255', '<=')) {
        return true;
    }

    return false;
}

function acym_displayErrors()
{
    error_reporting(E_ALL);
    @ini_set("display_errors", 1);
}

function acym_checkRobots()
{
    if (preg_match('#(libwww-perl|python|googlebot)#i', @$_SERVER['HTTP_USER_AGENT'])) {
        die('Not allowed for robots. Please contact us if you are not a robot');
    }
}

function acym_noCache()
{
    acym_header('Cache-Control: no-store, no-cache, must-revalidate');
    acym_header('Cache-Control: post-check=0, pre-check=0', false);
    acym_header('Pragma: no-cache');
    acym_header('Expires: Wed, 17 Sep 1975 21:32:10 GMT');
}

function acym_isAllowed($controller, $task = ''): bool
{
    $config = acym_config();
    $globalAccess = $config->get('acl_'.$controller, 'all');
    if ($globalAccess === 'all') {
        return true;
    }

    $globalAccess = explode(',', $globalAccess);
    $globalAccess[] = ACYM_ADMIN_GROUP;

    $userId = acym_currentUserId();
    if (empty($userId)) {
        return false;
    }

    $userGroups = acym_getGroupsByUser($userId);
    if (empty($userGroups)) {
        return false;
    }

    foreach ($userGroups as $oneGroup) {
        if (in_array($oneGroup, $globalAccess)) {
            return true;
        }
    }

    return false;
}

function acym_raiseError($code, $message)
{
    echo '<link type="text/css" rel="stylesheet" href="'.ACYM_CSS.'back_global.min.css?v='.filemtime(ACYM_MEDIA.'css'.DS.'back_global.min.css').'">';
    echo '<div id="acym_wrapper">';
    acym_display('Error '.$code.': '.$message, 'error', false);
    echo '</div>';
    http_response_code($code);
    exit;
}

function acym_isLicenseValidWeekly(): bool
{
    $config = acym_config();
    $expirationDate = $config->get('expirationdate', 0);
    if (empty($expirationDate) || (time() - 604800) > $config->get('lastlicensecheck', 0)) {
        acym_checkVersion();
        $config = acym_config(true);
        $expirationDate = $config->get('expirationdate', 0);
    }

    return $expirationDate >= time();
}
