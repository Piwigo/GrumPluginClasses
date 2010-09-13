<?php

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', true);

include_once('gpc_version.inc.php'); // => Don't forget to update this file !!
include_once('gpc_install.class.inc.php'); // => Don't forget to update this file !!



function plugin_install($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable;

  $obj = new GPC_Install($prefixeTable, __FILE__);
  $result=$obj->install();
  if(!$result)
  {
    array_push($errors, "error");
  }
  unset($obj);
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
  $obj = new GPC_Install($prefixeTable, __FILE__);
  $result=$obj->activate();
  if(!$result)
  {
    array_push($errors, "error");
  }
  unset($obj);
}

function plugin_deactivate($plugin_id)
{
}

function plugin_uninstall($plugin_id)
{
  global $prefixeTable;

  $obj = new GPC_Install($prefixeTable, __FILE__);
  $result=$obj->uninstall();
  /*
   * piwigo don't alllow to manage errors during the uninstall process...
  if(!$result or is_string($result))
  {
    array_push($errors, l10n($result));
  }
  */
  unset($obj);
}


?>
