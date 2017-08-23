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

<div id="teamContact" class="crm-search-results">
  <div class="crm-pager">{ts 1=$form.limit.html}Showing %1 entries{/ts}</div>
  <div class="form-item">
    <table>
      <tr class="columnheader">{foreach from=$colHeaders item=colHeader}<th>{$colHeader}</th>{/foreach}<th>{* Operations *}</th></tr>
      {foreach from=$rows item=team}
      <tr class="{cycle values="odd-row,even-row"}">
        {foreach from=$colHeaders key=ckey item=header}
        <td>{$team.$ckey}</td>
        {/foreach}
        <td>
          <a class="action-item crm-hover-button" href="#">{ts}Contacts{/ts}</a>
          <a class="action-item crm-hover-button" href="#">{ts}Settings{/ts}</a>
          <a class="action-item crm-hover-button" href="#">{ts}Disable{/ts}</a>
        </td>
      </tr>
      {/foreach}
      </table>
  </div>
</div>

