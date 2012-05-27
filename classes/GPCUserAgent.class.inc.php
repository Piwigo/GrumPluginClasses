<?php
/* -----------------------------------------------------------------------------
  class name: GPCUserAgent
  class version  : 1.0.0
  plugin version : 3.5.2
  date           : 2012-05-25
  ------------------------------------------------------------------------------
  author: grum at piwog.org
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

  this classes provides base functions to parse User Agent string and retrieve
  properties: Browser, OS, Engine


  (static public) function parse($userAgent)

| release | date       |
| 1.0.0   | 2012-05-25 | * implementation of the class
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |

----------------------------------------------------------------------------- */

require_once('GPCUserAgent.const.inc.php');


class GPCUserAgent
{
  /**
   * returned result is ana array with the following properties:
   *  'os'              => Operating System id on which the browser run; integer => UA_OS_*
   *  'os_version'      => Version of the OS; string
   *
   *  'browser'         => browser id; integer => UA_BROWSER_*
   *  'brower_name'     => browser name; string
   *  'browser_type'    => browser type id (crawler, computer, mobile, ...); integer => UA_PROP_TYPE_*
   *  'browser_version' => browser version; string
   *
   *  'engine'          => engine used by the browser (gecko, khtml, ...); integer => UA_ENGINE_*
   *
   * @param String $userAgent: a user agent string
   * @return Array: array of properties
   */
  static public function parse($userAgent)
  {
    global $UA_BrowserInfo;

    $returned=array(
      'os' => '',
      'os_version' => '',

      'browser' => '',
      'browser_name' => '',
      'browser_type' => '',
      'browser_version' => '',

      'engine' => '',
      'engine_version' => ''
    );

    $bot=self::getBot($userAgent);
    if($bot['crawler']!=='')
    {
      $returned['browser']=$bot['crawler'];
      $returned['browser_name']=$bot['name'];
      $returned['browser_type']=UA_PROP_TYPE_CRAWLER;
      $returned['browser_version']=str_replace(array(';', '(', ')'), array('', '', ''), $bot['version']);
      return($returned);
    }

    $tmp=self::getBrowser($userAgent);
    $returned['browser']=$tmp['browser'];
    $returned['browser_name']=$tmp['name'];
    $returned['browser_type']=$UA_BrowserInfo[UA_DATA_BROWSER][$tmp['browser']][UA_PROP_TYPE];
    $returned['browser_version']=str_replace(array(';', '(', ')'), array('', '', ''), $tmp['version']);

    $tmp=self::getOs($userAgent);
    $returned['os']=$tmp['os'];
    $returned['os_version']=str_replace(array(';', '(', ')'), array('', '', ''), $tmp['version']);

    $tmp=self::getEngine($userAgent);
    $returned['engine']=$tmp['engine'];
    $returned['engine_version']=str_replace(array(';', '(', ')'), array('', '', ''), $tmp['version']);

    return($returned);
  }


