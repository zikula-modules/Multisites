{* purpose of this template: inclusion template for managing related template *}
{if !isset($displayMode)}
    {assign var='displayMode' value='dropdown'}
{/if}
{if !isset($allowEditing)}
    {assign var='allowEditing' value=false}
{/if}
{if isset($panel) && $panel eq true}
    <h3 class="template z-panel-header z-panel-indicator z-pointer">{gt text='Template'}</h3>
    <fieldset class="template z-panel-content" style="display: none">
{else}
    <fieldset class="template">
{/if}
    <legend>{gt text='Template'}</legend>
    <div class="z-formrow">
    {if $displayMode eq 'dropdown'}
        {formlabel for=$alias __text='Choose template'}
            {multisitesRelationSelectorList group=$group id=$alias aliasReverse=$aliasReverse mandatory=$mandatory __title='Choose the template' selectionMode='single' objectType='template' linkingItem=$linkingItem}
    {elseif $displayMode eq 'autocomplete'}
        {assign var='createLink' value=''}
        {if $allowEditing eq true}
            {modurl modname='Multisites' type=$lct func='edit' ot='template' forcelongurl=true assign='createLink'}
        {/if}
        {multisitesRelationSelectorAutoComplete group=$group id=$alias aliasReverse=$aliasReverse mandatory=$mandatory __title='Choose the template' selectionMode='single' objectType='template' linkingItem=$linkingItem idPrefix=$idPrefix createLink=$createLink withImage=false}
        <div class="multisites-relation-leftside">
            {if isset($linkingItem.$alias)}
                {include file='template/include_selectItemListOne.tpl'  item=$linkingItem.$alias}
            {else}
                {include file='template/include_selectItemListOne.tpl' }
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
