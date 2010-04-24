<?php

/* -----------------------------------------------------------------------------
  class name: GPCCss
  class version  : 3.0.0
  plugin version : 3.0.0
  date           : 2010-03-30

  ------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------

  :: HISTORY

| release | date       |
| 3.0.0   | 2010/03/30 | * Update class & function names
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |

  ------------------------------------------------------------------------------

   this classes provides base functions to manage css
    classe consider that $filename is under plugins/ directory


    - constructor Css($filename)
    - (public) function fileExists()
    - (public) function makeCSS($css)
    - (public) function applyCSS()
   ---------------------------------------------------------------------- */
class GPCCss
{
  private $filename;

  static public function applyGpcCss()
  {
    add_event_handler('loc_end_page_header', array('GPCCss', 'applyCSSFile'));
  }

  static public function applyCSSFile($fileName="")
  {
    global $template;

    if($fileName=="")
    {
      //if no filename given, load the gpc.css file
      $fileName=basename(dirname(dirname(__FILE__))).'/css/gpc.css';
      $template->append('head_elements', '<link rel="stylesheet" type="text/css" href="plugins/'.$fileName.'">');
    }
    elseif(file_exists($fileName))
    {
      $template->append('head_elements', '<link rel="stylesheet" type="text/css" href="plugins/'.basename(dirname($fileName))."/".basename($fileName).'">');
    }
  }

  public function __construct($filename)
  {
    $this->filename=$filename;
  }

  public function __destruct()
  {
    unset($this->filename);
  }

  /*
    make the css file
  */
  public function makeCSS($css)
  {
    if($css!="")
    {
      $handle=fopen($this->filename, "w");
      if($handle)
      {
        fwrite($handle, $css);
        fclose($handle);
      }
    }
  }

  /*
    return true if css file exists
  */
  public function fileExists()
  {
    return(file_exists($this->filename));
  }

  /*
    put a link in the template to load the css file
    this function have to be called in a 'loc_end_page_header' trigger

    if $text="", insert link to css file, otherwise insert directly a <style> markup
  */
  public function applyCSS()
  {
    global $template;

    GPCCss::applyCSSFile($this->filename);
  }


} //class

?>
