<?php
/*
Plugin Name: Grum Plugins Classes.3
Version: 3.1.0
Description: Collection de classes partagées entre mes plugins (existants, ou à venir) / Partaged classes between my plugins (actuals or futures)
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=199
Author: grum@piwigo.org
Author URI: http://photos.grum.fr/
*/

/*
--------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr

    << May the Little SpaceFrog be with you ! >>
--------------------------------------------------------------------------------

:: HISTORY

| release | date       |
| 2.0.0   | 2008/07/20 | * convert classes for piwigo 2.0
|         |            |
| 2.0.1   | 2008/12/28 | * convert classe tables.class.inc to php5
|         |            |
| 2.0.2   | 2009/04/26 | * add setOptions/getOptions for GPCPagesNavigation class
|         |            | * add option to set first/prev/next/last textes
|         |            |
| 2.0.3   | 2009/07/24 | * modify common_plugin class config loader (r2.0.1)
|         |            |
| 2.0.4   | 2009/11/29 | * modify users class
|         |            |
| 3.0.0   | 2010/03/28 | * Uses piwigo pwg_db_* functions instead of mysql_* functions
|         |            | * update classes & functions names
|         |            | * include the JpegMetaData class
|         |            |
| 3.0.1   | 2010/04/11 | * little bug on the template (call of an undefined var)
|         |            | * Add new languages
|         |            |   . es_ES
|         |            |   . hu_HU
|         |            |   . it_IT
|         |            |
| 3.1.0   | 2010/04/24 | * add the GPCTabSheet class
|         |            | * update the GPCCore class
|         |            | * Add new languages
|         |            |   . nl_NL
|         |            |   . de_DE
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |


:: TO DO

:: WHAT ? WHY ?
This plugin doesn't do anything itself. It just provide classes for others plugins.

Classes version for this package
    CommonPlugin.class.php
    GPCAjax.class.php
    GPCCss.class.php
    GPCPagesNavigation.class.php
    GPCPublicIntegration.class.php
    GPCTables.class.php -v1.5
    GPCTranslate.class.inc.php + google_translate.js
    GPCUsersGroups.class.inc.php
    GPCTabSheet.class.inc.php

    genericjs.class.inc.php  + genericjs.js

See each file to know more about them
--------------------------------------------------------------------------------
*/

if(!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('GPC_DIR' , basename(dirname(__FILE__)));
define('GPC_PATH' , PHPWG_PLUGINS_PATH . GPC_DIR . '/');

include_once('gpc_version.inc.php'); // => Don't forget to update this file !!

global $prefixeTable;

if(defined('IN_ADMIN'))
{
  //GPC admin interface is loaded and active only if in admin page
  include_once("gpc_aim.class.inc.php");

  $obj = new GPC_AIM($prefixeTable, __FILE__);
  $obj->initEvents();
  set_plugin_data($plugin['id'], $obj);
}

?>