  /**
   * return bot properties
   * if user agent is not a bot, properties are filled with string
   *
   * @param String $userAgent : user agent string to parse
   * @return Array
   */
  static private function getBot($userAgent)
  {
    $botList=array(
      '(008)\/(\S*)[\;]'                                => UA_BOT_008,
      '(abachobot)'                                     => UA_BOT_ABACHOBOT,
      '(accoona-ai-agent)(?:\/(\S*))*'                  => UA_BOT_ACCOONA_AI_AGENT,
      '(arachmo)'                                       => UA_BOT_ARACHMO,
      '(b-l-i-t-z-b-o-t)'                               => UA_BOT_BLITZBOT,
      '(blitzbot)'                                      => UA_BOT_BLITZBOT,
      '(bingbot)\/(\S*)[\;]'                            => UA_BOT_MICROSOFT_BING,
      '(bingbot)(?:\/(\S*))*'                           => UA_BOT_MICROSOFT_BING,
      '(charlotte)\/(\S*)[\;]'                          => UA_BOT_CHARLOTTE,
      '(charlotte)(?:\/(\S*))*'                         => UA_BOT_CHARLOTTE,
      '(cerberian drtrs)\sversion-(\S*)[\)]'            => UA_BOT_CERBERIAN_DRTRS,
      '(cosmos)\/(\S*)[\_]'                             => UA_BOT_COSMOS,
      '(covario-ids)(?:\/(\S*))*'                       => UA_BOT_COVARIO_IDS,
      '(dataparksearch)\/(\S*)[\-]'                     => UA_BOT_DATAPARKSEARCH,
      '(dataparksearch)(?:\/(\S*))*'                    => UA_BOT_DATAPARKSEARCH,
      '(gaisbot)\/(\S*)[\(\s]'                          => UA_BOT_GAISBOT,
      '(google)(?:\/(\S*))*'                            => UA_BOT_GOOGLE,
      '(googlebot)\/(\S*);'                             => UA_BOT_GOOGLE,
      '(googlebot)(?:\/(\S*))*'                         => UA_BOT_GOOGLE,
      '(googlebot-image)(?:\/(\S*))*'                   => UA_BOT_GOOGLE,
      '(gurujibot)\/(\S*);'                             => UA_BOT_GURUJIBOT,
      '(gurujibot)(?:\/(\S*))*'                         => UA_BOT_GURUJIBOT,
      '(holmes)(?:\/(\S*))*'                            => UA_BOT_HOLMES,
      '(htdig)(?:\/(\S*))*'                             => UA_BOT_HTDIG,
      '(ia_archiver)\/(\S*)'                            => UA_BOT_IA_ARCHIVER,
      '(ia_archiver)'                                   => UA_BOT_IA_ARCHIVER,
      '(iccrawler)'                                     => UA_BOT_ICCRAWLER,
      '(ichiro)\/(\S*)\('                               => UA_BOT_ICHIRO,
      '(ichiro)(?:\/(\S*))*'                            => UA_BOT_ICHIRO,
      '(baiduspider)'                                   => UA_BOT_BAIDU,
      '(msnbot)\/(\d+(?:\.\d+)*)'                       => UA_BOT_MSN_SEARCH,
      '(exabot)\/(\d+(?:\.\d+)*)'                       => UA_BOT_EXALEAD,
      '(gamespyhttp)\/(\d+(?:\.\d+)*)'                  => UA_BOT_GAMESPY,
      '(gigabot)\/(\d+(?:\.\d+)*)'                      => UA_BOT_GIGABLAST,
      '(inktomi)'                                       => UA_BOT_INKTOMI,
      '(Yahoo! Slurp)'                                  => UA_BOT_YAHOO_SEARCH,
      '\s*([a-z]*(?:bot|spyder|crawl|crawler|spider)[a-z]*)'  => UA_BOT_UNKNOWN
    );

    $returned=array(
      'crawler' => '',
      'name' => '',
      'version' => ''
    );

    $result=array();
    foreach($botList as $regExp => $crawler)
    {
      if(preg_match('/'.$regExp.'/i',$userAgent,$result))
      {
        $returned=array(
          'crawler' => $crawler,
          'name' => $result[1],
          'version' => isset($result[2])?$result[2]:''
        );
        return($returned);
      }
    }

    return($returned);
  }


