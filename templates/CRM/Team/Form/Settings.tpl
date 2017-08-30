<h2>Team Settings: {$team_name}</h2>

<div class="crm-block crm-form-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>
  
  <div class="crm-section">
    <div class="label">{$form.team_name.label}</div>
    <div class="content">{$form.team_name.html}</div>
  </div>
  
  <div class="crm-section">
    <div class="label"></div>
    <div class="content">{$form.enabled.html}&nbsp;{$form.enabled.label}</div>
  </div>

  {foreach from=$groupNames item=groupName key=groupKey}
  <fieldset class="crm-accordion-wrapper">
    <legend>{$groupName}</legend>
    {foreach from=$elementNames.$groupKey item=elementName}
    <div class="crm-section">
      <div class="label">{$form.$elementName.label}</div>
      <div class="content">{$form.$elementName.html}</div>
      <div class="clear"></div>
    </div>
    {/foreach}
  </fieldset>
  {/foreach}

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
