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
 *
 * known functions :
 *  - admin.rbuilder.fillCaddie
 *  - admin.categorySelector.getList
 *
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

  global $page;
  $page['root_path']='./';

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
           $_REQUEST['ajaxfct']=='admin.rbuilder.fillCaddie' or
           $_REQUEST['ajaxfct']=='public.rbuilder.searchExecute' or
           $_REQUEST['ajaxfct']=='public.rbuilder.searchGetPage' or
           $_REQUEST['ajaxfct']=='admin.categorySelector.getList' or
           $_REQUEST['ajaxfct']=='public.categorySelector.getList'
          )
        ) $_REQUEST['ajaxfct']='';

      if(preg_match('/^admin\./i', $_REQUEST['ajaxfct']) and !is_admin()) $_REQUEST['ajaxfct']='';

      if($_REQUEST['ajaxfct']!='')
      {
        /*
         * check admin.rbuilder.fillCaddie values
         */
        if($_REQUEST['ajaxfct']=="admin.rbuilder.fillCaddie")
        {
          if(!isset($_REQUEST['fillMode'])) $_REQUEST['fillMode']="add";

          if(!($_REQUEST['fillMode']=="add" or
               $_REQUEST['fillMode']=="replace")) $_REQUEST['fillMode']="add";

          if(!isset($_REQUEST['requestNumber'])) $_REQUEST['ajaxfct']="";
        }

        /*
         * check public.rbuilder.searchExecute values
         */
        if($_REQUEST['ajaxfct']=="public.rbuilder.searchExecute")
        {
          if(!isset($_REQUEST['requestName'])) $_REQUEST['ajaxfct']="";
        }

        /*
         * check public.rbuilder.searchGetPage values
         */
        if($_REQUEST['ajaxfct']=="public.rbuilder.searchGetPage")
        {
           if(!isset($_REQUEST['requestNumber'])) $_REQUEST['ajaxfct']="";

          if(!isset($_REQUEST['page'])) $_REQUEST['page']=0;

          if($_REQUEST['page']<0) $_REQUEST['page']=0;

          if(!isset($_REQUEST['numPerPage'])) $_REQUEST['numPerPage']=25;

          if($_REQUEST['numPerPage']>100) $_REQUEST['numPerPage']=100;
        }


        /*
         * check admin.categorySelector.getList values
         */
        if($_REQUEST['ajaxfct']=="admin.categorySelector.getList" or
           $_REQUEST['ajaxfct']=="public.categorySelector.getList")
        {
          if(!isset($_REQUEST['filter'])) $_REQUEST['filter']="accessible";

          if(!($_REQUEST['filter']=="public" or
               $_REQUEST['filter']=="accessible" or
               $_REQUEST['filter']=="all")
            ) $_REQUEST['filter']="accessible";

          if(!isset($_REQUEST['galleryRoot'])) $_REQUEST['galleryRoot']="y";

          if(!($_REQUEST['galleryRoot']=="y" or
               $_REQUEST['galleryRoot']=="n")
            ) $_REQUEST['galleryRoot']="y";

          if(!isset($_REQUEST['tree'])) $_REQUEST['tree']="n";

          if(!($_REQUEST['tree']=="y" or
               $_REQUEST['tree']=="n")
            ) $_REQUEST['tree']="n";
        }


      }
    } //checkRequest()


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
        case 'public.rbuilder.searchExecute':
          $result=$this->ajax_gpc_public_rbuilderSearchExecute();
          break;
        case 'public.rbuilder.searchGetPage':
          $result=$this->ajax_gpc_public_rbuilderSearchGetPage();
          break;
        case 'admin.categorySelector.getList':
          $result=$this->ajax_gpc_admin_CategorySelectorGetList($_REQUEST['filter'], $_REQUEST['galleryRoot'], $_REQUEST['tree']);
          break;
        case 'public.categorySelector.getList':
          $result=$this->ajax_gpc_public_CategorySelectorGetList($_REQUEST['filter'], $_REQUEST['galleryRoot'], $_REQUEST['tree']);
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



    /**
     * return the list of all categories
     *
     * @param String $filter : 'public' or 'accessible' or 'all'
     * @param String $galleryRoot : 'y' if the gallery root is in the list
     * @param String $tree : 'y' to obtain a recursive array, 'n' to obtain a flat array
     * @return String : json string
     */
    private function ajax_gpc_admin_CategorySelectorGetList($filter, $galleryRoot, $tree)
    {
      global $user;

      include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCategorySelector.class.inc.php');

      $categorySelector=new GPCCategorySelector(
        array(
          'filter' => $filter,
          'galleryRoot' => ($galleryRoot=='y')?true:false,
          'tree' => ($tree=='y')?true:false,
          'userMode' => 'admin'
        )
      );

      $returned=array(
        'userId' => $user['id'],
        'nbCategories' => 0,
        'categories' => $categorySelector->getCategoryList(),
        'status' => array(
          0=>l10n('Private'),
          1=>l10n('Public')
        )
      );
      $returned['nbCategories']=count($returned['categories']);

      return(json_encode($returned));
    } //ajax_gpc_admin_CategorySelectorGetList


    /**
     * return the list of all categories
     *
     * @param String $filter : 'public' or 'accessible' or 'all'
     * @param String $galleryRoot : 'y' if the gallery root is in the list
     * @param String $tree : 'y' to obtain a recursive array, 'n' to obtain a flat array
     * @param String $userMode : 'public' or 'admin'
     * @return String : json string
     */
    private function ajax_gpc_public_CategorySelectorGetList($filter, $galleryRoot, $tree)
    {
      global $user;

      include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCategorySelector.class.inc.php');

      $categorySelector=new GPCCategorySelector(
        array(
          'filter' => $filter,
          'galleryRoot' => ($galleryRoot=='y')?true:false,
          'tree' => ($tree=='y')?true:false,
          'userMode' => 'public'
        )
      );

      $returned=array(
        'userId' => $user['id'],
        'nbCategories' => 0,
        'categories' => $categorySelector->getCategoryList(),
        'status' => array(
          0=>l10n('Private'),
          1=>l10n('Public')
        )
      );
      $returned['nbCategories']=count($returned['categories']);

      return(json_encode($returned));
    } //ajax_gpc_public_CategorySelectorGetList


    /**
     *
     * @return String :
     */
    private function ajax_gpc_public_rbuilderSearchExecute()
    {
      global $prefixeTable;
      include_once(GPC_PATH."classes/GPCRequestBuilder.class.inc.php");
      GPCRequestBuilder::init($prefixeTable, 'gpc');
      return(GPCRequestBuilder::executeRequest($_REQUEST['ajaxfct']));
    }

    /**
     *
     * @return String :
     */
    private function ajax_gpc_public_rbuilderSearchGetPage()
    {
      global $prefixeTable;
      include_once(GPC_PATH."classes/GPCRequestBuilder.class.inc.php");
      GPCRequestBuilder::init($prefixeTable, 'gpc');
      return(GPCRequestBuilder::executeRequest($_REQUEST['ajaxfct']));
    }


  } //class


  $returned=new GPC_Ajax($prefixeTable, __FILE__);
?>
