/**
 * -----------------------------------------------------------------------------
 * file: ui.categorySelector.js
 * file version: 1.0.0
 * date: 2010-10-10
 *
 * A jQuery plugin provided by the piwigo's plugin "GrumPluginClasses"
 *
 * -----------------------------------------------------------------------------
 * Author     : Grum
 *   email    : grum@piwigo.com
 *   website  : http://photos.grum.fr
 *   PWG user : http://forum.phpwebgallery.net/profile.php?id=3706
 *
 *   << May the Little SpaceFrog be with you ! >>
 * -----------------------------------------------------------------------------
 *
 * see ui.categorySelector.help.txt for help about this plugin
 *
 * :: HISTORY ::
 *
 * | release | date       |
 * | 1.0.0   | 2010/10/10 | first release
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 *
 */



(
  function($)
  {
    /*
     * plugin 'public' functions
     */
    var publicMethods =
    {
      init : function (opt)
        {
          return this.each(function()
            {
              // default values for the plugin
              var $this=$(this),
                  data = $this.data('options'),
                  objects = $this.data('objects'),
                  properties = $this.data('properties'),
                  options =
                    {
                      autoLoad:true,
                      galleryRoot:true,
                      displayStatus:true,
                      //displayNbPhotos:true,
                      listMaxWidth:0,
                      listMaxHeight:0,
                      levelIndent:16,
                      iconWidthEC:15,
                      iconWidthCheck:18,
                      serverUrl:'plugins/GrumPluginClasses/gpc_ajax.php',
                      filter:'accessible',
                      popup:null,
                      change:null,
                      load:null,
                      multiple:false,
                      userMode:'public'
                    };

              // if options given, merge it
              // if(opt) $.extend(options, opt); ==> options are set by setters

              $this.data('options', options);


              if(!properties)
              {
                $this.data('properties',
                  {
                    index:-1,
                    initialized:false,
                    selectorVisible:false,
                    categories:[],
                    labelStatus:['', ''],
                  }
                );
                properties=$this.data('properties');
              }

              if(!objects)
              {
                objects =
                  {
                    container:$('<div/>',
                        {
                          'class':'ui-category-selector',
                          css:{
                            width:'100%'
                          }
                        }
                    ).bind('click.categorySelector',
                        function ()
                        {
                          privateMethods.displaySelector($this, !$this.data('properties').selectorVisible);
                        }
                      ),
                    containerName:$('<div/>',
                      {
                        html: '&nbsp;',
                        'class':'ui-category-selector-name',
                      }
                    ),
                    containerStatus:$('<div/>',
                      {
                        'class':'ui-category-selector-status',
                        css: {
                          float:'right',
                          display:(options.displayStatus)?'block':'none'
                        }
                      }
                    ),
                    containerArrow:$('<div/>',
                      {
                        html: '&dArr;',
                        'class':'ui-category-selector-arrow',
                        css: {
                          'float':'right',
                          cursor:'pointer'
                        }
                      }
                    ),

                    listContainer:$('<div/>',
                        {
                          html: "",
                          'class':'ui-category-selector-list',
                          css: {
                            overflow:"auto",
                            display:'none',
                            position:'absolute'
                          }
                        }
                    ).bind('mouseleave.categorySelector',
                        function ()
                        {
                          privateMethods.displaySelector($this, false);
                        }
                      ),
                    list:$('<ul/>',
                      {
                        css: {
                          listStyle:'none',
                          padding:'0px',
                          margin:'0px'
                        }
                      }
                    )
                  };

                $this
                  .html('')
                  .append(objects.container.append(objects.containerArrow).append(objects.containerStatus).append(objects.containerName))
                  .append(objects.listContainer.append(objects.list));

                $this.data('objects', objects);
              }

              privateMethods.setOptions($this, opt);
            }
          );
        }, // init
      destroy : function ()
        {
          return this.each(
            function()
            {
              // default values for the plugin
              var $this=$(this),
                  objects = $this.data('objects');
              objects.container.unbind().remove();
              objects.list.children().unbind();
              objects.listContainer.remove();
              $this
                .unbind('.categorySelector')
                .css(
                  {
                    width:'',
                    height:''
                  }
                );
            }
          );
        }, // destroy

      options: function (value)
        {
          return this.each(function()
            {
              privateMethods.setOptions($(this), value);
            }
          );
        }, // autoLoad

      autoLoad: function (value)
        {
          if(value!=null)
          {
            return this.each(function()
              {
                privateMethods.setAutoLoad($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.autoLoad);
            }
            else
            {
              return(true);
            }
          }
        }, // autoLoad

      galleryRoot: function (value)
        {
          if(value!=null)
          {
            return this.each(function()
              {
                privateMethods.setGalleryRoot($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.galleryRoot);
            }
            else
            {
              return(true);
            }
          }
        }, // autoLoad


      listMaxWidth: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setListMaxWidth($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.listMaxWidth);
            }
            else
            {
              return(0);
            }
          }
        }, // listMaxWidth

      listMaxHeight: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setListMaxHeight($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.listMaxHeight);
            }
            else
            {
              return(0);
            }
          }
        }, // listMaxHeight

      displayStatus: function (value)
        {
          if(value!=null)
          {
            return this.each(function()
              {
                privateMethods.setDisplayStatus($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.displayStatus);
            }
            else
            {
              return(true);
            }
          }
        }, // displayStatus

      levelIndent: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setLevelIndent($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.levelIndent);
            }
            else
            {
              return(0);
            }
          }
        }, // levelIndent

      serverUrl: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setServerUrl($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.serverUrl);
            }
            else
            {
              return('');
            }
          }
        }, // serverUrl

      filter: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setFilter($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.filter);
            }
            else
            {
              return('');
            }
          }
        }, // filter

      collapse: function (value)
        {
          return this.each(function()
            {
              if(!value) value=':all';
              privateMethods.setExpandCollapse($(this), value, 'C');
            }
          );
        }, // collapse

      expand: function (value)
        {
          return this.each(function()
            {
              if(!value) value=':all';
              privateMethods.setExpandCollapse($(this), value, 'E');
            }
          );
        }, // expand

      iconWidthEC: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setIconWidthEC($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.iconWidthEC);
            }
            else
            {
              return(0);
            }
          }
        }, // iconWidthEC

      iconWidthCheck: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setIconWidthCheck($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.iconWidthCheck);
            }
            else
            {
              return(0);
            }
          }
        }, // iconWidthCheck

      userMode: function (value)
        {
          if(value)
          {
            return this.each(function()
              {
                privateMethods.setUserMode($(this), value);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.userMode);
            }
            else
            {
              return(0);
            }
          }
        }, // userMode

      value: function (value)
        {
          if(value)
          {
            // set selected value
            return this.each(function()
              {
                privateMethods.setValue($(this), value);
              }
            );
          }
          else
          {
            // return the selected value
            var properties=this.data('properties'),
                options = this.data('options');

            if(properties && properties.index!=null && !options.multiple && properties.index>-1 && properties.index<properties.categories.length)
            {
              return(properties.categories[properties.index].id);
            }
            else if(properties && properties.index!=null && options.multiple)
            {
              var returned=[];
              for(var i=0;i<properties.index.length;i++)
              {
                if(properties.index[i]>-1 && properties.index[i]<properties.categories.length)
                  returned.push(properties.categories[properties.index[i]].id);
              }
              return(returned);
            }
            else
            {
              return(null);
            }
          }
        }, // value
      load: function (value)
        {
          /*
           * two functionnalities :
           *  - if value is set, use it to set the load event function
           *  - if no value, loads data from server
           */
          if(value && $.isFunction(value))
          {
            // set selected value
            return this.each(function()
              {
                privateMethods.setEventLoad($(this), value);
              }
            );
          }
          else
          {
            // loads data from server
            privateMethods.load(this);
          }
        },

      popup: function (value)
        {
          if(value && $.isFunction(value))
          {
            // set selected value
            return this.each(function()
              {
                privateMethods.setEventPopup($(this), value);
              }
            );
          }
          else
          {
            // return the selected value
            var options=this.data('options');

            if(options)
            {
              return(options.popup);
            }
            else
            {
              return(null);
            }
          }
        }, // popup
      change: function (value)
        {
          if(value && $.isFunction(value))
          {
            // set selected value
            return this.each(function()
              {
                privateMethods.setEventChange($(this), value);
              }
            );
          }
          else
          {
            // return the selected value
            var options=this.data('options');

            if(options)
            {
              return(options.change);
            }
            else
            {
              return(null);
            }
          }
        }, // popup
      numberOfCategories: function ()
        {
          var properties=this.data('properties');

          if(properties)
          {
            return(properties.categories.length);
          }
          else
          {
            return(null);
          }
        }, // numberOfCategories
      properties: function (value)
        {
          var properties=this.data('properties');

          if(properties && value==':first' && properties.categories.length>0)
          {
            return(properties.categories[0]);
          }
          else if(properties && properties.index!=null && (value==':selected' || value==null) && properties.categories.length>0)
          {
            if(!option.multiple && properties.index>-1 && properties.index<properties.categories.length)
            {
              return(properties.categories[properties.index]);
            }
            else if(option.multiple)
            {
              var returned=[];
              for(var i=0;i<properties.index.length;i++)
              {
                if(properties.index[i]>-1 && properties.index<properties.categories.length)
                  returned.push(properties.categories[properties.index[i]]);
              }
              return(returned);
            }
            return(null);
          }
          else if(properties && value>-1)
          {
            var index=privateMethods.findIndexByValue(this, value);
            if(index>-1)
            {
              return(properties.categories[index]);
            }
            return(null);
          }
          else
          {
            return(null);
          }
        }, // numberOfCategories
    }; // methods


    /*
     * plugin 'private' methods
     */
    var privateMethods =
    {
      setOptions : function (object, value)
        {
          var properties=object.data('properties'),
              options=object.data('options');

          if(!$.isPlainObject(value)) return(false);

          properties.initialized=false;

          privateMethods.setAutoLoad(object, (value.autoLoad!=null)?value.autoLoad:options.autoLoad);
          privateMethods.setGalleryRoot(object, (value.galleryRoot!=null)?value.galleryRoot:options.galleryRoot);
          privateMethods.setDisplayStatus(object, (value.displayStatus!=null)?value.displayStatus:options.displayStatus);
          privateMethods.setListMaxWidth(object, (value.listMaxWidth!=null)?value.listMaxWidth:options.listMaxWidth);
          privateMethods.setListMaxHeight(object, (value.listMaxHeight!=null)?value.listMaxHeight:options.listMaxHeight);
          privateMethods.setLevelIndent(object, (value.levelIndent!=null)?value.levelIndent:options.levelIndent);
          privateMethods.setIconWidthEC(object, (value.iconWidthEC!=null)?value.iconWidthEC:options.iconWidthEC);
          privateMethods.setIconWidthCheck(object, (value.iconWidthCheck!=null)?value.iconWidthCheck:options.iconWidthCheck);
          privateMethods.setServerUrl(object, (value.serverUrl!=null)?value.serverUrl:options.serverUrl);
          privateMethods.setFilter(object, (value.filter!=null)?value.filter:options.filter);
          privateMethods.setUserMode(object, (value.userMode!=null)?value.userMode:options.userMode);
          privateMethods.setEventPopup(object, (value.popup!=null)?value.popup:options.popup);
          privateMethods.setEventChange(object, (value.change!=null)?value.change:options.change);
          privateMethods.setEventLoad(object, (value.load!=null)?value.load:options.load);
          privateMethods.setMultiple(object, (value.multiple!=null)?value.multiple:options.multiple); // can be set only at the initialization

          if(options.autoLoad) privateMethods.load(object);

          properties.initialized=true;
        },

      setAutoLoad : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.autoLoad!=value) && (value==true || value==false))
          {
            options.autoLoad=value;
          }
          return(options.autoLoad);
        },

      setGalleryRoot : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.galleryRoot!=value) && (value==true || value==false))
          {
            options.galleryRoot=value;
            if(options.autoLoad && properties.initialized) privateMethods.load(object);
          }
          return(options.galleryRoot);
        },

      setListMaxWidth : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.listMaxWidth!=value) && value>=0)
          {
            options.listMaxWidth=value;
            if(options.listMaxWidth>0)
            {
              objects.listContainer.css('max-width', options.listMaxWidth+'px');
            }
            else
            {
              objects.listContainer.css('max-width', '');
            }
          }
          return(options.listMaxWidth);
        },

      setListMaxHeight : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.listMaxHeight!=value) && value>=0)
          {
            options.listMaxHeight=value;
            if(options.listMaxHeight>0)
            {
              objects.listContainer.css('max-height', options.listMaxHeight+'px');
            }
            else
            {
              objects.listContainer.css('max-height', '');
            }
          }
          return(options.listMaxHeight);
        },

      setDisplayStatus : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.displayStatus!=value) && (value==true || value==false))
          {
            options.displayStatus=value;
            if(options.displayStatus)
            {
              object.find('.ui-category-selector-status').show();
            }
            else
            {
              object.find('.ui-category-selector-status').hide();
            }
          }
          return(options.displayStatus);
        },

      setLevelIndent : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.levelIndent!=value) && value>=0)
          {
            options.levelIndent=value;
            objects.list.find('.ui-category-selector-item').each(
              function ()
              {
                $(this).css('padding-left', (options.iconWidthEC+options.iconWidthCheck+$(this).attr('level')*options.levelIndent)+'px');
              }
            );
          }
          return(options.levelIndent);
        },

      setServerUrl : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if(!properties.initialized || options.serverUrl!=value)
          {
            options.serverUrl=value;
            if(options.autoLoad && properties.initialized) privateMethods.load(object);
          }
          return(options.serverUrl);
        },


      setFilter : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.filter!=value) && (value=='none' || value=='accessible' || value=='public'))
          {
            options.filter=value;
            if(options.autoLoad && properties.initialized) privateMethods.load(object);
          }
          return(options.filter);
        },

      setIconWidthEC : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.iconWidthEC!=value) && value>=0)
          {
            options.iconWidthEC=value;
            objects.list.find('.ui-category-selector-item').each(
              function ()
              {
                $(this).css('padding-left', (options.iconWidthEC+options.iconWidthCheck+$(this).attr('level')*options.levelIndent)+'px');
              }
            );
          }
          return(options.filter);
        },

      setIconWidthCheck : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.iconWidthCheck!=value) && (value>=0 && options.multiple || !options.multiple && value==0))
          {
            options.iconWidthCheck=value;
            objects.list.find('.ui-category-selector-item').each(
              function ()
              {
                $(this).css('padding-left', (options.iconWidthEC+options.iconWidthCheck+$(this).attr('level')*options.levelIndent)+'px');
              }
            );
          }
          return(options.filter);
        },

      setMultiple : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.multiple!=value) && (value==true || value==false))
          {
            if(!value)
            {
              privateMethods.setIconWidthCheck(object, 0);
              properties.index=-1;
            }
            else
            {
              properties.index=[];
              objects.listContainer.addClass('ui-category-selector-multiple');
            }
            options.multiple=value;
          }
          return(options.filter);
        }, //setMultiple

      setUserMode : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if((!properties.initialized || options.userMode!=value) && (value=='admin' || value=='public'))
          {
            options.userMode=value;
            if(options.autoLoad && properties.initialized) privateMethods.load(object);
          }
          return(options.filter);
        }, //setUserMode

      /**
       * usage : see notes on the header file
       *
       * @param jQuery object : the jQuery object
       * @param String value : the filter
       * @param String mode : 'C' for collapse, 'E' for expand
       */
      setExpandCollapse : function (object, value, mode)
        {
          var objects=object.data('objects');

          /*
           *  target[1] = ':all' or catId
           * target[2] = '=' or '<' or '>' or '+'
           * target[3] = level
           */
          re=/^(?:(:all)(?:(=|<|>|\+)(\d+))?)|(?:(\d+)(<|>|\+))?$/i;
          target=re.exec(value);

          if(target!=null)
          {
            switch(mode)
            {
              case 'C':
                applyExpandCollapse=privateMethods.applyCollapse;
                break;
              case 'E':
                applyExpandCollapse=privateMethods.applyExpand;
                break;
            }


            if(target[1]==':all')
            {
              objects.list.find('.ui-category-selector-expandable-item, .ui-category-selector-collapsable-item').each(applyExpandCollapse);
            }
            else if(target[4]!=null)
            {
              switch(target[5])
              {
                case '+':
                  objects.list.find('.ui-category-selector-expandable-item, .ui-category-selector-collapsable-item').each(privateMethods.applyCollapse);
                  objects.list.find(':has([catId='+target[4]+'])').prev().each(privateMethods.applyExpand);
                  objects.list.find('[catId='+target[4]+']').each(applyExpandCollapse);
                  break;
                case '>':
                  objects.list.find('[catId='+target[4]+'] + ul').find('.ui-category-selector-expandable-item, .ui-category-selector-collapsable-item').each(applyExpandCollapse);
                  objects.list.find('[catId='+target[4]+']').each(applyExpandCollapse);
                  break;
                case '<':
                  objects.list.find(':has([catId='+target[4]+'])').prev().each(applyExpandCollapse);
                  objects.list.find('[catId='+target[4]+']').each(applyExpandCollapse);
                  break;
                default:
                  objects.list.find('[catId='+target[4]+']').each(applyExpandCollapse);
                  break;
              }
            }

          }
        }, //setExpandCollapse

      applyExpand : function (index, domElt)
        {
          privateMethods.applyExpandCollapse(index, domElt, 'E');
        }, //applyExpand
      applyCollapse : function (index, domElt)
        {
          privateMethods.applyExpandCollapse(index, domElt, 'C');
        }, //applyCollapse

      /**
       * used by the setExpandCollapse function
       * not aimed to be used directly
       */
      applyExpandCollapse : function (index, domElt, mode)
              {
                action='';
                var $domElt=$(domElt);

                if(target.length>2 && target[2]!=null && target[3]!=null)
                {
                  switch(target[2])
                  {
                    case '=':
                      if($domElt.attr('level')==target[3]) action=mode;
                      break;
                    case '>':
                      if($domElt.attr('level')>=target[3]) action=mode;
                      break;
                    case '<':
                      if($domElt.attr('level')<=target[3]) action=mode;
                      break;
                    case '+':
                      if((mode=='E' && $domElt.attr('level')<=target[3]) ||
                         (mode=='C' && $domElt.attr('level')>=target[3]))
                      {
                        action=mode;
                      }
                      else
                      {
                        action=(mode=='C')?'E':'C';
                      }
                      break;
                  }
                }
                else action=mode;

                switch(action)
                {
                  case 'C':
                    $domElt
                      .removeClass('ui-category-selector-expandable-item ui-category-selector-collapsable-item')
                      .addClass('ui-category-selector-expandable-item')
                      .next().hide();
                    break;
                  case 'E':
                    $domElt
                      .removeClass('ui-category-selector-expandable-item ui-category-selector-collapsable-item')
                      .addClass('ui-category-selector-collapsable-item')
                      .next().show();
                    break;
                }

              }, //applyExpandCollapse

      setValue : function (object, value, trigger)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects'),
              index=-1;

          re=/^(:invert|:all|:none)(?:(=|<|>)(\d+))$/i;
          target=re.exec(value);
          if(target!=null) value=target[1];

          switch(value)
          {
            case ':first':
              if(properties.categories.length>0) index=0;
              break;
            case ':last':
              index=properties.categories.length-1;
              break;
            case ':invert':
              if(!options.multiple) return(false);

              objects.list.find('.ui-category-selector-item').each(
                function ()
                {
                  var $this=$(this),
                      apply=true;

                  if(target!=null)
                  {
                    switch(target[2])
                    {
                      case '=':
                        apply=($this.attr('level')==target[3]);
                        break;
                      case '>':
                        apply=($this.attr('level')>=target[3]);
                        break;
                      case '<':
                        apply=($this.attr('level')<=target[3]);
                        break;
                    }
                  }

                  if(apply)
                  {
                    if($this.hasClass('ui-category-selector-selected-item'))
                    {
                      $this.removeClass('ui-category-selector-selected-item');
                    }
                    else
                    {
                      $this.addClass('ui-category-selector-selected-item');
                    }
                  }
                }
              );
              privateMethods.setValue(object, [], false);
              return(false);
              break;
            case ':none':
              if(!options.multiple) return(false);
              objects.list.find('.ui-category-selector-selected-item').each(
                function ()
                {
                  var $this=$(this),
                      apply=true;

                  if(target!=null)
                  {
                    switch(target[2])
                    {
                      case '=':
                        apply=($this.attr('level')==target[3]);
                        break;
                      case '>':
                        apply=($this.attr('level')>=target[3]);
                        break;
                      case '<':
                        apply=($this.attr('level')<=target[3]);
                        break;
                    }
                  }

                  if(apply) $this.removeClass('ui-category-selector-selected-item');
                }
              );
              privateMethods.setValue(object, [], false);
              return(false);
              break;
            case ':all':
              if(!options.multiple) return(false);
              objects.list.find('.ui-category-selector-item').each(
                function ()
                {
                  var $this=$(this),
                      apply=true;

                  if(target!=null)
                  {
                    switch(target[2])
                    {
                      case '=':
                        apply=($this.attr('level')==target[3]);
                        break;
                      case '>':
                        apply=($this.attr('level')>=target[3]);
                        break;
                      case '<':
                        apply=($this.attr('level')<=target[3]);
                        break;
                    }
                  }

                  if(apply) $this.addClass('ui-category-selector-selected-item');
                }
              );
              privateMethods.setValue(object, [], false);
              return(false);
              break;
            default:
              if($.isArray(value) && options.multiple)
              {
                index=[];
                for(var i=0;i<value.length;i++)
                {
                  tmp=privateMethods.findIndexByValue(object, value[i]);
                  if(tmp>-1) index.push(tmp);
                }
              }
              else
              {
                index=privateMethods.findIndexByValue(object, value);
              }

              break;
          }

          if(!options.multiple && (!properties.initialized || properties.index!=index) && index>-1)
          {
            objects.list.find('.ui-category-selector-selected-item').removeClass('ui-category-selector-selected-item');
            objects.list.find('[catId="'+value+'"]').addClass('ui-category-selector-selected-item');
            properties.index=index;
            objects.containerName.html(properties.categories[properties.index].name);
            objects.containerStatus.html(properties.labelStatus[properties.categories[properties.index].status]);
            if(trigger && options.change) object.trigger('categorySelectorChange', [properties.categories[properties.index].id]);
            if(properties.index>-1) return(properties.categories[properties.index].id);
          }
          else if(options.multiple)
          {
            if(!$.isArray(index))
            {
              if(index<0 || index==null) return(-1);
              index=[index];
            }
            tmp=[];
            for(var i=0;i<index.length;i++)
            {
              var item=objects.list.find('[catId="'+properties.categories[index[i]].id+'"]');

              if(item.hasClass('ui-category-selector-selected-item'))
              {
                item.removeClass('ui-category-selector-selected-item');

                tmpIndex=$.inArray(index[i] ,properties.index);
                if(tmpIndex>-1) properties.index.splice(tmpIndex, 1);
              }
              else
              {
                item.addClass('ui-category-selector-selected-item');
                properties.index.push(index[i]);
              }
              tmp.push(properties.categories[index[i]].id);
            }
            containerHtml='';
            objects.list.find('.ui-category-selector-selected-item div.ui-category-selector-name').each(
              function ()
              {
                containerHtml+=((containerHtml=='')?'':'&nbsp;; ')+$(this).html();
              }
            );
            if(containerHtml=='') containerHtml="&nbsp;";
            objects.containerName.html(containerHtml);
            if(trigger && options.change) object.trigger('categorySelectorChange', [tmp]);
            return(tmp);
          }
          return(null);
        },

      displaySelector : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');
          if(properties.selectorVisible!=value)
          {
            properties.selectorVisible=value;

            if(properties.selectorVisible && properties.categories.length>0)
            {
              var index=0;
              objects.listContainer
                .css(
                  {
                    display:'block',
                    'min-width':objects.listContainer.parent().css('width')
                  }
                );
              if($.isArray(properties.index))
              {
                if (properties.index.length>0) index=properties.index[0];
              }
              else if(properties.index>-1)
              {
                index=properties.index;
              }
              objects.listContainer.scrollTop(objects.listContainer.scrollTop()+objects.list.find('[catId="'+properties.categories[index].id+'"]').position().top);
            }
            else
            {
              objects.listContainer.css('display', 'none');
            }
            if(options.popup) object.trigger('categorySelectorPopup', [properties.selectorVisible]);
          }
          return(properties.selectorVisible);
        },

      load : function (object)
        {
          // load datas from server through an asynchronous ajax call
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          $.ajax(
            {
              type: "POST",
              url: options.serverUrl,
              async: true,
              data:
                {
                  ajaxfct:options.userMode+'.categorySelector.getList',
                  filter:options.filter,
                  galleryRoot:options.galleryRoot?'y':'n',
                  tree:'y'
                },
              success: function(msg)
                {
                  list=$.parseJSON(msg);

                  properties.labelStatus=list.status;
                  privateMethods.listClear(object);
                  privateMethods.listAddItems(object, list.categories, objects.list);

                  properties.initialized=false;
                  privateMethods.setValue(object, ':first');
                  properties.initialized=true;

                  if(options.load) object.trigger('categorySelectorLoad');
                },
              error: function(msg)
                {
                  objects.listContainer.html('Error ! '+msg);
                },
            }
         );
        },

      listClear : function (object)
        {
          // clear the categorie list
          var objects=object.data('objects'),
              options=object.data('options'),
              properties=object.data('properties');

          objects.list.children().unbind();
          objects.list.html('');
          if(options.multiple)
          {
            properties.index=[];
          }
          else
          {
            properties.index=-1;
          }
          properties.categories=[];
        },
      listAddItems : function (object, listItems, parent)
        {
          // add the items to the categorie list
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          var previousLevel=-1;
          for(var i=0;i<listItems.length;i++)
          {
            properties.categories.push(
              {
                id:listItems[i].id,
                level:listItems[i].level,
                name:listItems[i].name,
                status:listItems[i].status,
                childs:listItems[i].childs.length
              }
            );

            if(options.displayStatus)
            {
              status="<div class='ui-category-selector-status'>"+properties.labelStatus[listItems[i].status]+"</div>";
            }
            else
            {
              status="";
            }

            var spaceWidth = (options.iconWidthEC+options.iconWidthCheck+listItems[i].level*options.levelIndent),
                li=$('<li/>',
                      {
                        'class':'ui-category-selector-item',
                        html:"<div>"+status+"<div class='ui-category-selector-name'>"+listItems[i].name+"</div></div>",
                        catId:listItems[i].id,
                        level:listItems[i].level,
                        css:{
                          'padding-left':spaceWidth+'px'
                        }
                      }
                    ).bind('click.categorySelector',
                        {object:object, expandArea: spaceWidth, nbchilds:listItems[i].childs.length },
                        function (event)
                        {
                          if(!event.layerX && event.offsetX)
                          {
                            // trick for IE..
                            event.layerX=event.offsetX;
                            event.layerY=event.offsetY;
                          }

                          if(event.layerX<event.data.expandArea && event.data.nbchilds>0 )
                          {
                            if($(this).hasClass('ui-category-selector-expandable-item'))
                            {
                              $(this)
                                .removeClass('ui-category-selector-expandable-item')
                                .addClass('ui-category-selector-collapsable-item')
                                .next().show();
                            }
                            else
                            {
                              $(this)
                                .removeClass('ui-category-selector-collapsable-item')
                                .addClass('ui-category-selector-expandable-item')
                                .next().hide();
                            }
                          }
                          else
                          {
                            privateMethods.setValue(event.data.object, $(this).attr('catId'), true);
                            if(options.multiple)
                            {
                            }
                            else
                            {
                              privateMethods.displaySelector(event.data.object, false);
                            }
                          }
                        }
                      );
            if(listItems[i].childs.length>0)
            {
              li.addClass('ui-category-selector-collapsable-item').css('background-position', (options.iconWidthCheck+options.levelIndent*listItems[i].level)+'px 0px');
            }

            if(options.multiple)
            {
              li.prepend('<div class="ui-category-selector-check"></div>');
            }

            parent.append(li);

            if(listItems[i].childs.length>0)
            {
              var ul=$('<ul/>',
                        {
                          'class':'ui-category-selector-group',
                          css: {
                            listStyle:'none',
                            padding:'0px',
                            margin:'0px',
                            'font-size':(100-listItems[i].level*2)+'%'
                          }
                        }
                      );
              li.after(ul);
              privateMethods.listAddItems(object, listItems[i].childs, ul);
            }

          }
        },

      findIndexByValue : function (object, value)
        {
          /*
           * search a categorie inside the categories list and return the index
           * in the array
           */
          var properties=object.data('properties');
          for(var i=0;i<properties.categories.length;i++)
          {
            if(properties.categories[i].id==value) return(i);
          }
          return(-1);
        },
      setEventPopup : function (object, value)
        {
          var options=object.data('options');
          options.popup=value;
          object.unbind('categorySelectorPopup');
          if(value) object.bind('categorySelectorPopup', options.popup);
          return(options.popup);
        },
      setEventChange : function (object, value)
        {
          var options=object.data('options');
          options.change=value;
          object.unbind('categorySelectorChange');
          if(value) object.bind('categorySelectorChange', options.change);
          return(options.change);
        },
      setEventLoad : function (object, value)
        {
          var options=object.data('options');
          options.load=value;
          object.unbind('categorySelectorLoad');
          if(value) object.bind('categorySelectorLoad', options.load);
          return(options.load);
        }
    };


    $.fn.categorySelector = function(method)
    {
      if(publicMethods[method])
      {
        return publicMethods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
      }
      else if(typeof method === 'object' || ! method)
      {
        return publicMethods.init.apply(this, arguments);
      }
      else
      {
        $.error( 'Method ' +  method + ' does not exist on jQuery.categorySelector' );
      }
    } // $.fn.categorySelector

  }
)(jQuery);


