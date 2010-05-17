<?php
/* -----------------------------------------------------------------------------
  class name: GCPRequestBuilder
  class version  : 1.0.0
  plugin version : 3.1.0
  date           : 2010-04-29

  ------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------

  * theses classes provides base functions to manage search pictures in the
  * database
  *


  ------------------------------------------------------------------------------
  :: HISTORY

| release | date       |
| 1.0.0   | 2010/04/30 | * start coding
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

  --------------------------------------------------------------------------- */

include_once('GPCAjax.class.inc.php');
include_once('GPCTables.class.inc.php');


class GPCSearchCallback {

  /**
   * the getSelect function must return an attribute list separated with a comma
   *
   * "att1, att2, att3, att4"
   */
  static public function getSelect($param="")
  {
    return("");
  }

  /**
   * the getFrom function must return a tables list separated with a comma
   *
   * "table1, (table2 left join table3 on table2.key = table3.key), table4"
   */
  static public function getFrom($param="")
  {
    return("");
  }

  /**
   * the getWhere function must return a ready to use where clause
   *
   * "(att1 = value0 OR att2 = value1) AND att4 LIKE value2 "
   */
  static public function getWhere($param="")
  {
    return("");
  }

  /**
   * the getJoin function must return a ready to use where allowing to join the
   * IMAGES table (key : pit.id) with given conditions
   *
   * "att3 = pit.id "
   */
  static public function getJoin($param="")
  {
    return("");
  }


  /**
   * this function is called by the request builder, allowing to display plugin
   * data with a specific format
   *
   * @param Array $attributes : array of ('attribute_name' => 'attribute_value')
   * @return String : HTML formatted value
   */
  static public function formatData($attributes)
  {
    return(print_r($attributes, true));
  }


  /**
   * this function is called by the request builder to make the search page, and
   * must return the HTML & JS code of the dialogbox used to select criterion
   *
   * Notes :
   *  - the dialogbox is a JS object with a public method 'show'
   *  - when the method show is called, one parameter is given by the request
   *    builder ; the parameter is an object defined as this :
   *      {
   *        cBuilder: an instance of the criteriaBuilder object used in the page,
   *      }
   *
   *
   *
   *
   * @param String $mode : can take 'admin' or 'public' values, allowing to
   *                       return different interface if needed
   * @return String : HTML formatted value
   */
  static public function getInterfaceContent($mode='admin')
  {
    return("");
  }

  /**
   * this function returns the label displayed in the criterion menu
   *
   * @return String : label displayed in the criterions menu
   */
  static public function getInterfaceLabel()
  {
    return(l10n('gpc_rb_unknown_interface'));
  }

  /**
   * this function returns the name of the dialog box class
   *
   * @return String : name of the dialogbox class
   */
  static public function getInterfaceDBClass()
  {
    return('');
  }


}



class GPCRequestBuilder {

  static public $pluginName = 'GPCRequestBuilder';
  static public $version = '1.0.0';

  static private $tables = Array();

  /**
   * register a plugin using GPCRequestBuilder
   *
   * @param String $pluginName : the plugin name
   * @param String $fileName : the php filename where the callback function can
   *                           be found
   * @return Boolean : true if registering is Ok, otherwise false
   */
  static public function register($pluginName, $fileName, $dialogBoxObject)
  {
    $config=Array();
    if(GPCCore::loadConfig(self::$pluginName, $config))
    {
      $config['registered'][$pluginName]=Array(
        'name' => $pluginName,
        'fileName' => $fileName,
        'dialogBox' => $dialogBoxObject,
        'date' => date("Y-m-d H:i:s"),
        'version' => self::$version,
      );
      return(GPCCore::saveConfig(self::$pluginName, $config));
    }
    return(false);
  }

