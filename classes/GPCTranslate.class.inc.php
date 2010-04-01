<?php

/* -----------------------------------------------------------------------------
  class name     : GPCTranslate
  class version  : 2.1.0
  plugin version : 3.0.0
  date           : 2010-03-31
  ------------------------------------------------------------------------------
  author: grum at piwigo.org
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------


  ------------------------------------------------------------------------------

  :: HISTORY

| release | date       |
| 2.1.0   | 2010/03/31 | * update class & functions names
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |

  ------------------------------------------------------------------------------


   class call API in HTML header, and provide a .js file manage API call
        >>  http://code.google.com/apis/ajaxlanguage/

    - constructor

   ---------------------------------------------------------------------- */
class GPCTranslate
{
  public function __construct()
  {
    add_event_handler('loc_end_page_header', array(&$this, 'loadJS'));
  }

  public function loadJS()
  {
    global $template;

    $googleload='
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="plugins/'.basename(dirname(__FILE__)).'/google_translate.js"></script>';

    $template->append('head_elements', $googleload);
  }

} //class

?>
