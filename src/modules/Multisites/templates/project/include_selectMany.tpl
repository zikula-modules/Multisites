{* purpose of this template: inclusion template for managing related projects *}
{if !isset($displayMode)}
    {assign var='displayMode' value='dropdown'}
{/if}
{if !isset($allowEditing)}
    {assign var='allowEditing' value=false}
{/if}
{if isset($panel) && $panel eq true}
    <h3 class="projects z-panel-header z-panel-indicator z-pointer">{gt text='Projects'}</h3>
    <fieldset class="projects z-panel-content" style="display: none">
{else}
    <fieldset class="projects">
{/if}
    <legend>{gt text='Projects'}</legend>
    <div class="z-formrow">
    {if $displayMode eq 'dropdown'}
        {formlabel for=$alias __text='Choose projects'}
            {multisitesRelationSelectorList group=$group id=$alias aliasReverse=$aliasReverse mandatory=$mandatory __title='Choose the projects' selectionMode='multiple' objectType='project' linkingItem=$linkingItem}
    {elseif $displayMode eq 'autocomplete'}
        {assign var='createLink' value=''}
        {if $allowEditing eq true}
            {modurl modname='Multisites' type=$lct func='edit' ot='project' forcelongurl=true assign='createLink'}
        {/if}
        {multisitesRelationSelectorAutoComplete group=$group id=$alias aliasReverse=$aliasReverse mandatory=$mandatory __title='Choose the projects' selectionMode='multiple' objectType='project' linkingItem=$linkingItem idPrefix=$idPrefix createLink=$createLink withImage=false}
        <div class="multisites-relation-leftside">
            {if isset($linkingItem.$alias)}
                {include file='project/include_selectItemListMany.tpl'  items=$linkingItem.$alias}
            {else}
                {include file='project/include_selectItemListMany.tpl' }
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
