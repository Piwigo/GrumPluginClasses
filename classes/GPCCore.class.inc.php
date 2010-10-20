<?php

/* -----------------------------------------------------------------------------
  class name     : GPCCore
  class version  : 1.3.1
  plugin version : 3.3.2
  date           : 2010-10-20
  ------------------------------------------------------------------------------
  author: grum at piwigo.org
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

  :: HISTORY

| release | date       |
| 1.0.0   | 2010/03/30 | * Update class & function names
|         |            |
| 1.1.0   | 2010/03/30 | * add the BBtoHTML function
|         |            |
| 1.2.0   | 2010/07/28 | * add the loadConfigFromFile function
|         |            |
| 1.3.0   | 2010/10/13 | * add the addHeaderCSS, addHeaderJS functions
|         |            |
| 1.3.1   | 2010/10/20 | * applyHeaderItems functions implemented with an
|         |            |   higher priority on the 'loc_begin_page_header' event
|         |            |
|         |            | * implement the getUserLanguageDesc() function, using
|         |            |   extended description function if present
|         |            |
|         |            | * implement the getPiwigoSystemPath function
|         |            |
|         |            |

  ------------------------------------------------------------------------------
    no constructor, only static function are provided
    - static function loadConfig
    - static function loadConfigFromFile
    - static function saveConfig
    - static function deleteConfig
    - static function getRegistered
    - static function getModulesInfos
    - static function register
    - static function unregister
    - static function BBtoHTML
    - static function addHeaderCSS
    - static function addHeaderJS
    - static function getUserLanguageDesc
    - static function getPiwigoSystemPath
    - static function formatOctet
   ---------------------------------------------------------------------- */



class GPCCore
{
  static private $piwigoSystemPath;

  static public $pluginName = "GPCCore";
  static protected $headerItems = array(
    'css' => array(),
    'js'  => array()
  );

  static public function init()
  {
    self::$piwigoSystemPath=dirname(dirname(dirname(dirname(__FILE__))));
  }

  /* ---------------------------------------------------------------------------
   * grum plugin classes informations functions
   * -------------------------------------------------------------------------*/
  static public function getModulesInfos()
  {
    return(
      Array(
        Array('name' => "CommonPlugin", 'version' => "2.2.0"),
        Array('name' => "GPCAjax", 'version' => "3.0.0"),
        Array('name' => "GPCCore", 'version' => "1.2.0"),
        Array('name' => "GPCCss", 'version' => "3.0.0"),
        Array('name' => "GPCPagesNavigations", 'version' => "2.0.0"),
        Array('name' => "GPCPublicIntegration", 'version' => "2.0.0"),
        Array('name' => "GPCRequestBuilder", 'version' => "1.1.0"),
        Array('name' => "GPCTables", 'version' => "1.5.0"),
        Array('name' => "GPCTabSheet", 'version' => "1.1.0"),
        Array('name' => "GPCTranslate", 'version' => "2.1.0"),
        Array('name' => "GPCUsersGroups", 'version' => "2.0.0"),
      )
    );
  }


  /* ---------------------------------------------------------------------------
   * register oriented functions
   * -------------------------------------------------------------------------*/

  /**
   * register a plugin using GPC
   *
   * @param String $pluginName : the plugin name
   * @param String $release : the plugin version like "2.0.0"
   * @param String $GPCNeed : the minimal version of GPC needed by the plugin to
   *                          work
   * @return Boolean : true if registering is Ok, otherwise false
   */
  static public function register($plugin, $release, $GPCneeded)
  {
    $config=Array();
    if(self::loadConfig(self::$pluginName, $config))
    {
      $config['registered'][$plugin]=Array(
        'name' => $plugin,
        'release' => $release,
        'needed' => $GPCneeded,
        'date' => date("Y-m-d"),
      );
      return(self::saveConfig(self::$pluginName, $config));
    }
    return(false);
  }

  /**
   * unregister a plugin using GPC
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
    if(self::loadConfig(self::$pluginName, $config))
    {
      if(array_key_exists('registered', $config))
      {
        if(array_key_exists($plugin, $config['registered']))
        {
          unset($config['registered'][$plugin]);
          return(self::saveConfig(self::$pluginName, $config));
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
    if(self::loadConfig(self::$pluginName, $config))
    {
      if(array_key_exists('registered', $config))
      {
        return($config['registered']);
      }
    }
    return(Array());
  }



  /* ---------------------------------------------------------------------------
   * config oriented functions
   * -------------------------------------------------------------------------*/

