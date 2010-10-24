/**
 * -----------------------------------------------------------------------------
 * file: ui.tagSelector.js
 * file version: 1.0.0
 * date: 2010-10-22
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
 *
 *
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
                      ignoreCase:true,
                      //allowCreate:false,
                      serverUrl:'plugins/GrumPluginClasses/gpc_ajax.php',
                      serverCallDelay:250,

                      listMaxWidth:0,
                      listMaxHeight:0,
                      maximumTagLoaded:0,  //0 = no limits

                      textStart:'Start to type text...',
                      textFound:'%s tags found',
                      textDisplay:'display only %s tags',

                      mode:'public',
                      filter:'affected',

                      inputNumCar:5,

                      add:null,
                      remove:null,
                      popup:null,
                      load:null
                    };

              // if options given, merge it
              // if(opt) $.extend(options, opt); ==> options are set by setters

              $this.data('options', options);


              if(!properties)
              {
                $this.data('properties',
                  {
                    initialized:false,
                    selectorVisible:false,
                    totalTags:0,
                    tags:[], // a tag = {id:0, name:''}
                    cache:[],
                    timerHandle:null,
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
                          'class':'ui-tag-selector-input',
                          css:{
                            width:'100%'
                          }
                        }
                    ).bind('click.tagSelector',
                        function ()
                        {
                          objects.input.focus();
                        }
                      ),
                    selectedTagList:$('<ul/>',
                      {
                        html: '',
                        'class':'ui-tag-selector-selected-tag-list',
                      }
                    ),
                    input:$('<input>',
                      {
                        type:"text",
                        value:''
                      }
                    ).bind('focusout.tagSelector',
                        function ()
                        {
                          privateMethods.lostFocus($this);
                        }
                      )
                      .bind('focus.tagSelector',
                          function ()
                          {
                            privateMethods.getFocus($this);
                          }
                        )
                      .bind('keypress.tagSelector',
                          function ()
                          {
                            privateMethods.setTimerHandle($this);
                          }
                        ),
                    selectorList:$('<div/>',
                        {
                          html: "",
                          'class':'ui-tag-selector-list',
                          css: {
                            display:'none',
                            position:'absolute',
                            zIndex:9999,
                          }
                        }
                    ).bind('mouseleave.tagSelector',
                        function ()
                        {
                          privateMethods.displaySelector($this, false);
                        }
                      ),
                    tagList:$('<ul/>',
                      {
                        css: {
                          listStyle:'none',
                          padding:'0px',
                          margin:'0px',
                          overflow:"auto",
                        }
                      }
                    ),
                    textArea:$('<div/>',
                      {
                        html:'',
                        'class':'ui-tag-selector-text'
                      }
                    )
                  };

                $this
                  .html('')
                  .append(objects.container.append(objects.selectedTagList.append($('<li/>').append(objects.input) ) ) )
                  .append(objects.selectorList.append(objects.tagList).append(objects.textArea));

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
              objects.selectedTagList.children().unbind();
              objects.input.unbind().remove();
              objects.container.unbind().remove();
              objects.selectorList.unbind().remove();
              objects.tagList.remove();
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
          this.each(function()
            {
              var $this=$(this);
              privateMethods.setOptions($this, value);
              return($this);
            }
          );
        }, // autoLoad


      ignoreCase: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setIgnoreCase($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.ignoreCase);
            }
            else
            {
              return(true);
            }
          }
        }, // ignoreCase

      inputNumCar: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setInputNumCar($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.inputNumCar);
            }
            else
            {
              return(true);
            }
          }
        }, // ignoreCase

/*
      allowCreate: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setAllowCreate($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.allowCreate);
            }
            else
            {
              return(false);
            }
          }
        }, // allowCreate
*/
      maximumTagLoaded: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setMaximumTagLoaded($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.maximumTagLoaded);
            }
            else
            {
              return(0);
            }
          }
        }, // maximumTagLoaded

      listMaxWidth: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setListMaxWidth($this, value);
                return($this);
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
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setListMaxHeight($this, value);
                return($this);
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


      serverCallDelay: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setServerCallDelay($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.serverCallDelay);
            }
            else
            {
              return(0);
            }
          }
        }, // serverCallDelay


      serverUrl: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setServerUrl($this, value);
                return($this);
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

      textStart: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setTextStart($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.textStart);
            }
            else
            {
              return('');
            }
          }
        }, // textStart

      textFound: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setTextFound($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.textFound);
            }
            else
            {
              return('');
            }
          }
        }, // textFound

      textDisplay: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setTextDisplay($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.textDisplay);
            }
            else
            {
              return('');
            }
          }
        }, // textDisplay

      filter: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setFilter($this, value);
                return($this);
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
              return(true);
            }
          }
        }, // filter

      mode: function (value)
        {
          if(value)
          {
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setMode($this, value);
                return($this);
              }
            );
          }
          else
          {
            var options = this.data('options');

            if(options)
            {
              return(options.mode);
            }
            else
            {
              return(true);
            }
          }
        }, // mode

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
            // return the selected tags
            var properties=this.data('properties');
            return(properties.tags);
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
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setEventLoad($this, value);
                return($this);
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
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setEventPopup($this, value);
                return($this);
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
      add: function (value)
        {
          if(value && $.isFunction(value))
          {
            // set selected value
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setEventAdd($this, value);
                return($this);
              }
            );
          }
          else
          {
            // return the selected value
            var options=this.data('options');

            if(options)
            {
              return(options.add);
            }
            else
            {
              return(null);
            }
          }
        }, // add
      remove: function (value)
        {
          if(value && $.isFunction(value))
          {
            // set selected value
            this.each(function()
              {
                var $this=$(this);
                privateMethods.setEventRemove($this, value);
                return($this);
              }
            );
          }
          else
          {
            // return the selected value
            var options=this.data('options');

            if(options)
            {
              return(options.remove);
            }
            else
            {
              return(null);
            }
          }
        }, // remove
      numberOfTags: function ()
        {
          var properties=this.data('properties');

          if(properties)
          {
            return(properties.tags.length);
          }
          else
          {
            return(null);
          }
        } // numberOfTags

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

          privateMethods.setIgnoreCase(object, (value.ignoreCase!=null)?value.ignoreCase:options.ignoreCase);
          privateMethods.setInputNumCar(object, (value.inputNumCar!=null)?value.inputNumCar:options.inputNumCar);
          //privateMethods.setAllowCreate(object, (value.allowCreate!=null)?value.allowCreate:options.allowCreate);
          privateMethods.setValue(object, (value.value!=null)?value.value:[]);
          privateMethods.setMaximumTagLoaded(object, (value.maximumTagLoaded!=null)?value.maximumTagLoaded:options.maximumTagLoaded);
          privateMethods.setTextStart(object, (value.textStart!=null)?value.textStart:options.textStart);
          privateMethods.setTextFound(object, (value.textFound!=null)?value.textFound:options.textFound);
          privateMethods.setTextDisplay(object, (value.textDisplay!=null)?value.textDisplay:options.textDisplay);
          privateMethods.setListMaxWidth(object, (value.listMaxWidth!=null)?value.listMaxWidth:options.listMaxWidth);
          privateMethods.setListMaxHeight(object, (value.listMaxHeight!=null)?value.listMaxHeight:options.listMaxHeight);
          privateMethods.setServerCallDelay(object, (value.serverCallDelay!=null)?value.serverCallDelay:options.serverCallDelay);
          privateMethods.setServerUrl(object, (value.serverUrl!=null)?value.serverUrl:options.serverUrl);
          privateMethods.setMode(object, (value.mode!=null)?value.mode:options.mode);
          privateMethods.setFilter(object, (value.filter!=null)?value.filter:options.filter);
          privateMethods.setEventPopup(object, (value.popup!=null)?value.popup:options.popup);
          privateMethods.setEventAdd(object, (value.add!=null)?value.add:options.add);
          privateMethods.setEventRemove(object, (value.remove!=null)?value.remove:options.remove);
          privateMethods.setEventLoad(object, (value.load!=null)?value.load:options.load);

          if(options.autoLoad) privateMethods.load(object);

          properties.initialized=true;
        },

      setIgnoreCase : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.ignoreCase!=value) && (value==true || value==false))
          {
            options.ignoreCase=value;
          }
          return(options.ignoreCase);
        },

      setInputNumCar : function (object, value)
        {
          var options=object.data('options'),
              objects=object.data('objects'),
              properties=object.data('properties');
          if((!properties.initialized || options.inputNumCar!=value) && value>0)
          {
            options.inputNumCar=value;
            objects.input.attr('size', options.inputNumCar);
          }
          return(options.inputNumCar);
        },

