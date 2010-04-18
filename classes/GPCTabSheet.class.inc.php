<?php
/* -----------------------------------------------------------------------------
  class name     : GPCTabSheet
  class version  : 1.0.0
  plugin version : 3.0.2
  date           : 2010-04-18
  ------------------------------------------------------------------------------
  author: grum at piwigo.org
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

  :: HISTORY

| release | date       |
| 1.0.0   | 2010/04/18 | * create class
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |

  ------------------------------------------------------------------------------

   this class provides base functions to manage tabsheet navigation

   this class extends the Piwigo tabsheet class

    - constructor GPCPagesNavigation()
    - (public) function setNbItems($nbitems)
    - (public) function getNbItems()
    - (public) function setNbItemsPerPage($nbitems)
    - (public) function getNbItemsPerPage()
    - (public) function getNbPages()
    - (public) function setCurrentPage($page)
    - (public) function getCurrentPage()
    - (public) function setBaseUrl($url)
    - (public) function getBaseUrl()
    - (public) function setOptions($var)
    - (public) function getOptions()
    - (public) function makeNavigation()
    - (private) function calcNbPages()
   ---------------------------------------------------------------------- */

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

class GPCTabSheet extends tabsheet
{
  protected $classes;
  protected $id;
  protected $tplFile;
  /*
    $name is the tabsheet's name inside the template .tpl file
    $titlename in the template is affected by $titlename value
  */
  public function __construct($name = 'TABSHEET', $titlename = 'TABSHEET_TITLE', $classes="", $id="", $tplFile="")
  {
    parent::tabsheet($name, $titlename);
    $this->classes=$classes;
    $this->id=$id;
    $this->setTplFile($tplFile);
  }


  public function setClasses($classes)
  {
    $this->classes=$classes;
    return($this->classes);
  }

  public function getClasses()
  {
    return($this->classes);
  }

  public function setId($id)
  {
    $this->id=$id;
    return($this->id);
  }

  public function getId()
  {
    return($this->id);
  }

  public function setTplFile($fileName)
  {
    if(file_exists($fileName) or $fileName=="")
    {
      $this->tplFile=$fileName;
    }
    return($this->tplFile);
  }

  public function getTplFile()
  {
    return($this->tplFile);
  }


  /*
   * Build TabSheet and assign this content to current page
   *
   * Fill $this->$name {default value = TABSHEET} with HTML code for tabsheet
   * Fill $this->titlename {default value = TABSHEET_TITLE} with formated caption of the selected tab
   */
  function assign()
  {
    global $template;

    if($this->tplFile=="")
    {
      $tplFile=dirname(dirname(__FILE__)).'/templates/GPCTabSheet.tpl';
    }
    else
    {
      $tplFile=$this->tplFile;
    }

    $template->set_filename('tabsheet', $tplFile);
    $template->assign('tabsheet', $this->sheets);
    $template->assign('tabsheet_selected', $this->selected);

    $selected_tab = $this->get_selected();

    if (isset($selected_tab))
    {
      $template->assign(
        array($this->titlename => '['.$selected_tab['caption'].']'));
    }

    if($this->classes!="") $template->assign('tabsheet_classes', $this->classes);
    if($this->id!="") $template->assign('tabsheet_id', $this->id);

    $template->assign_var_from_handle($this->name, 'tabsheet');
    $template->clear_assign('tabsheet');
  }
}

?>
