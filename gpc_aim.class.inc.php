<?php
/* -----------------------------------------------------------------------------
  Plugin     : Grum Plugin Classes - 3
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  UserStat_AIM : classe to manage plugin integration into plugin menu

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCss.class.inc.php');

class GPC_AIM extends CommonPlugin
{
  protected $css = null;

  public function __construct($prefixeTable, $filelocation)
  {
    $this->setPluginName("Grum Plugin Classes");
    $this->setPluginNameFiles("gpc");
    parent::__construct($prefixeTable, $filelocation);
    $this->css = new GPCCss(dirname($this->getFileLocation()).'/'.$this->getPluginNameFiles().".css");
  }

  public function __destruct()
  {
    unset($this->css);
    parent::__destruct();
  }

  /*
    initialize events call for the plugin
  */
  function initEvents()
  {
    add_event_handler('get_admin_plugin_menu_links', array(&$this, 'pluginAdminMenu') );
  }

  /*
    surchage of CommonPlugin->saveConfig function
  */
  function loadConfig()
  {
    parent::loadConfig();
    if(!$this->css->fileExists())
    {
      $this->css->makeCSS($this->generate_CSS());
    }
  }

  /*
    surchage of CommonPlugin->saveConfig function
  */
  function saveConfig()
  {
    if(parent::saveConfig())
    {
      $this->css->makeCSS($this->generate_CSS());
      return(true);
    }
    return(false);
  }

  /*
    generate the css code
  */
  function generate_CSS()
  {
    $text = "
.formtable, .formtable P { text-align:left; display:block; }
.formtable tr { vertical-align:top; }
.invisible { visibility:hidden; display:none; }
.littlefont { font-size:90%; }
table.table2.littlefont td { text-align:center;padding:0px;padding-left:3px;padding-right:3px; }
.throw { line-height:auto; font-size:100%; }
table.table2 tr.throw { height:26px; }
table.table2 td.toLeft  { text-align:left; }
div.table { margin-bottom:15px; }
pointerHand { cursor:pointer; }
    ";

    return($text);
  }

  /* ---------------------------------------------------------------------------
  Function needed for plugin activation
  --------------------------------------------------------------------------- */



} // GPC_AIM class


?>
