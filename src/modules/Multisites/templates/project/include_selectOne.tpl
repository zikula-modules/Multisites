{* purpose of this template: inclusion template for managing related project *}
{if !isset($displayMode)}
    {assign var='displayMode' value='dropdown'}
{/if}
{if !isset($allowEditing)}
    {assign var='allowEditing' value=false}
{/if}
{if isset($panel) && $panel eq true}
    <h3 class="project z-panel-header z-panel-indicator z-pointer">{gt text='Project'}</h3>
    <fieldset class="project z-panel-content" style="display: none">
{else}
    <fieldset class="project">
{/if}
    <legend>{gt text='Project'}</legend>
    <div class="z-formrow">
    {if $displayMode eq 'dropdown'}
        {formlabel for=$alias __text='Choose project'}
            {multisitesRelationSelectorList group=$group id=$alias aliasReverse=$aliasReverse mandatory=$mandatory __title='Choose the project' selectionMode='single' objectType='project' linkingItem=$linkingItem}
    {elseif $displayMode eq 'autocomplete'}
        {assign var='createLink' value=''}
        {if $allowEditing eq true}
            {modurl modname='Multisites' type=$lct func='edit' ot='project' forcelongurl=true assign='createLink'}
        {/if}
        {multisitesRelationSelectorAutoComplete group=$group id=$alias aliasReverse=$aliasReverse mandatory=$mandatory __title='Choose the project' selectionMode='single' objectType='project' linkingItem=$linkingItem idPrefix=$idPrefix createLink=$createLink withImage=false}
        <div class="multisites-relation-leftside">
            {if isset($linkingItem.$alias)}
                {include file='project/include_selectItemListOne.tpl'  item=$linkingItem.$alias}
            {else}
                {include file='project/include_selectItemListOne.tpl' }
            {/if}
        </div>
        <br class="z-clearer" />
    {/if}
    </div>
{if isset($panel) && $panel eq true}
    </fieldset>
{else}
    </fieldset>
{/if}
