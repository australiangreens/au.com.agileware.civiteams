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
