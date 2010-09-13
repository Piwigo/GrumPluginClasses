<?php
/*
 * -----------------------------------------------------------------------------
 * Plugin Name: Grum Plugins Classes.3
 * -----------------------------------------------------------------------------
 * Author     : Grum
 *   email    : grum@piwigo.org
 *   website  : http://photos.grum.fr
 *   PWG user : http://forum.piwigo.org/profile.php?id=3706
 *
 *   << May the Little SpaceFrog be with you ! >>
 *
 * -----------------------------------------------------------------------------
 *
 * See main.inc.php for release information
 *
 * manage all the ajax requests
 * -----------------------------------------------------------------------------
 */

  define('PHPWG_ROOT_PATH',dirname(dirname(dirname(__FILE__))).'/');

  /*
   * set ajax module in admin mode if request is used for admin interface
   */
  if(!isset($_REQUEST['ajaxfct'])) $_REQUEST['ajaxfct']='';
  if(preg_match('/^admin\./i', $_REQUEST['ajaxfct']))
  {
    define('IN_ADMIN', true);
  }

  // the common.inc.php file loads all the main.inc.php plugins files
  include_once(PHPWG_ROOT_PATH.'include/common.inc.php' );
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCAjax.class.inc.php');

  load_language('plugin.lang', GPC_PATH);

  class GPC_Ajax extends CommonPlugin
  {
    /**
     * constructor
     */
    public function __construct($prefixeTable, $filelocation)
    {
      $this->setPluginName("Grum Plugin Classes");
      $this->setPluginNameFiles("gpc");
      parent::__construct($prefixeTable, $filelocation);

      $tableList=array('result_cache');
      $this->setTablesList($tableList);

      $this->loadConfig();
      $this->checkRequest();
      $this->returnAjaxContent();
    }

    /**
     * check the $_REQUEST values and set default values
     *
     */
    protected function checkRequest()
    {
      global $user;

      if(!isset($_REQUEST['ajaxfct'])) $_REQUEST['ajaxfct']='';

      // check if asked function is valid
      if(!(
           $_REQUEST['ajaxfct']=='admin.rbuilder.fillCaddie'
          )
        ) $_REQUEST['ajaxfct']='';

      if(preg_match('/^admin\./i', $_REQUEST['ajaxfct']) and !is_admin()) $_REQUEST['ajaxfct']='';

      if($_REQUEST['ajaxfct']!='')
      {
        /*
         * check admin.makeStats.getList values
         */
        if($_REQUEST['ajaxfct']=="admin.rbuilder.fillCaddie")
        {
          if(!isset($_REQUEST['fillMode'])) $_REQUEST['fillMode']="add";

          if(!($_REQUEST['fillMode']=="add" or
               $_REQUEST['fillMode']=="replace")) $_REQUEST['fillMode']="add";

          if(!isset($_REQUEST['requestNumber'])) $_REQUEST['ajaxfct']="";
        }
      }
    }


    /**
     * return ajax content
     */
    protected function returnAjaxContent()
    {
      $result="<p class='errors'>An error has occured</p>";
      switch($_REQUEST['ajaxfct'])
      {
        case 'admin.rbuilder.fillCaddie':
          $result=$this->ajax_gpc_admin_rbuilderFillCaddie($_REQUEST['fillMode'], $_REQUEST['requestNumber']);
          break;
      }
      GPCAjax::returnResult($result);
    }



    /*------------------------------------------------------------------------*
     *
     * ADMIN FUNCTIONS
     *
     *----------------------------------------------------------------------- */

    /**
     * fill the caddie with the result of the requestNumber
     *
     * @param String $fillMode : 'addCaddie' or 'replaceCaddie'
     * @param Integer $requestNumber : number of the request in cache
     * @return String :
     */
    private function ajax_gpc_admin_rbuilderFillCaddie($mode, $requestNumber)
    {
      global $user;

      switch($mode)
      {
        case "replace":
          $sql="DELETE FROM ".CADDIE_TABLE." WHERE user_id = '".$user['id']."';";
          pwg_query($sql);
        case "add":
          $sql="INSERT IGNORE INTO ".CADDIE_TABLE."
                  SELECT '".$user['id']."', image_id
                  FROM ".$this->tables['result_cache']."
                  WHERE id = '$requestNumber';";
          pwg_query($sql);
          break;
      }
    }

  } //class


  $returned=new GPC_Ajax($prefixeTable, __FILE__);
?>
