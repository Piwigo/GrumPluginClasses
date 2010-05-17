{if isset($tabsheet) and count($tabsheet)}
<ul {if isset($tabsheet_classes)}class="{$tabsheet_classes}"{/if} {if isset($tabsheet_id)}id="{$tabsheet_id}"{/if} >
{foreach from=$tabsheet key=name item=sheet name=tabs}
  <li class="{if ($name == $tabsheet_selected)}selected_tab{else}normal_tab{/if}">
    <a {if $sheet.url!=''}href="{$sheet.url}"{/if} {if $sheet.onClick!=''}onclick="{$sheet.onClick}"{/if} ><span>{$sheet.caption}</span></a>
  </li>
{/foreach}
</ul>

  {if isset($tabsheet_id)}
  {literal}
  <script type="text/javascript">
    $('#{/literal}{$tabsheet_id}{literal} li a').bind('click',
      function ()
      {
        $('#{/literal}{$tabsheet_id}{literal} li').removeClass('selected_tab').addClass('normal_tab');
        $(this.parentNode).removeClass('normal_tab').addClass('selected_tab');
      }
    );
  {/literal}
  </script>

  {/if}

{/if}