  /**
   *  load config from CONFIG_TABLE into an array
   *
   * @param String $pluginName : the plugin name, must contain only alphanumerical
   *                             character
   * @param Array $config : array, initialized or not with default values ; the
   *                        config values are loaded in this value
   * @return Boolean : true if config is loaded, otherwise false
   */
  static public function loadConfig($pluginName, &$config=Array())
  {
    if(!is_array($config))
    {
      return(false);
    }

    $sql="SELECT value FROM ".CONFIG_TABLE."
          WHERE param = '".$pluginName."_config'";
    $result=pwg_query($sql);
    if($result)
    {
      $row=pwg_db_fetch_row($result);
      if(is_string($row[0]))
      {
        $configValues = unserialize($row[0]);
        reset($configValues);
        while (list($key, $val) = each($configValues))
        {
          if(is_array($val))
          {
            foreach($val as $key2 => $val2)
            {
              $config[$key][$key2]=$val2;
            }
          }
          else
          {
            $config[$key] =$val;
          }
        }
      }
      return(true);
    }
    return(false);
  }

  /**
   *  load config from a file into an array
   *
   *  note : the config file is a PHP file one var $conf used as an array,
   *  like the piwigo $conf var
   *
   * @param String $fileName : the file name
   * @param Array $config : array, initialized or not with default values ; the
   *                        config values are loaded in this value
   * @return Boolean : true if config is loaded, otherwise false
   */
  static public function loadConfigFromFile($fileName, &$config=Array())
  {
    $conf=array();

    if(!is_array($config) or !file_exists($fileName))
    {
      return(false);
    }

    include_once($fileName);

    foreach($conf as $key=>$val)
    {
      $config[$key]=$val;
    }
    return(true);
  }


