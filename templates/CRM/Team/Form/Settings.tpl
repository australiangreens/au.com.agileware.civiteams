<div class="crm-block crm-form-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <div class="crm-section form-item">
    <div class="label">{$form.team_name.label}</div>
    <div class="content">{$form.team_name.html}</div>
  </div>

  <div class="crm-section form-item">
    <div class="label"></div>
    <div class="content">{$form.enabled.html}&nbsp;{$form.enabled.label}</div>
  </div>

  <div class="crm-section form-item">
    <div class="label"></div>
    <div class="content">
      {if $is_domain} {ts 1=$team_name}%1 is only available for <em class="placeholder">{$baseURL}</em>.{/ts}
      {else}{ts 1=$team_name}%1 is available for all domains.{/ts}{/if}
    </div>
  </div>

  {foreach from=$groupNames item=groupName key=groupKey}
  <div class="crm-accordion-wrapper">
    <div class="crm-accordion-header">{$groupName}</div>
    <div class="crm-accordion-body">
      {foreach from=$elementNames.$groupKey item=elementName}
      <div class="crm-section form-item">
        {if $form.$elementName.type == 'checkbox'}
        <div class="label"></div>
        <div class="content">{$form.$elementName.html}&nbsp;{$form.$elementName.label}</div>
        {else}
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        {/if}
        {if $descriptions.$elementName}
        <div class="content description">{$descriptions.$elementName}</div>
        {/if}
        <div class="clear"></div>
      </div>
      {/foreach}
    </div>
  </div>
  {/foreach}

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
