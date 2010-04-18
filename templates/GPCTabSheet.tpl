{if isset($tabsheet) and count($tabsheet)}
<ul {if isset($tabsheet_classes)}class="{$tabsheet_classes}"{/if} {if isset($tabsheet_id)}id="{$tabsheet_id}"{/if} >
{foreach from=$tabsheet key=name item=sheet}
  <li class="{if ($name == $tabsheet_selected)}selected_tab{else}normal_tab{/if}">
    <a href="{$sheet.url}"><span>{$sheet.caption}</span></a>
  </li>
{/foreach}
</ul>
{/if}