  /**
   * save var $my_config into CONFIG_TABLE
   *
   * @param String $pluginName : the plugin name, must contain only alphanumerical
   *                             character
   * @param Array $config : array of configuration values
   * @return Boolean : true if config is saved, otherwise false
   */
  static public function saveConfig($pluginName, $config)
  {
    $sql="REPLACE INTO ".CONFIG_TABLE."
           VALUES('".$pluginName."_config', '"
           .serialize($config)."', '')";
    $result=pwg_query($sql);
    if($result)
    { return true; }
    else
    { return false; }
  }

  /**
   * delete config from CONFIG_TABLE
   *
   * @param String $pluginName : the plugin name, must contain only alphanumerical
   *                             character
   * @return Boolean : true if config is deleted, otherwise false
   */
  static public function deleteConfig($pluginName)
  {
    $sql="DELETE FROM ".CONFIG_TABLE."
          WHERE param='".$pluginName."_config'";
    $result=pwg_query($sql);
    if($result)
    { return true; }
    else
    { return false; }
  }


  /**
   * convert (light) BB tag to HTML tag
   *
   * all BB codes are not recognized, only :
   *  - [ul] [/ul]
   *  - [li] [/li]
   *  - [b] [/b]
   *  - [i] [/i]
   *  - [url] [/url]
   *  - carriage return is replaced by a <br>
   *
   * @param String $text : text to convert
   * @return String : BB to HTML text
   */
  static public function BBtoHTML($text)
  {
    $patterns = Array(
      '/\[li\](.*?)\[\/li\]\n*/im',
      '/\[b\](.*?)\[\/b\]/ism',
      '/\[i\](.*?)\[\/i\]/ism',
      '/\[p\](.*?)\[\/p\]/ism',
      '/\[url\]([\w]+?:\/\/[^ \"\n\r\t<]*?)\[\/url\]/ism',
      '/\[url=([\w]+?:\/\/[^ \"\n\r\t<]*?)\](.*?)\[\/url\]/ism',
      '/\n{0,1}\[ul\]\n{0,1}/im',
      '/\n{0,1}\[\/ul\]\n{0,1}/im',
      '/\n{0,1}\[ol\]\n{0,1}/im',
      '/\n{0,1}\[\/ol\]\n{0,1}/im',
      '/\n/im',
    );
    $replacements = Array(
      '<li>\1</li>',
      '<b>\1</b>',
      '<i>\1</i>',
      '<p>\1</p>',
      '<a href="\1">\1</a>',
      '<a href="\1">\2</a>',
      '<ul>',
      '</ul>',
      '<ol>',
      '</ol>',
      '<br>',
    );

    return(preg_replace($patterns, $replacements, $text));
  }

  /**
   * used to add a css file in the header
   *
   * @param String $id : a unique id for the file
   * @param String $file : the css file
   */
  static public function addHeaderCSS($id, $file)
  {
    if(!array_key_exists($file, self::$headerItems['css']))
    {
      self::$headerItems['css'][$id]=$file;
    }
  }
  static public function addHeaderJS($id, $file)
  {
    global $template;

    if(!isset($template->known_scripts)) $template->known_scripts=array();

    if(!array_key_exists($id,  $template->known_scripts) and !array_key_exists($file, self::$headerItems['js']))
    {
     $template->known_scripts[$id]=$file;
     self::$headerItems['js'][$id]=$file;
    }
  }

  /**
   * declared as public to be accessible by the event manager, but this funcion
   * is not aimed to be used directly
   */
  static public function applyHeaderItems()
  {
    global $template;

    foreach(self::$headerItems['css'] as $file)
    {
      $template->append('head_elements', '<link rel="stylesheet" type="text/css" href="'.$file.'"/>');
    }

    foreach(self::$headerItems['js'] as $file)
    {
      $template->append('head_elements', '<script type="text/javascript" src="'.$file.'"></script>');
    }
  }

  /**
   * use the extended description get_user_language_desc() function if exist
   * otherwise returns the value
   *
   * @param String $value : value to translate
   * @return String : translated value
   */
  static public function getUserLanguageDesc($value)
  {
    if(function_exists('get_user_language_desc'))
    {
      return(get_user_language_desc($value));
    }
    else
    {
      return($value);
    }
  }

  /**
   * returns the piwigo system path
   * @return String
   */
  static public function getPiwigoSystemPath()
  {
    return(self::$piwigoSystemPath);
  }


 /**
  * formats a file size into a human readable size
  *
  * @param String $format : "A"  : auto
  *                         "Ai" : auto (io)
  *                         "O"  : o
  *                         "K"  : Ko
  *                         "M"  : Mo
  *                         "G"  : Go
  *                         "Ki" : Kio
  *                         "Mi" : Mio
  *                         "Gi" : Gio
  * @param String $thsep : thousand separator
  * @param Integer $prec : number of decimals
  * @param Bool $visible : display or not the unit
  * @return String : a formatted file size
  */
 static public function formatOctet($octets, $format="Ai", $thsep="", $prec=2, $visible=true)
 {
  if($format=="Ai")
  {
   if($octets<1024)
   { $format="O"; }
   elseif($octets<1024000)
   { $format="Ki"; }
   elseif($octets<1024000000)
   { $format="Mi"; }
   else
   { $format="Gi"; }
  }
  elseif($format=="A")
  {
   if($octets<1000)
   { $format="O"; }
   elseif($octets<1000000)
   { $format="Ki"; }
   elseif($octets<1000000000)
   { $format="Mi"; }
   else
   { $format="Gi"; }
  }

  switch($format)
  {
   case "O":
    $unit="o"; $div=1;
    break;
   case "K":
    $unit="Ko"; $div=1000;
    break;
   case "M":
    $unit="Mo"; $div=1000000;
    break;
   case "G":
    $unit="Go"; $div=1000000000;
    break;
   case "Ki":
    $unit="Kio"; $div=1024;
    break;
   case "Mi":
    $unit="Mio"; $div=1024000;
    break;
   case "Gi":
    $unit="Gio"; $div=1024000000;
    break;
  }

  $returned=number_format($octets/$div, $prec, '.', $thsep);
  if($visible) $returned.=' '.$unit;
  return($returned);
 } //function formatOctet


} //class

add_event_handler('loc_begin_page_header', array('GPCCore', 'applyHeaderItems'), 10);

GPCCore::init();

?>
