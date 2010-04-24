<?php

/* -----------------------------------------------------------------------------
  class name     : GPCCore
  class version  : 1.1.0
  plugin version : 3.1.0
  date           : 2010-03-30
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
|         |            |
|         |            |
|         |            |
|         |            |

  ------------------------------------------------------------------------------
    no constructor, only static function are provided
    - static function loadConfig
    - static function saveConfig
    - static function deleteConfig
    - static function getRegistered
    - static function getModulesInfos
    - static function register
    - static function unregister
    - static function BBtoHTML
   ---------------------------------------------------------------------- */



class GPCCore
{
  static public $pluginName = "GPCCore";

  /* ---------------------------------------------------------------------------
   * grum plugin classes informations functions
   * -------------------------------------------------------------------------*/
  static public function getModulesInfos()
  {
    return(
      Array(
        Array('name' => "CommonPlugin", 'version' => "2.1.0"),
        Array('name' => "GPCAjax", 'version' => "3.0.0"),
        Array('name' => "GPCCore", 'version' => "1.1.0"),
        Array('name' => "GPCCss", 'version' => "3.0.0"),
        Array('name' => "GPCPagesNavigations", 'version' => "2.0.0"),
        Array('name' => "GPCPublicIntegration", 'version' => "2.0.0"),
        Array('name' => "GPCTables", 'version' => "1.5.0"),
        Array('name' => "GPCTabSheet", 'version' => "1.0.0"),
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
      '/\[url\]([\w]+?:\/\/[^ \"\n\r\t<]*?)\[\/url\]/ism',
      '/\[url=([\w]+?:\/\/[^ \"\n\r\t<]*?)\](.*?)\[\/url\]/ism',
      '/\n{0,1}\[ul\]\n{0,1}/im',
      '/\n{0,1}\[\/ul\]\n{0,1}/im',
      '/\n/im',
    );
    $replacements = Array(
      '<li>\1</li>',
      '<b>\1</b>',
      '<i>\1</i>',
      '<a href="\1">\1</a>',
      '<a href="\1">\2</a>',
      '<ul>',
      '</ul>',
      '<br>',
    );

    return(preg_replace($patterns, $replacements, $text));
  }

} //class

?>