  /**
   * unregister a plugin using GPCRequestBuilder
   *
   * assume that if the plugin was not registerd before, unregistering returns
   * a true value
   *
   * @param String $pluginName : the plugin name
   * @return Boolean : true if registering is Ok, otherwise false
   */
  static public function unregister($plugin)
  {
    $config=Array();
    if(GPCCore::loadConfig(self::$pluginName, $config))
    {
      if(array_key_exists('registered', $config))
      {
        if(array_key_exists($plugin, $config['registered']))
        {
          unset($config['registered'][$plugin]);
          return(GPCCore::saveConfig(self::$pluginName, $config));
        }
      }
    }
    // assume if the plugin was not registered before, unregistering it is OK
    return(true);
  }

  /**
   * @return Array : list of registered plugins
   */
  static public function getRegistered()
  {
    $config=Array();
    if(GPCCore::loadConfig(self::$pluginName, $config))
    {
      if(array_key_exists('registered', $config))
      {
        return($config['registered']);
      }
    }
    return(Array());
  }


  /**
   * initialise the class
   *
   * @param String $prefixeTable : the piwigo prefixe used on tables name
   * @param String $pluginNameFile : the plugin name used for tables name
   */
  static public function init($prefixeTable, $pluginNameFile)
  {
    $list=Array('request', 'result_cache');

    for($i=0;$i<count($list);$i++)
    {
      self::$tables[$list[$i]]=$prefixeTable.$pluginNameFile.'_'.$list[$i];
    }
  }

