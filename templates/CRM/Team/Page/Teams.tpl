{include file="CRM/Team/Form/Teams.tpl"}

{if $rows}

  {include file="CRM/common/pager.tpl" location="top"}

  <div id="teamContact" class="crm-search-results">
    <div class="form-item">
      <table>
        <thead class="sticky">
          <tr class="columnheader">
            {foreach from=$colHeaders item=header}
            <th>
              {if $header.sort}
                {assign var='key' value=$header.sort}
                {$sort->_response.$key.link}
              {else}
                {$header.name}
              {/if}
            </th>
            {/foreach}
            <th> {* Operations *}</th>
          </tr>
          {foreach from=$rows item=team}
            <tr class="{cycle values="odd-row,even-row"}">
              {foreach from=$colHeaders key=ckey item=header}
              <td>{$team.$ckey}</td>
              {/foreach}
              <td>{$team.actions}</td>
            </tr>
          {/foreach}
      </table>
    </div>
  </div>

{include file="CRM/common/pager.tpl" location="bottom"}

{/if}
