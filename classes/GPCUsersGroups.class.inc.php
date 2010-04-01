<?php

/* -----------------------------------------------------------------------------
  class name: GPCAllowedAccess, GPCGroups, GPCUsers
  class version  : 2.0.0
  plugin version : 3.0.0
  date           : 2010-03-30
  ------------------------------------------------------------------------------
  author: grum at piwog.org
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to manage users/groups access
  groups and users classes extends GPCAllowedAccess classes

    - constructor GPCAllowedAccess($alloweds="")
    - constructor groups($alloweds="")
    - constructor users($alloweds="")
    - (public) function getList()
    - (public) function setAllowed($id, $allowed)
    - (public) function setAlloweds()
    - (public) function getAlloweds($return_type)
    - (public) function isAllowed($id)
    - (public) function htmlView($sep=", ", $empty="")
    - (public) function htmlForm($basename)
    - (private) function initList()


| release | date       |
| 1.1.0   | 2009/11/29 | * add 'webmaster' status for users
|         |            |
| 2.0.0   | 2010/03/28 | * Uses piwigo pwg_db_* functions instead of mysql_*
|         |            |   functions
|         |            | * update classes & functions names
|         |            |
|         |            |

   ---------------------------------------------------------------------- */
class GPCAllowedAccess
{
  public $access_list;

  /*
    constructor initialize the groups_getListgetListlist
  */
  public function __construct($alloweds = "")
  {
    $this->initList();
    $this->setAlloweds($alloweds);
  }

  public function __destruct()
  {
    unset($this->access_list);
  }

  /*
    initialize the groups list
  */
  protected function initList()
  {
    $this->access_list=array();
  }

  /*
    returns list (as an array)
  */
  function getList()
  {
    return($this->access_list);
  }

  /*
    set element an allowed state
  */
  function setAllowed($id, $allowed)
  {
    if(isset($this->access_list[$id]))
    {
      $this->access_list[$id]['allowed']=$allowed;
    }
  }

  /*
    set a group enabled/disabled state
  */
  function setState($id, $enabled)
  {
    if(isset($this->access_list[$id]))
    {
      $this->access_list[$id]['enabled']=$enabled;
    }
  }

  /*
    set alloweds list
    $list is string of id, separated with "/"
  */
  function setAlloweds($list)
  {
    $alloweds=explode("/", $list);
    $alloweds=array_flip($alloweds);
    foreach($this->access_list as $key => $val)
    {
      if(isset($alloweds[$key]))
      {
        $this->access_list[$key]['allowed']=true;
      }
      else
      {
        $this->access_list[$key]['allowed']=false;
      }
    }
  }

  /*
    get alloweds list
    return a string of groups, separated with "/"
  */
  function getAlloweds($return_type = 'name')
  {
    $returned="";
    foreach($this->access_list as $key => $val)
    {
      if($val['allowed'])
      { $returned.=$val[$return_type]."/"; }
    }
    return($returned);
  }


  /*
    returns true if is allowed
  */
  function isAllowed($id)
  {
    if(isset($this->access_list[$id]))
    { return($this->access_list[$id]['allowed']); }
    else
    { return(false); }
  }

  /*
    returns true if all or one is allowed
      ids is an array
  */
  function areAllowed($ids, $all=false)
  {
    foreach($ids as $val)
    {
      if($all)
      {
        if(!$this->isAllowed($val))
        {
          return(false);
        }
      }
      else
      {
        if($this->isAllowed($val))
        {
          return(true);
        }
      }
    }
    return(false);
  }

  /*
    returns an HTML list with label rather than id
  */
  function htmlView($sep=", ", $empty="")
  {
    $returned="";
    foreach($this->access_list as $key => $val)
    {
      if($val['allowed'])
      {
        if($returned!="")
        {
          $returned.=$sep;
        }
        $returned.=$val['name'];
      }
    }
    if($returned=="")
    {
      $returned=$empty;
    }
    return($returned);
  }
  /*
    returns a generic HTML form to manage the groups access
  */
  function htmlForm($basename)
  {
    /*
    <!-- BEGIN allowed_group_row -->
    <label><input type="checkbox" name="fmypolls_att_allowed_groups_{allowed_group_row.ID}" {allowed_group_row.CHECKED}/>&nbsp;{allowed_group_row.NAME}</label>
    <!-- END allowed_group_row -->
    */
    $text='';
    foreach($this->access_list as $key => $val)
    {
      if($val['allowed'])
      {
        $checked=' checked';
      }
      else
      {
        $checked='';
      }

      if($val['enabled'])
      {
        $enabled='';
      }
      else
      {
        $enabled=' disabled';
      }

      $text.='<label><input type="checkbox" name="'.$basename.$val['id'].'" '.$checked.$enabled.'/>
          &nbsp;'.$val['name'].'</label>&nbsp;';
    }
    return($text);
  }
} //GPCAllowedAccess








/* ----------------------------------------------------------------------
   this class provides base functions to manage groups access
    initList redefined to initialize access_list from database GROUPS
   ---------------------------------------------------------------------- */
class GPCGroups extends GPCAllowedAccess
{
  /*
    initialize the groups list
  */
  protected  function initList()
  {
    $this->access_list=array();
    $sql="SELECT id, name FROM ".GROUPS_TABLE." ORDER BY name";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $this->access_list[$row['id']] =
                   array('id' => $row['id'],
                         'name' => $row['name'],
                         'allowed' => false,
                         'enabled' => true);
      }
    }
  }
}








/* -----------------------------------------------------------------------------
   this class provides base functions to manage users access
----------------------------------------------------------------------------- */
class GPCUsers extends GPCAllowedAccess
{
  /*
    constructor
  */
  public function __construct($alloweds = "")
  {
    parent::__construct($alloweds);
    $this->setState('admin', false);
    $this->setAllowed('admin', true);
  }

  /*
    initialize the groups list
  */
  protected function initList()
  {
    $users_list = array('guest', 'generic', 'normal', 'webmaster', 'admin');
    $this->access_list=array();
    foreach($users_list as $val)
    {
      $this->access_list[$val] =
                  array('id' => $val,
                        'name' => l10n('user_status_'.$val),
                        'allowed' => false,
                        'enabled' => true);
    }
  }
} //class users



?>
