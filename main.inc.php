<?php
/*
Plugin Name: GMaps
Version: 0.2.0
Description: Display and manage (google) maps
Plugin URI: http://phpwebgallery.net/ext/extension_view.php?eid=
Author: grum@piwigo.org
Author URI: http://photos.grum.fr
*/

/*
--------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@piwigo.com
    website  : http://photos.grum.fr
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
--------------------------------------------------------------------------------

:: HISTORY

| release | date       |
| 0.1.0   | 2010-08-22 | * first lines of code
|         |            |   . release not published
|         |            |
| 0.2.0   | 2010-09-30 | * first official release
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
|         |            |
|         |            |
|         |            |
|         |            |


:: TO DO

--------------------------------------------------------------------------------

:: NFO
  GMaps_root : common classe for admin and public classes
  GMaps_AIM  : classe to manage plugin integration into plugin menu
  GMaps_AIP  : classe to manage plugin admin pages
  GMaps_PIP  : classe to manage plugin public pages

--------------------------------------------------------------------------------
*/

// pour faciliter le debug :o)
 //ini_set('error_reporting', E_ALL);
 //ini_set('display_errors', true);

if(!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');


define('GMAPS_DIR' , basename(dirname(__FILE__)));
define('GMAPS_PATH' , PHPWG_PLUGINS_PATH . GMAPS_DIR . '/');

include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
include_once('gmaps_version.inc.php'); // => Don't forget to update this file !!

global $prefixeTable;

if(defined('IN_ADMIN'))
{
  //GMaps admin interface loaded and active only if in admin page
  include_once("gmaps_aim.class.inc.php");
  $obj=new GMaps_AIM($prefixeTable, __FILE__);
  $obj->initEvents();
}
else
{
  if(CommonPlugin::checkGPCRelease(GMAPS_GPC_NEEDED))
  {
    //GMaps public interface loaded and active only if in public page
    include_once("gmaps_pip.class.inc.php");
    $obj=new GMaps_PIP($prefixeTable, __FILE__);
  }
}

set_plugin_data($plugin['id'], $obj);

?>