  /**
   * return browser properties
   *
   * @param String $userAgent : user agent string to parse
   * @return Array
   */
  static private function getBrowser($userAgent)
  {
    $browserList=array(
      '(android).*applewebkit.*(?:version\/(\S*))(?:safari)?' => UA_BROWSER_ANDROID_WEBKIT,
      '(opera mini)\/(\S*)\/'                                 => UA_BROWSER_OPERA_MINI,
      '(opera mini)\/(\S*)'                                   => UA_BROWSER_OPERA_MINI,
      '(opera mobi).*(?:version\/(\S*))'                      => UA_BROWSER_OPERA_MOBILE,
      '(opera mobi)'                                          => UA_BROWSER_OPERA_MOBILE,
      '(blackberry).*version\/(\S*)'                          => UA_BROWSER_BLACKBERRY,
      '(blackberry\d*)\/(\S*)'                                => UA_BROWSER_BLACKBERRY,
      '(blackberry)'                                          => UA_BROWSER_BLACKBERRY,
      '(bunjalloo)\/(\S*)[\;\(]'                              => UA_BROWSER_BUNJALLOO,
      '(Fennec)\/(\S*)'                                       => UA_BROWSER_FENNEC,
      '(gobrowser)\/(\S*)'                                    => UA_BROWSER_GOBROWSER,
      '(gobrowser)'                                           => UA_BROWSER_GOBROWSER,
      '(iemobile)\/(\S*)[\;\)]'                               => UA_BROWSER_IEMOBILE,
      '(iemobile)\s(\S*)[\;\)]'                               => UA_BROWSER_IEMOBILE,
      '(iris)\/(\S*)'                                         => UA_BROWSER_IRIS,
      '(maemo browser)\s(\S*)'                                => UA_BROWSER_MAEMO_BROWSER,
      '(mib)\/(\S*)'                                          => UA_BROWSER_MIB,
      '(minimo)\/(\S*)'                                       => UA_BROWSER_MINIMO,
      '(netfront)\/(\S*)'                                     => UA_BROWSER_NETFRONT,
      '(SEMC-Browser)\/(\S*)'                                 => UA_BROWSER_SEMC,
      '(teashark)\/(\S*)'                                     => UA_BROWSER_TEASHARK,

      '(playstation 3);\s*(\S*)[\;\)]'                        => UA_BROWSER_PS3,
      '(playstation 3)'                                       => UA_BROWSER_PS3,
      '(PSP \(PlayStation Portable\));\s*(\S*)[\;\)]'         => UA_BROWSER_PSP,
      '(PSP \(PlayStation Portable\));\s*(\S*)'               => UA_BROWSER_PSP,

      '(opera)\/(\S*)\s.*(?:nintendo)'                        => UA_BROWSER_OPERA_WII,
      '(opera).*(?:version\/(\S*))'                           => UA_BROWSER_OPERA,
      '(opera)\/(\S*)'                                        => UA_BROWSER_OPERA,
      '(opera)\s(\S*)'                                        => UA_BROWSER_OPERA,
      '(shiira)\/(\S*)'                                       => UA_BROWSER_SHIIRA,
      '(shiira)'                                              => UA_BROWSER_SHIIRA,
      '(skyfire)\/(\S*)'                                      => UA_BROWSER_SKYFIRE,
      '(thunderbird)\/(\S*)'                                  => UA_BROWSER_THUNDERBIRD,
      '(namoroka)\/(\S*)'                                     => UA_BROWSER_NAMOROKA,
      '(minefield)\/(\S*)'                                    => UA_BROWSER_MINEFIELD,
      '(lunascape)\/(\S*)'                                    => UA_BROWSER_LUNASCAPE,
      '(lunascape)\s(\S*)[\;\)]'                              => UA_BROWSER_LUNASCAPE,
      '(lunascape)\s(.*)[\;\)]'                               => UA_BROWSER_LUNASCAPE,
      '(lunascape)'                                           => UA_BROWSER_LUNASCAPE,
      '(arora)\/(\S*)'                                        => UA_BROWSER_ARORA,
      '(camino)\/(\S*)'                                       => UA_BROWSER_CAMINO,
      '(kapiko)\/(\S*)'                                       => UA_BROWSER_KAPIKO,
      '(kazehakase)\/(\S*)'                                   => UA_BROWSER_KAZEHAKAZE,
      '(chromeplus)\/(\S*)'                                   => UA_BROWSER_CHROMEPLUS,
      '(chrome)\/(\S*)'                                       => UA_BROWSER_CHROME,
      '(epiphany)\/(\S*)'                                     => UA_BROWSER_EPIPHANY,
      '(galeon)\/(\S*)'                                       => UA_BROWSER_GALEON,
      '(orca)\/(\S*)'                                         => UA_BROWSER_ORCA,
      '(lobo)\/(\S*)'                                         => UA_BROWSER_LOBO,
      '(seamonkey)\/(\S*)'                                    => UA_BROWSER_SEAMONKEY,
      '(midori)\/(\S*)'                                       => UA_BROWSER_MIDORI,
      '(midori)'                                              => UA_BROWSER_MIDORI,
      '(maxthon)\/(\S*)'                                      => UA_BROWSER_MAXTHON,
      '(maxthon)\s?(\S*)[\);]'                                => UA_BROWSER_MAXTHON,
      '(iceape)\/(\S*)'                                       => UA_BROWSER_ICEAPE,
      '(iceweasel)\/(\S*)'                                    => UA_BROWSER_ICEWEASEL,
      '(netscape\d?)\/(\S*)'                                  => UA_BROWSER_NETSCAPE,
      '(navigator)\/(\S*)'                                    => UA_BROWSER_NETSCAPE,
      '(firebird)\/(\S*)'                                     => UA_BROWSER_FIREBIRD,
      '(firefox)\/(\S*)'                                      => UA_BROWSER_FIREFOX,
      '(msie)\s?(\S*)[\);]'                                   => UA_BROWSER_INTERNET_EXPLORER,
      '(konqueror)\/(\S*)[\);]'                               => UA_BROWSER_KONQUEROR,
      '(links)\s?\((\S*)[\);]'                                => UA_BROWSER_LINKS,
      '(lynx)\/(\S*)'                                         => UA_BROWSER_LYNX,
      '(safari)|version\/(\S*)'                               => UA_BROWSER_SAFARI,
      '(mozilla)\/(\S*)'                                      => UA_BROWSER_NETSCAPE
    );

    $returned=array(
      'browser' => UA_BROWSER_UNKNOWN,
      'name' => '',
      'version' => ''
    );

    $result=array();
    foreach($browserList as $regExp => $browser)
    {
      if(preg_match('/'.$regExp.'/i',$userAgent,$result))
      {
        //echo print_r($userAgent, true).print_r($result, true);
        if(is_array($result) and count($result)>2)
        {
          $returned=array(
            'browser' => $browser,
            'name' => $result[1],
            'version' => isset($result[2])?$result[2]:''
          );
        }
        else
        {
          $returned=array(
            'browser' => UA_BROWSER_UNKNOWN,
            'name' => '',
            'version' => ''
          );
        }
        return($returned);
      }
    }
    return($returned);
  }


