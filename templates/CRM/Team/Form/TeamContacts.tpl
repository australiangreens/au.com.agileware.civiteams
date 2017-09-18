<div class="crm-block crm-form-block crm-group-search-form-block">
  <div class="crm-accordion-wrapper">
    <div class="crm-accordion-header crm-master-accordion-header">{ts}Find Contacts within this Team{/ts}</div>
    {$form.team_id.html}
    <div class="crm-accordion-body">
      {foreach from=$searchElements item=elementName}
      {if is_array($elementName)}
      <fieldset>
        <legend>{$elementName.title}</legend>
        {foreach from=$elementName.children item=childName}
        <div class="crm-section">
          <div class="label">{$form.$childName.label}</div>
          <div class="content">{$form.$childName.html}</div>
          <div class="clear"></div>
        </div>
        {/foreach}
      </fieldset>
      {else}
      <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
      {/if}
      {/foreach}
      <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
      </div>
    </div>
  </div>
</div>
