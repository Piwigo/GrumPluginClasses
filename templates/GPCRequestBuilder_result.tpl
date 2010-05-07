<ul class='rbResultList'>
  {foreach from=$datas item=row}
  <li>
    <table>
      <tr>
        <td>
          <img src='{$row.imageThumbnail}'>
        </td>

        <td class="rbResultItemDetail">
          {$row.imageId}<br>
          {$row.imageName}<br>
          {$row.imagePath}<br>
          {$row.imageCategories}<br>
          <hr>
          {foreach from=$row.plugin item=plugin}
          {$plugin}<br>
          {/foreach}
        </td>

      </tr>
    </table>

  </li>
  {/foreach}


</ul>