  /**
   * return OS properties
   *
   * @param String $userAgent : user agent string to parse
   * @return Array
   */
  static private function getOs($userAgent)
  {
    $osList=array(
      '(android)\s?(\S*)[\;\)]'                               => UA_OS_ANDROID,
      '(android)\s?(\S*)'                                     => UA_OS_ANDROID,
      '(linux)'                                               => UA_OS_LINUX,
      '(freebsd)'                                             => UA_OS_FREEBSD,
      '(openbsd)'                                             => UA_OS_OPENBSD,
      '(netbsd)'                                              => UA_OS_NETBSD,
      '(sunos)'                                               => UA_OS_SUNOS,
      '(blackberry)\s?([^;.]*);'                              => UA_OS_BLACKBERRY,
      '(symbianos)'                                           => UA_OS_SYMBIAN_OS,
      '(symbos)'                                              => UA_OS_SYMBOS,
      '(windows)\s?(phone\sos\s\S*)[\;\)]'                    => UA_OS_WINDOWS,
      '(windows)\s?(phone\sos\s\S*)'                          => UA_OS_WINDOWS,
      '(windows)\s?(ce\s?\S*)[\;\)]'                          => UA_OS_WINDOWS,
      '(windows)\s?(ce\s?\S*)'                                => UA_OS_WINDOWS,
      '(windows)\s?(NT\s?\S*)[\;\)]'                          => UA_OS_WINDOWS,
      '(windows)\s?(NT\s?\S*)'                                => UA_OS_WINDOWS,
      '(windows)\s?(\S*)[\;\)]'                               => UA_OS_WINDOWS,
      '(windows)\s?(\S*)'                                     => UA_OS_WINDOWS,
      '(mac_powerpc)'                                         => UA_OS_MACINTOSH,
      '(cpu\siphone\sos)\s?(\S*)'                             => UA_OS_IOS,
      '(iphone|ipad|ipod);.*(?:cpu\sos\s(\S*))'               => UA_OS_IOS,
      '(mac\sos\sx)\s(\S*)[\;\)]'                             => UA_OS_MAC_OS_X,
      '(mac\sos\sx)\s(\S*)'                                   => UA_OS_MAC_OS_X,
      '(mac\sos\sx)'                                          => UA_OS_MAC_OS_X,
      '(nintendo\swii).*;\s([0-9\-]*);'                       => UA_OS_NINTENDO_WII,
      '(playstation\s3)'                                      => UA_OS_SONY_PS3,
      '(playstation\sportable)'                               => UA_OS_SONY_PSP,
      '(nintendo\sds)'                                        => UA_OS_NINTENDO_DS

    );

    $returned=array(
      'os' => UA_OS_UNKNOWN,
      'name' => '',
      'version' => ''
    );

    $result=array();
    foreach($osList as $regExp => $os)
    {
      if(preg_match('/'.$regExp.'/i',$userAgent,$result))
      {
        $returned=array(
          'os' => $os,
          'name' => $result[1],
          'version' => isset($result[2])?$result[2]:''
        );
        switch($os)
        {
          case UA_OS_WINDOWS:
            switch($returned['version'])
            {
              case 'NT 5.0':
                  $returned['version']='2000';
                break;
              case 'NT 5.1':
                  $returned['version']='XP';
                break;
              case 'NT 5.2':
                  $returned['version']='Server 2003';
                break;
              case 'NT 6.0':
                  $returned['version']='Vista';
                break;
              case 'NT 6.1':
                  $returned['version']='Seven';
                break;
              default;
                break;
            }
            break;
        }

        return($returned);
      }
    }

    return($returned);
  }

  /**
   * return engine properties
   *
   * @param String $userAgent : user agent string to parse
   * @return Array
   */
  static private function getEngine($userAgent)
  {
    $engineList=array(
      '(presto)\/(\S*)'           => UA_ENGINE_PRESTO,
      '(gecko)\/(\S*)'            => UA_ENGINE_GECKO,
      '(applewebkit)\/(\S*)'      => UA_ENGINE_APPLEWEBKIT,
      '(webkit)\/(\S*)'           => UA_ENGINE_WEBKIT,
      '(khtml)\/(\S*)'            => UA_ENGINE_KHTML,
      '(trident)\/(\S*)'          => UA_ENGINE_TRIDENT
    );

    $returned=array(
      'engine' => UA_ENGINE_UNKNOWN,
      'name' => '',
      'version' => ''
    );

    $result=array();
    foreach($engineList as $regExp => $engine)
    {
      if(preg_match('/'.$regExp.'/i',$userAgent,$result))
      {
        $returned=array(
          'engine' => $engine,
          'name' => $result[1],
          'version' => isset($result[2])?$result[2]:''
        );
        return($returned);
      }
    }

    return($returned);
  }


}


?>
