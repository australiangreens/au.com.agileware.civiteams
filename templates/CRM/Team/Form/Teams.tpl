{* HEADER *}
<div class="crm-block crm-form-block crm-group-search-form-block">
  <div class="crm-accordion-wrapper">
    <div class="crm-accordion-header crm-master-accordion-header">Find Teams</div>
    <div class="crm-accordion-body">
      {foreach from=$searchElements item=elementName}
      <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
      {/foreach}

      <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
      </div>
    </div>
  </div>
</div>

<div id="teamContact">
  <p>
    <div class="form-item">
      <table>
        <tr class="columnheader"><th>{ts}Team Name{/ts}</th><th>{ts}Members{/ts}</th><th>{ts}From Email Addresses{/ts}</th><th>{ts}Groups{/ts}</th><th></th></tr>
        {foreach from=$teamList item=team}
        <tr class="{cycle values="odd-row,even-row"}">
          <td>{$team.team_name}</td>
          <td>{$team.members}</td>
          <td></td>
          <td></td>
          <td>
            <ul>
              <li><a href="#">{ts}Contacts{/ts}</a></li>
              <li><a href="#">{ts}Settings{/ts}</a></li>
              <li><a href="#">{ts}Disable{/ts}</a></li>
            </ul>
          </td>
        </tr>
        {/foreach}
      </table>
    </div>
  </p>
</div>

<pre>{$results}</pre>
