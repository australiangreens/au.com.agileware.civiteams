{include file="CRM/Team/Form/TeamContacts.tpl"}

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
        </thead>
        <tbody>
          {foreach from=$rows item=team}
            <tr class="{cycle values="odd-row,even-row"}">
              {foreach from=$colHeaders key=ckey item=header}
              <td>{$team.$ckey}</td>
              {/foreach}
              <td>{$team.actions}</td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </div>

{include file="CRM/common/pager.tpl" location="bottom"}
{/if}

<a href="{crmURL p="civicrm/teams/settings" q="reset=1&team_id=$team_id"}">{ts}» Team Settings{/ts}</a>