/*
      setAllowCreate : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.allowCreate!=value) && (value==true || value==false))
          {
            options.allowCreate=value;
          }
          return(options.allowCreate);
        },
*/

      setMaximumTagLoaded : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.setMaximumTagLoaded!=value) && value>=0)
          {
            options.maximumTagLoaded=value;
          }
          return(options.maximumTagLoaded);
        },


      setTextStart : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if(!properties.initialized || options.textStart!=value)
          {
            options.textStart=value;
          }
          return(options.textStart);
        },

      setTextFound : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if(!properties.initialized || options.textFound!=value)
          {
            options.textFound=value;
          }
          return(options.textFound);
        },

      setTextDisplay : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if(!properties.initialized || options.textDisplay!=value)
          {
            options.textDisplay=value;
          }
          return(options.textDisplay);
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
              objects.selectorList.css('max-width', options.listMaxWidth+'px');
            }
            else
            {
              objects.selectorList.css('max-width', '');
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
              objects.tagList.css('max-height', options.listMaxHeight+'px');
            }
            else
            {
              objects.tagList.css('max-height', '');
            }
          }
          return(options.listMaxHeight);
        },

      setServerCallDelay : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.serverCallDelay!=value) && value>0 )
          {
            options.serverCallDelay=value;
          }
          return(options.serverCallDelay);
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


      setMode : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.mode!=value) && (value=='admin' || value=='public'))
          {
            options.mode=value;
          }
          return(options.mode);
        },

      setFilter : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties');
          if((!properties.initialized || options.filter!=value) && (value=='all' || value=='affected'))
          {
            options.filter=value;
          }
          return(options.filter);
        },


      setValue : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          if(value=='clear')
          {
            properties.tags=[];
            objects.selectedTagList.children('.ui-tag-selector-selected-tag').remove();
          }
          else
          {
            if(!$.isArray(value))
            {
              value=[value]; //works with array only
            }

            for(var i=0;i<value.length;i++)
            {
              if(value[i].id!=null && value[i].name!=null)
              {
                // remove tag if present, otherwise add it
                if(privateMethods.removeTag(object, value[i].id)==-1) privateMethods.addTag(object, value[i].id, value[i].name);
              }
              else
              {
                //not an object, consider it's a tag id to be removed
                privateMethods.removeTag(object, value[i]);
              }
            }
          }

          return(null);
        }, //setValue


      displaySelector : function (object, value)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects'),
              popup=false;

          if(properties.selectorVisible!=value) popup=true;;

          properties.selectorVisible=value;

          if(properties.selectorVisible)
          {
            if(properties.cache.length>0)
            {
              objects.tagList.css('display', 'block');
              if(properties.cache.length<properties.totalTags)
              {
                objects.textArea.html(
                  options.textFound.replace('%s', properties.totalTags)+', '+
                  options.textDisplay.replace('%s', properties.cache.length)
                ).css('display', 'block');
              }
              else
              {
                objects.textArea.html(options.textFound.replace('%s', properties.cache.length)).css('display', 'block');
              }
            }
            else if(options.textStart!='')
            {
              objects.tagList.css('display', 'none');
              objects.textArea.html(options.textStart).css('display', 'block');
            }
            else
            {
              objects.textArea.html('').css('display', 'none');
            }

            objects.selectorList
              .css(
                {
                  display:'block',
                  'min-width':objects.selectorList.parent().css('width')
                }
              );
          }
          else
          {
            objects.selectorList.css('display', 'none');
          }

          if(options.popup && popup) object.trigger('tagSelectorPopup', [properties.selectorVisible]);

          return(properties.selectorVisible);
        }, //displaySelector

      load : function (object)
        {
          // load datas from server through an asynchronous ajax call
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          privateMethods.clearTimerHandle(object);

          $.ajax(
            {
              type: "POST",
              url: options.serverUrl,
              data: {
                ajaxfct:options.mode+'.tagSelector.get',
                filter:options.filter,
                maxTags:options.maximumTagLoaded,
                ignoreCase:options.ignoreCase,
                letters:objects.input.val()
              },
              async: true,
              success: function(msg)
                {
                  list=$.parseJSON(msg);

                  properties.totalTags=list.totalNbTags;
                  privateMethods.cacheClear(object);
                  privateMethods.cacheAddItems(object, list.tags);
                  if(options.load) object.trigger('tagSelectorLoad');

                  privateMethods.displaySelector(object, true);
                },
              error: function(msg)
                {
                  objects.selectorList.html('Error ! '+msg);
                },
            }
         );
        },

      cacheClear : function (object)
        {
          // clear the cache tag list
          var objects=object.data('objects'),
              options=object.data('options'),
              properties=object.data('properties');

          objects.tagList.children().unbind().remove();
          properties.cache=[];
        },
      cacheAddItems : function (object, listItems)
        {
          // add the items to the cache list
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          for(var i=0;i<listItems.length;i++)
          {
            properties.cache.push(
              {
                id:listItems[i].id,
                name:listItems[i].name,
              }
            );

            if(options.ignoreCase)
            {
              var re=new RegExp('(.*)('+objects.input.val()+')(.*)', 'i');
            }
            else
            {
              var re=new RegExp('(.*)('+objects.input.val()+')(.*)');
            }
            tmpResult=re.exec(listItems[i].name);
            if(tmpResult!=null)
            {
              tmpResult=tmpResult[1]+'<span class="ui-tag-selector-highlight">'+tmpResult[2]+'</span>'+tmpResult[3];
            }
            else
            {
              tmpResult=listItems[i].name;
            }

            var li=$('<li/>',
                      {
                        html:tmpResult,
                        value:listItems[i].id,
                        'class':'ui-tag-selector-list-item'
                      }
                    ).bind('mousedown.tagSelector',
                          {object:object},
                          function (event)
                          {
                            privateMethods.addTag(event.data.object, $(this).attr('value'), $(this).text());
                          }
                      );
            objects.tagList.append(li);
          }
        },

      addTag : function (object, id, name)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          if(privateMethods.findTagById(object, id)==-1)
          {
            //add only if not already selected..
            properties.tags.push({id:id, name:name});

            var li=$('<li/>',
                      {
                        value:id,
                        html:name,
                        'class':'ui-tag-selector-selected-tag'
                      }
                    ).prepend(
                      $('<span/>',
                        {
                          html:'x'
                        }
                       ).bind('click.tagSelector',
                          {object:object},
                          function (event)
                          {
                            privateMethods.removeTag(event.data.object, $(this).parent().attr('value'));
                          }
                        )
                      );
            objects.input.val('').parent().before(li);
            if(options.add) object.trigger('tagSelectorAdd', id);
          }
        },

      removeTag : function (object, id)
        {
          var options=object.data('options'),
              properties=object.data('properties'),
              objects=object.data('objects');

          var index=privateMethods.findTagById(object, id);
          if(index>-1)
          {
            properties.tags.splice(index,1);
            item=objects.selectedTagList.children('[value='+id+']').remove();

            if(options.remove) object.trigger('tagSelectorRemove', id);
          }
          return(index);
        },

      findTagById : function (object, value)
        {
          var properties=object.data('properties');

          for(var i=0;i<properties.tags.length;i++)
          {
            if(properties.tags[i].id==value) return(i);
          }
          return(-1);
        },

      getFocus : function (object)
        {
          privateMethods.displaySelector(object, true);
        },

      lostFocus : function (object)
        {
          privateMethods.displaySelector(object, false);
        },

      setEventPopup : function (object, value)
        {
          var options=object.data('options');
          options.popup=value;
          object.unbind('tagSelectorPopup');
          if(value) object.bind('tagSelectorPopup', options.popup);
          return(options.popup);
        },
      setEventAdd : function (object, value)
        {
          var options=object.data('options');
          options.add=value;
          object.unbind('tagSelectorAdd');
          if(value) object.bind('tagSelectorAdd', options.add);
          return(options.add);
        },
      setEventRemove : function (object, value)
        {
          var options=object.data('options');
          options.remove=value;
          object.unbind('tagSelectorRemove');
          if(value) object.bind('tagSelectorRemove', options.remove);
          return(options.remove);
        },
      setEventLoad : function (object, value)
        {
          var options=object.data('options');
          options.load=value;
          object.unbind('categorySelectorLoad');
          if(value) object.bind('tagSelectorLoad', options.load);
          return(options.load);
        },

      clearTimerHandle : function(object)
        {
          var properties=object.data('properties');

          if(properties.timerHandle!=null)
          {
            window.clearInterval(properties.timerHandle);
            properties.timerHandle=null;
          }
        },
      setTimerHandle : function(object)
        {
          var properties=object.data('properties'),
              options=object.data('options');

          privateMethods.clearTimerHandle(object);
          properties.timerHandle=window.setInterval(function () { privateMethods.load(object); }, options.serverCallDelay);
        },
    };


    $.fn.tagSelector = function(method)
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
        $.error( 'Method ' +  method + ' does not exist on jQuery.tagSelector' );
      }
    } // $.fn.tagSelector

  }
)(jQuery);

