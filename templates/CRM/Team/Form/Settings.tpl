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

  <div class="crm-section">
    <div class="label"></div>
    <div class="content description">
      {if $is_domain} {ts 1=$team_name}%1 is only available for <em class="placeholder">{$baseURL}</em>.{/ts}
      {else}{ts 1=$team_name}%1 is available for all domains.{/ts}{/if}
    </div>
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
