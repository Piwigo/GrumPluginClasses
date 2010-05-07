<?php
/* -----------------------------------------------------------------------------
  Plugin     : Grum Plugin Classes - 3
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information


  GPC_Install : classe to manage plugin install

  --------------------------------------------------------------------------- */

  if(!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

  include_once('gpc_version.inc.php'); // => Don't forget to update this file !!
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCore.class.inc.php');
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCRequestBuilder.class.inc.php');

  /* PGC class for install process */
  class GPC_Install extends CommonPlugin
  {
    private $tablef;

    public function __construct($prefixeTable, $filelocation)
    {
      $this->setPluginName("Grum Plugin Classes");
      $this->setPluginNameFiles("gpc");
      parent::__construct($prefixeTable, $filelocation);
      GPCRequestBuilder::init($prefixeTable, $this->getPluginNameFiles());
    }

    public function __destruct()
    {
      parent::__destruct();
    }

    /*
        function for installation process
        return true if install process is ok, otherwise false
    */
    public function install()
    {
      $result=GPCRequestBuilder::createTables();
      return($result);
    }


    /*
        function for uninstall process
    */
    public function uninstall()
    {
      $registeredPlugin=GPCCore::getRegistered();
      if(count($registeredPlugin)>0)
      {
        return("Some plugins are dependent on Grum Plugin Classes: before uninstall, you must first uninstall the plugins dependent");
      }
      else
      {
        $result=GPCRequestBuilder::deleteTables();
        return($result);
      }
    }

    public function activate()
    {
    }

    public function deactivate()
    {
    }

  } //class

?>
