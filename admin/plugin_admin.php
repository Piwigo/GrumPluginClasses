<?php
/* -----------------------------------------------------------------------------
  Plugin     : Grum Plugin Class
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }


global $prefixeTable;

load_language('plugin.lang', GPC_PATH);


if(isset($_REQUEST['searchRequest']))
{
  load_language('requestBuilder.lang', GPC_PATH);
  // if set, this is probably an ajax search request
  include_once(GPC_PATH."classes/GPCRequestBuilder.class.inc.php");
  GPCRequestBuilder::init($prefixeTable, 'gpc');
  GPCRequestBuilder::executeRequest();
}
else
{
  include(GPC_PATH."gpc_aip.class.inc.php");

  $main_plugin_object = get_plugin_data($plugin_id);

  $plugin_ai = new GPC_AIP($prefixeTable, $main_plugin_object->getFileLocation());
  $plugin_ai->manage();
}



?>
