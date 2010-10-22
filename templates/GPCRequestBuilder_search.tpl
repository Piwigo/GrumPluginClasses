{known_script id="jquery.ui" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.core.packed.js"}
{known_script id="jquery.ui.dialog" src=$ROOT_URL|@cat:"themes/default/js/ui/packed/ui.dialog.packed.js"}
{known_script id="jquery.tipTip" src=$ROOT_URL|@cat:"themes/default/js/plugins/jquery.tipTip.minified.js"}

{known_script id="gpc.pagesNavigator" src=$ROOT_URL|@cat:"plugins/GrumPluginClasses/js/pagesNavigator.packed.js"}


{literal}
<script type="text/javascript">
var cb=null;

  interfaceManager = function(optionsToSet)
  {
    var pn=null,
        requestNumber=0,
        options =
          {
            requestCriterionsVisible:'',
            requestCriterionsHidden:'',
            requestResult:'',
            requestResultContent:'',
            requestResultNfo:'',
            requestResultPagesNavigator:'',
            requestResultRequestNumber:0,
            onPageChange:null,
            numberPerPage:30,
          };

    /**
     *
     */
    this.doAction = function(fct)
    {
      switch(fct)
      {
        case 'queryResult':
          /* function 'queryResult' : when query is executed, prepare the interface
           */
          if(arguments.length==3)
          {
            displayQueryResult(arguments[1], arguments[2]);
          }
          break;
        case 'queryPage':
          /* function 'queryPage' : display returned page
           */
          if(arguments.length==3)
          {
            displayQueryPage(arguments[1], arguments[2]);
          }
          break;
        case 'show':
          /* function 'show' : show/hide the query/result
           */
          if(arguments.length==2)
          {
            show(arguments[1]);
          }
          break;
        case 'setOptions':
          /* function 'setOptions' : allows to set options after the object was
           *                         created
           */
          if(arguments.length==2)
          {
            setOptions(arguments[1]);
          }
          break;
        case 'fillCaddie':
          /* function 'fillCaddie' : allows to fill the caddie with the search result
           *
           */
          if(arguments.length==2)
          {
            fillCaddie(arguments[1], this.getRequestNumber());
          }
          break;
      }
    }

    /**
     * returns the current request number
     */
    this.getRequestNumber = function ()
    {
      return(requestNumber);
    }

    /**
     * returns the number of items per page
     */
    this.getNumberPerPage = function ()
    {
      return(options.numberPerPage);
    }

    /**
     * this function show/hide the different panels
     *  'buildQuery'  : hide the result panel and display the panel to build query
     *  'resultQuery' : hide the panel to build query and display the result panel
     */
    var show = function(mode)
    {
      switch(mode)
      {
        case 'buildQuery':
          $('.'+options.requestCriterionsVisible).css('display', 'block');
          $('.'+options.requestCriterionsHidden).css('display', 'none');
          $('.'+options.requestResult).css('display', 'none');
          break;
        case 'resultQuery':
          $('#iResultQueryContent').html("<br><img class='waitingResult' src='./plugins/GrumPluginClasses/icons/processing.gif'>");
          $('.'+options.requestCriterionsVisible).css('display', 'none');
          $('.'+options.requestCriterionsHidden).css('display', 'block');
          $('.'+options.requestResult).css('display', 'block');
          break;
      }
    }

    /**
     * this function display the number of items found and prepare the page
     * navigator
     *
     * @param String nfo : 2 information separated with a semi-colon ';'
     *                      requestNumber;numberOfItems
     */
    var displayQueryResult = function (isSuccess, nfo)
    {
      if(isSuccess)
      {
        nfo=nfo.split(';');

        requestNumber=nfo[0];
        $('#iResultQueryNfo').html(nfo[1]);
        pn.doAction('setOptions', { numberItem:nfo[1], defaultPage:1 } );
        show('resultQuery');
      }
      else
      {
        //$('#'+options.requestResultContent).html("");
        show('buildQuery');
        alert('{/literal}{"gpc_something_is_wrong_on_the_server_side"|@translate}{literal}');
      }
    }


    /**
     * this function display the number of items found and prepare the page
     * navigator
     *
     * @param String nfo : 2 information separated with a semi-colon ';'
     *                      requestNumber;numberOfItems
     */
    var displayQueryPage = function (isSuccess, nfo)
    {
      if(isSuccess)
      {
        $('#iResultQueryContent').html(nfo);
        $('.tiptip').tipTip(
          {
            'delay' : 0,
            'fadeIn' : 0,
            'fadeOut' : 0,
            'edgeOffset' : 5,
          }
        );
      }
      else
      {
        alert('{/literal}{"gpc_something_is_wrong_on_the_server_side"|@translate}{literal}');
      }
    }


    /**
     *
     * @param Object optionsToSet : set the given options
     */
    var setOptions = function(optionsToSet)
    {
      if(typeof optionsToSet=='object')
      {
        options = jQuery.extend(options, optionsToSet);
      }
    }

    /**
     * initialize the object
     */
    var init = function (optionsToSet)
    {
      setOptions(optionsToSet);

      pn = new pagesNavigator(options.requestResultPagesNavigator,
        {
          itemPerPage:options.numberPerPage,
          displayNumPage:9,
          classActive:'pnActive{/literal}{$datas.themeName}{literal}',
          classInactive:'pnInactive{/literal}{$datas.themeName}{literal}',
          onPageChange: function (page)
            {
              if(options.onPageChange!=null && jQuery.isFunction(options.onPageChange))
              {
                options.onPageChange(requestNumber, page, options.numberPerPage);
              }
            },
        }
      );

      requestNumber=options.requestResultRequestNumber;
    }

    /**
     * fill the caddie with the search results
     * @param String mode : 'add' or 'fill'
     */
    var fillCaddie = function (mode, requestNumber)
    {
      $('#iMenuCaddieImg').css('display', 'inline-block');
      $('#iMenuCaddieItems ul').css('display', 'none');

      $.ajax(
        {
          type: "POST",
          url: "plugins/GrumPluginClasses/gpc_ajax.php",
          async: true,
          data: { ajaxfct:"admin.rbuilder.fillCaddie", fillMode:mode, requestNumber:requestNumber },
          success:
            function(msg)
            {
              $('#iMenuCaddieImg').css('display', 'none');
              $('#iMenuCaddieItems ul').css('display', 'block');
              alert('{/literal}{"gpc_the_caddie_is_updated"|@translate}{literal}');
            },
          error:
            function(msg)
            {
              $('#iMenuCaddieImg').css('display', 'none');
              $('#iMenuCaddieItems ul').css('display', 'block');
              alert('{/literal}{"gpc_something_is_wrong_on_the_server_side"|@translate}{literal}');
            },
        }
      );
    }

    init(optionsToSet);
  }


  function init()
  {
    im = new interfaceManager(
      {
        requestCriterionsVisible:'cRequestCriterions',
        requestCriterionsHidden:'cModifyRequest',
        requestResult:'cResultQuery',
        requestResultContent:'iResultQueryContent',
        requestResultNfo:'iResultQueryNfo',
        requestResultPagesNavigator:'iPagesNavigator',
      }
    );

    {/literal}



    {if defined('IN_ADMIN')}
      requestBuilderOptions.imgEditUrl='{$ROOT_URL}{$themeconf.admin_icon_dir}/edit_s.png';
      requestBuilderOptions.imgDeleteUrl='{$ROOT_URL}{$themeconf.admin_icon_dir}/delete.png';
    {else}
      requestBuilderOptions.imgEditUrl='{$ROOT_URL}{$themeconf.icon_dir}/edit.png';
      requestBuilderOptions.imgDeleteUrl='{$ROOT_URL}{$themeconf.icon_dir}/delete.png';
    {/if}
    {literal}

    requestBuilderOptions.classGroup='gcBorderInput gcTextInput';
    requestBuilderOptions.classItem='gcBgInput gcTextInput';
    requestBuilderOptions.classOperator='cbOperator cbOperatorBg{/literal}{$datas.themeName}{literal} gcLinkHover';
    requestBuilderOptions.onRequestSuccess = function (msg) { im.doAction('queryResult', true, msg); cb.doAction('getPage', im.getRequestNumber(), 1, im.getNumberPerPage()); };
    requestBuilderOptions.onRequestError = function (msg) { im.doAction('queryResult', false, msg); };
    requestBuilderOptions.onGetPageSuccess = function (msg) { im.doAction('queryPage', true, msg); };
    requestBuilderOptions.onGetPageError = function (msg) { im.doAction('queryPage', false, msg); };

    cb = new criteriaBuilder('iListSelectedCriterions', requestBuilderOptions);

    im.doAction('setOptions',
      {
        onPageChange:
          function (requestNumber, page, numberPerPage)
          {
            $('#iResultQueryContent').html("<br><img class='waitingResult' src='./plugins/GrumPluginClasses/icons/processing.gif'>");
            cb.doAction('getPage', requestNumber, page, numberPerPage);
          }
      }
    );
  }
</script>
{/literal}

{foreach from=$datas.dialogBox item=dialogBox}
  {$dialogBox.content}
{/foreach}

{if is_admin()}
<div id='iRBCaddieNfo'></div>
{/if}

<form>
  <fieldset>
    <legend>{'gpc_rb_search_criterion'|@translate}</legend>

    <div id='iRequestCriterions' class='cRequestCriterions'>
      <div style='width:100%;min-height:250px;margin-bottom:8px;'>
        <ul id='iListSelectedCriterions'>
        </ul>
      </div>

      <div id='iMenuCriterions' >
        <div id='iMenuCTitle' class='gcLink gcBgInput cbButtons'>
          <div id='iMenuCText'>{'gpc_rb_add_criterions'|@translate}&nbsp;&dArr;</div>
          <div id='iMenuCItems'>
            <ul class='gcBgInput'>
              {foreach from=$datas.dialogBox item=dialogBox}
                <li class='gcBgInput'><a onclick="{$dialogBox.handle}.show({literal}{cBuilder:cb}{/literal});">{$dialogBox.label}</a></li>
              {/foreach}
            </ul>
          </div>
        </div>
      </div>

      <div class='gcBgInput cbButtons'>{literal}<a onclick="cb.doAction('clear');">{/literal}{'gpc_rb_clear_criterions'|@translate}</a></div>
    </div>
    <div class='cModifyRequest' style='display:none;'>
      <div class='gcBgInput cbButtons'>{literal}<a onclick="im.doAction('show', 'buildQuery');">{/literal}{'gpc_rb_do_modify_request'|@translate}</a></div>
    </div>

  </fieldset>

  <input type="button" class='cRequestCriterions' onclick="cb.doAction('send');" value="{'gpc_rb_search'|@translate}">
</form>


<fieldset id='iResultQuery' style='display:none;' class='cResultQuery'>
  <legend>{'gpc_rb_result_query'|@translate}</legend>

  <div id='iResultQueryContent' style='width:100%;min-height:250px;max-height:450px;overflow:auto;margin-bottom:8px;'></div>

  <div class='gcBgInput gcTextInput'>
    <div id='iPagesNavigator' style='float:right;'></div>
    <div style='text-align:left;padding:4px;'>
      {'gpc_rb_number_of_item_found'|@translate}&nbsp;:&nbsp;<span id='iResultQueryNfo'></span>

      {if is_admin()}
      <div id='iMenuCaddie' style='display:inline-block;'>
        <div id='iMenuCaddieBar'>
          <div id='iMenuCaddieText' class='gcLink gcBgInput'>{'gpc_manage_caddie'|@translate}&dArr;
          <div id='iMenuCaddieImg' style='display:none;width:16px;height:16px;background:url(./plugins/GrumPluginClasses/icons/processing.gif) no-repeat 0 0 transparent;'>&nbsp;</div>
          <div id='iMenuCaddieItems'>
            <ul class='gcBgInput'>
              <li class='gcBgInput'><a onclick="im.doAction('fillCaddie', 'add');">{'gpc_add_caddie'|@translate}</a></li>
              <li class='gcBgInput'><a onclick="im.doAction('fillCaddie', 'replace');">{'gpc_replace_caddie'|@translate}</a></li>
            </ul>
          </div>
        </div>
      </div>
      {/if}

    </div>
  </div>
</fieldset>


<script type="text/javascript">
  {foreach from=$datas.dialogBox item=dialogBox}
  var {$dialogBox.handle}=new {$dialogBox.dialogBoxClass}();
  {/foreach}

  $('.ui-dialog').css('overflow', 'visible');

  init();
</script>