  /**
   * create the tables needed by RequestBuilder (used during the gpc process install)
   */
  static public function createTables()
  {
    $tablesDef=array(
"CREATE TABLE `".self::$tables['request']."` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `num_items` int(10) unsigned NOT NULL default '0',
  `execution_time` float unsigned NOT NULL default '0',
  `connected_plugin` char(255) NOT NULL,
  PRIMARY KEY  (`id`)
)
CHARACTER SET utf8 COLLATE utf8_general_ci",

"CREATE TABLE `".self::$tables['result_cache']."` (
  `id` int(10) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`,`image_id`)
)
CHARACTER SET utf8 COLLATE utf8_general_ci",
  );

    $tablef= new GPCTables(self::$tables);
    $tablef->create($tablesDef);

    return(true);
  }

  /**
   * delete the tables needed by RequestBuilder
   */
  static public function deleteTables()
  {
    $tablef= new GPCTables(self::$tables);
    $tablef->drop($tables_def);
    return(true);
  }


  /**
   * this function add and handler on the 'loc_end_page_header' to add request
   * builder JS script & specific CSS on the page
   *
   * use it when the displayed page need an access to the criteriaBuilder GUI
   *
   */
  static public function loadJSandCSS()
  {
    add_event_handler('loc_end_page_header', array('GPCRequestBuilder', 'insertJSandCSSFiles'));
  }


  /**
   * insert JS a CSS file in header
   *
   * the function is declared public because it used by the 'loc_end_page_header'
   * event callback
   *
   * DO NOT USE IT DIRECTLY
   *
   */
  static public function insertJSandCSSFiles()
  {
    global $template;

    $baseName=basename(dirname(dirname(__FILE__))).'/css/';
    $template->append('head_elements', '<link href="plugins/'.$baseName.'rbuilder.css" type="text/css" rel="stylesheet"/>');

    $baseName=basename(dirname(dirname(__FILE__))).'/js/';
    $template->append('head_elements', '<script type="text/javascript" src="plugins/'.$baseName.'external/interface/interface.js"></script>');
    $template->append('head_elements', '<script type="text/javascript" src="plugins/'.$baseName.'external/inestedsortable.pack.js"></script>');
    $template->append('head_elements', '<script type="text/javascript" src="plugins/'.$baseName.'criteriaBuilder.js"></script>');
    $template->append('head_elements',
"<script type=\"text/javascript\">
  requestBuilderOptions = {
      textAND:'".l10n('gpc_rb_textAND')."',
      textOR:'".l10n('gpc_rb_textOR')."',
      imgEditUrl:'',
      imgDeleteUrl:'',
      ajaxUrl:'admin.php?page=plugin&section=".basename(GPC_DIR)."/admin/plugin_admin.php&searchRequest=',
  }
</script>");
  }


  /**
   * execute request from the ajax call
   *
   * @return String : a ready to use HTML code
   */
  static public function executeRequest()
  {
    if(self::checkAjaxRequest())
    {
      switch($_REQUEST['searchRequest'])
      {
        case 'execute':
          $result=self::doCache();
          break;
        case 'getPage':
          $result=self::getPage($_REQUEST['requestNumber'], $_REQUEST['page'], $_REQUEST['numPerPage']);
          break;
      }
      GPCAjax::returnResult($result);
    }
    else
    {
      GPCAjax::returnResult(l10n('gpc_rb_invalid_request'));
    }
  }


  /**
   * clear the cache table
   *
   * @param Boolean $clearAll : if set to true, clear all records without
   *                            checking timestamp
   */
  static public function clearCache($clearAll=false)
  {
    if($clearAll)
    {
      $sql="DELETE FROM ".self::$tables['result_cache'];
    }
    else
    {
      $sql="DELETE pgrc FROM ".self::$tables['result_cache']." pgrc
              LEFT JOIN ".self::$tables['request']." pgr
                ON pgrc.id = pgr.id
              WHERE pgr.date < '".date('Y-m-d H:i:s', strtotime("-2 hour"))."'";
    }
    pwg_query($sql);
  }

  /**
   * execute a query, and place result in cache
   *
   *
   * @return String : queryNumber;numberOfItems
   */
  static private function doCache()
  {
    global $user;

    self::clearCache();

    $registeredPlugin=self::getRegistered();

    $build=Array(
      'SELECT' => 'pit.id',
      'FROM' => '',
      'WHERE' => '',
      'GROUPBY' => '',
    );
    $tmpBuild=Array(
      'FROM' => Array(
        '('.IMAGES_TABLE.' pit LEFT JOIN '.IMAGE_CATEGORY_TABLE.' pic ON pit.id = pic.image_id)', //JOIN IMAGES & IMAGE_CATEGORY tables
      ),
      'WHERE' => Array(),
      'JOIN' => Array(),
      'GROUPBY' => Array(
        'pit.id'
      )
    );

    /* build data request for plugins
     *
     * Array('Plugin1' =>
     *          Array(
     *            criteriaNumber1 => pluginParam1,
     *            criteriaNumber2 => pluginParam2,
     *            criteriaNumberN => pluginParamN
     *          ),
     *       'Plugin2' =>
     *          Array(
     *            criteriaNumber1 => pluginParam1,
     *            criteriaNumber2 => pluginParam2,
     *            criteriaNumberN => pluginParamN
     *          )
     * )
     *
     */
    $pluginNeeded=Array();
    $pluginList=Array();
    foreach($_REQUEST['extraData'] as $key => $val)
    {
      $pluginNeeded[$val['owner']][$key]=$_REQUEST['extraData'][$key]['param'];
      $pluginList[$val['owner']]=$val['owner'];
    }

    /* for each needed plugin :
     *  - include the file
     *  - call the static public function getWhere
     */
    foreach($pluginNeeded as $key => $val)
    {
      if(array_key_exists($key, $registeredPlugin))
      {
        if(file_exists($registeredPlugin[$key]['fileName']))
        {
          include_once($registeredPlugin[$key]['fileName']);

          $tmpBuild['FROM'][]=call_user_func(Array('RBCallBack'.$key, 'getFrom'));
          $tmpBuild['JOIN'][]=call_user_func(Array('RBCallBack'.$key, 'getJoin'));

          foreach($val as $itemNumber => $param)
          {
            $tmpBuild['WHERE'][$itemNumber]=call_user_func(Array('RBCallBack'.$key, 'getWhere'), $param);
          }
        }
      }
    }


    /* build FROM
     *
     */
    $build['FROM']=implode(',', $tmpBuild['FROM']);
    unset($tmpBuild['FROM']);


    /* build WHERE
     */
    self::cleanArray($tmpBuild['WHERE']);
    if(count($tmpBuild['WHERE'])>0)
    {
      $build['WHERE']=' ('.self::buildGroup($_REQUEST[$_REQUEST['requestName']], $tmpBuild['WHERE'], $_REQUEST['operator'], ' AND ').') ';
    }
    unset($tmpBuild['WHERE']);


    /* for each plugin, adds jointure with the IMAGE table
     */
    self::cleanArray($tmpBuild['JOIN']);
    if(count($tmpBuild['JOIN'])>0)
    {
      $build['WHERE'].=' AND ('.implode(' AND ', $tmpBuild['JOIN']).') ';
    }
    unset($tmpBuild['JOIN']);

    self::cleanArray($tmpBuild['GROUPBY']);
    if(count($tmpBuild['GROUPBY'])>0)
    {
      $build['GROUPBY'].=' '.implode(', ', $tmpBuild['GROUPBY']).' ';
    }
    unset($tmpBuild['GROUPBY']);



    $sql=' FROM '.$build['FROM'];
    if($build['WHERE']!='')
    {
      $sql.=' WHERE '.$build['WHERE'];
    }
    if($build['GROUPBY']!='')
    {
      $sql.=' GROUP BY '.$build['GROUPBY'];
    }

    $sql.=" ORDER BY pit.id ";

    $requestNumber=self::getNewRequest($user['id']);

    $sql="INSERT INTO ".self::$tables['result_cache']." (SELECT DISTINCT $requestNumber, ".$build['SELECT']." $sql)";

    $result=pwg_query($sql);
    if($result)
    {
      $numberItems=pwg_db_changes($result);
      self::updateRequest($requestNumber, $numberItems, 0, implode(',', $pluginList));


      return("$requestNumber;".$numberItems);
    }

    return("0;0");
  }

  /**
   * return a page content. use the cache table to find request result
   *
   * @param Integer $requestNumber : the request number (from cache table)
   * @param Integer $pageNumber : the page to be returned
   * @param Integer $numPerPage : the number of items returned on a page
   * @return String : formatted HTML code
   */
  static private function getPage($requestNumber, $pageNumber, $numPerPage)
  {
    global $conf;
    $request=self::getRequest($requestNumber);

    if($request===false)
    {
      return("KO");
    }

    $limitFrom=$numPerPage*($pageNumber-1);

    $pluginNeeded=explode(',', $request['connected_plugin']);
    $registeredPlugin=self::getRegistered();

    $build=Array(
      'SELECT' => '',
      'FROM' => '',
      'WHERE' => '',
      'GROUPBY' => '',
    );
    $tmpBuild=Array(
      'SELECT' => Array(
        'RB_PIT' => "pit.id AS imageId, pit.name AS imageName, pit.path AS imagePath", // from the piwigo's image table
        'RB_PIC' => "GROUP_CONCAT(pic.category_id SEPARATOR ',') AS imageCategoriesId",     // from the piwigo's image_category table
        'RB_PCT' => "GROUP_CONCAT(CASE WHEN pct.name IS NULL THEN '' ELSE pct.name END SEPARATOR '#sep#') AS imageCategoriesNames,
                     GROUP_CONCAT(CASE WHEN pct.permalink IS NULL THEN '' ELSE pct.permalink END SEPARATOR '#sep#') AS imageCategoriesPLink,
                     GROUP_CONCAT(CASE WHEN pct.dir IS NULL THEN 'V' ELSE 'P' END) AS imageCategoriesDir",   //from the piwigo's categories table
      ),
      'FROM' => Array(
        // join rb result_cache table with piwigo's images table, joined with the piwigo's image_category table, joined with the categories table
        'RB' => "((".self::$tables['result_cache']." pgrc
                  RIGHT JOIN ".IMAGES_TABLE." pit
                  ON pgrc.image_id = pit.id)
                    RIGHT JOIN ".IMAGE_CATEGORY_TABLE." pic
                    ON pit.id = pic.image_id)
                       RIGHT JOIN piwigo_categories pct
                       ON pct.id = pic.category_id ",
      ),
      'WHERE' => Array(
        'RB' => "pgrc.id=".$requestNumber,
        ),
      'JOIN' => Array(),
      'GROUPBY' => Array(
        'RB' => "pit.id"
      )
    );

    /* for each needed plugin :
     *  - include the file
     *  - call the static public function getFrom, getJoin, getSelect
     */
    foreach($pluginNeeded as $key => $val)
    {
      if(array_key_exists($val, $registeredPlugin))
      {
        if(file_exists($registeredPlugin[$val]['fileName']))
        {
          include_once($registeredPlugin[$val]['fileName']);
          $tmpBuild['SELECT'][$val]=call_user_func(Array('RBCallBack'.$val, 'getSelect'));
          $tmpBuild['FROM'][$val]=call_user_func(Array('RBCallBack'.$val, 'getFrom'));
          $tmpBuild['JOIN'][$val]=call_user_func(Array('RBCallBack'.$val, 'getJoin'));
        }
      }
    }

    /* build SELECT
     *
     */
    $build['SELECT']=implode(',', $tmpBuild['SELECT']);

    /* build FROM
     *
     */
    $build['FROM']=implode(',', $tmpBuild['FROM']);
    unset($tmpBuild['FROM']);


    /* build WHERE
     */
    $build['WHERE']=implode(' AND ', $tmpBuild['WHERE']);
    unset($tmpBuild['WHERE']);


    /* for each plugin, adds jointure with the IMAGE table
     */
    self::cleanArray($tmpBuild['JOIN']);
    if(count($tmpBuild['JOIN'])>0)
    {
      $build['WHERE'].=' AND ('.implode(' AND ', $tmpBuild['JOIN']).') ';
    }
    unset($tmpBuild['JOIN']);

    self::cleanArray($tmpBuild['GROUPBY']);
    if(count($tmpBuild['GROUPBY'])>0)
    {
      $build['GROUPBY'].=' '.implode(', ', $tmpBuild['GROUPBY']).' ';
    }
    unset($tmpBuild['GROUPBY']);


    $imagesList=Array();

    $sql='SELECT '.$build['SELECT']
        .' FROM '.$build['FROM']
        .' WHERE '.$build['WHERE']
        .' GROUP BY '.$build['GROUPBY']
        .' ORDER BY pit.id '
        .' LIMIT '.$limitFrom.', '.$numPerPage;

    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        // affect standard datas
        $datas['imageThumbnail']=dirname($row['imagePath'])."/".$conf['dir_thumbnail']."/".$conf['prefix_thumbnail'].basename($row['imagePath']);
        $datas['imageId']=$row['imageId'];
        $datas['imagePath']=$row['imagePath'];
        $datas['imageName']=$row['imageName'];

        $datas['imageCategoriesId']=explode(',', $row['imageCategoriesId']);
        $datas['imageCategoriesNames']=explode('#sep#', $row['imageCategoriesNames']);
        $datas['imageCategoriesPLink']=explode('#sep#', $row['imageCategoriesPLink']);
        $datas['imageCategoriesDir']=explode(',', $row['imageCategoriesDir']);


        $datas['imageCategories']=Array();
        for($i=0;$i<count($datas['imageCategoriesId']);$i++)
        {
          $datas['imageCategories'][]=array(
            'id' => $datas['imageCategoriesId'][$i],
            'name' => $datas['imageCategoriesNames'][$i],
            'dirType' => $datas['imageCategoriesDir'][$i],
            'pLinks' => $datas['imageCategoriesPLink'][$i],
            'link'=> make_index_url(
                              array(
                                'category' => array(
                                  'id' => $datas['imageCategoriesId'][$i],
                                  'name' => $datas['imageCategoriesNames'][$i],
                                  'permalink' => $datas['imageCategoriesPLink'][$i])
                              )
                            )
          );
        }

        /* affect datas for each plugin
         *
         * each plugin have to format the data in an HTML code
         *
         * so, for each plugin :
         *  - look the attributes given in the SELECT clause
         *  - for each attributes, associate the returned value of the record
         *  - affect in datas an index equals to the plugin pluginName, with returned HTML code ; HTML code is get from a formatData function
         *
         * Example :
         *  plugin ColorStart provide 2 attributes 'csColors' and 'csColorsPct'
         *
         *  we affect to the $attributes var :
         *  $attributes['csColors'] = $row['csColors'];
         *  $attributes['csColorsPct'] = $row['csColorsPct'];
         *
         *  call the ColorStat RB callback formatData with the $attributes => the function return a HTML code ready to use in the template
         *
         *  affect $datas['ColorStat'] = $the_returned_html_code;
         *
         *
         */
        foreach($tmpBuild['SELECT'] as $key => $val)
        {
          if($key!='RB_PIT' && $key!='RB_PIC' && $key!='RB_PCT')
          {
            $tmp=explode(',', $val);

            $attributes=Array();

            foreach($tmp as $key2 => $val2)
            {
              $name=self::getAttribute($val2);
              $attributes[$name]=$row[$name];
            }

            $datas['plugin'][$key]=call_user_func(Array('RBCallBack'.$key, 'formatData'), $attributes);

            unset($tmp);
            unset($attributes);
          }
        }
        $imagesList[]=$datas;
        unset($datas);
      }
    }

    return(self::toHtml($imagesList));
    //return("get page : $requestNumber, $pageNumber, $numPerPage<br>$debug<br>$sql");
  }

  /**
   * remove all empty value from an array
   * @param Array a$array : the array to clean
   */
  static private function cleanArray(&$array)
  {
    foreach($array as $key => $val)
    {
      if(trim($val)=='') unset($array[$key]);
    }
  }

  /**
   * returns the alias for an attribute
   *
   *  item1                  => returns item1
   *  table1.item1           => returns item1
   *  table1.item1 AS alias1 => returns alias1
   *  item1 AS alias1        => returns alias1
   *
   * @param String $var : value ti examine
   * @return String : the attribute name
   */
  static private function getAttribute($val)
  {
    preg_match('/(?:(?:[A-Z0-9_]*)\.)?([A-Z0-9_]*)(?:\s+AS\s+([A-Z0-9_]*))?/i', trim($val), $result);
    if(array_key_exists(2, $result))
    {
      return($result[2]);
    }
    elseif(array_key_exists(1, $result))
    {
      return($result[1]);
    }
    else
    {
      return($val);
    }
  }


  /**
   * get a new request number and create it in the request table
   *
   * @param Integer $userId : id of the user
   * @return Integer : the new request number, -1 if something wrong appened
   */
  static private function getNewRequest($userId)
  {
    $sql="INSERT INTO ".self::$tables['request']." VALUES('', '$userId', '".date('Y-m-d H:i:s')."', 0, 0, '')";
    $result=pwg_query($sql);
    if($result)
    {
      return(pwg_db_insert_id());
    }
    return(-1);
  }

  /**
   * update request properties
   *
   * @param Integer $request_id : the id of request to update
   * @param Integer $numItems : number of items found in the request
   * @param Float $executionTime : time in second to execute the request
   * @param String $pluginList : list of used plugins
   * @return Boolean : true if request was updated, otherwise false
   */
  static private function updateRequest($requestId, $numItems, $executionTime, $pluginList)
  {
    $sql="UPDATE ".self::$tables['request']."
            SET num_items = $numItems,
                execution_time = $executionTime,
                connected_plugin = '$pluginList'
            WHERE id = $requestId";
    $result=pwg_query($sql);
    if($result)
    {
      return(true);
    }
    return(false);
  }

  /**
   * returns request properties
   *
   * @param Integer $request_id : the id of request to update
   * @return Array : properties for request, false if request doesn't exist
   */
  static private function getRequest($requestId)
  {
    $returned=false;
    $sql="SELECT user_id, date, num_items, execution_time, connected_plugin
          FROM ".self::$tables['request']."
          WHERE id = $requestId";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $returned=$row;
      }
    }
    return($returned);
  }


  /**
   * internal function used by the executeRequest function
   *
   * this function is called recursively
   *
   * @param Array $groupContent :
   * @param Array $items :
   * @return String : a where clause
   */
  static private function buildGroup($groupContent, $items, $groups, $operator)
  {
    $returned=Array();
    foreach($groupContent as $key => $val)
    {
      if(strpos($val['id'], 'iCbGroup')!==false)
      {
        preg_match('/[0-9]*$/i', $val['id'], $groupNumber);
        $returned[]=self::buildGroup($val['children'], $items, $groups, $groups[$groupNumber[0]]);
      }
      else
      {
        preg_match('/[0-9]*$/i', $val['id'], $itemNumber);
        $returned[]=" (".$items[$itemNumber[0]].") ";
      }
    }
    return('('.implode($operator, $returned).')');
  }


  /**
   * convert a list of images to HTML
   *
   * @param Array $imagesList : list of images id & associated datas
   * @return String : list formatted into HTML code
   */
  static protected function toHtml($imagesList)
  {
    global $template;

    $template->set_filename('result_items',
                dirname(dirname(__FILE__)).'/templates/GPCRequestBuilder_result.tpl');



    $template->assign('datas', $imagesList);

    return($template->parse('result_items', true));
  }


  /**
   * returns allowed (or not allowed) categories for a user
   *
   * used the USER_CACHE_TABLE if possible
   *
   * @param Integer $userId : a valid user Id
   * @return String : IN(...), NOT IN(...) or nothing if there is no restriction
   *                  for the user
   */
  public function getUserCategories($userId)
  {
/*
    $returned='';
    if($user['forbidden_categories']!='')
    {
      $returned=Array(
        'JOIN' => 'AND ('.IMAGE_CATEGORY.'.category_id NOT IN ('.$user['forbidden_categories'].') ) ',
        'FROM' => IMAGE_CATEGORY
      );


    }
    *
    *
    */
  }


  /**
   * check if this is a valid ajax request
   *
   * @return Boolean : true if this is a valide ajax request
   */
  static protected function checkAjaxRequest()
  {
    if(isset($_REQUEST['searchRequest']))
    {
      if($_REQUEST['searchRequest']=='execute')
      {
        if(!isset($_REQUEST['requestName'])) return(false);

        return(true);
      }

      if($_REQUEST['searchRequest']=='getPage')
      {
        if(!isset($_REQUEST['requestNumber'])) return(false);

        if(!isset($_REQUEST['page']))
        {
          $_REQUEST['page']=0;
        }
        if($_REQUEST['page']<0) $_REQUEST['page']=0;

        if(!isset($_REQUEST['numPerPage']))
        {
          $_REQUEST['numPerPage']=25;
        }

        if($_REQUEST['numPerPage']>100) $_REQUEST['numPerPage']=100;

        return(true);
      }

    }
    return(false);
  }



  /**
   * display search page
   */
  static public function displaySearchPage()
  {
    global $template, $lang;

    load_language('rbuilder.lang', GPC_PATH);

    $template->set_filename('gpc_search_page',
                dirname(dirname(__FILE__)).'/templates/GPCRequestBuilder_search.tpl');

    $registeredPlugin=self::getRegistered();
    $dialogBox=Array();
    foreach($registeredPlugin as $key=>$val)
    {
      if(array_key_exists($key, $registeredPlugin))
      {
        if(file_exists($registeredPlugin[$key]['fileName']))
        {
          include_once($registeredPlugin[$key]['fileName']);

          $dialogBox[]=Array(
            'handle' => $val['name'].'DB',
            'dialogBoxClass' => call_user_func(Array('RBCallBack'.$key, 'getInterfaceDBClass')),
            'label' => call_user_func(Array('RBCallBack'.$key, 'getInterfaceLabel')),
            'content' => call_user_func(Array('RBCallBack'.$key, 'getInterfaceContent')),
          );
        }
      }
    }

    $datas=Array(
      'dialogBox' => $dialogBox,
      'themeName' => $template->get_themeconf('name'),
    );

    $template->assign('datas', $datas);

    return($template->parse('gpc_search_page', true));
  } //displaySearchPage

}


?>
