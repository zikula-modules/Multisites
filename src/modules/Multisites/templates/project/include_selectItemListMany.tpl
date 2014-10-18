{* purpose of this template: inclusion template for display of related projects *}
{icon type='delete' size='extrasmall' assign='removeImageArray'}
{assign var='removeImage' value="<img src=\"`$removeImageArray.src`\" width=\"16\" height=\"16\" alt=\"\" />"}

<input type="hidden" id="{$idPrefix}ItemList" name="{$idPrefix}ItemList" value="{if isset($items) && (is_array($items) || is_object($items))}{foreach name='relLoop' item='item' from=$items}{$item.id}{if $smarty.foreach.relLoop.last ne true},{/if}{/foreach}{/if}" />
<input type="hidden" id="{$idPrefix}Mode" name="{$idPrefix}Mode" value="0" />

<ul id="{$idPrefix}ReferenceList">
{if isset($items) && (is_array($items) || is_object($items))}
{foreach name='relLoop' item='item' from=$items}
{assign var='idPrefixItem' value="`$idPrefix`Reference_`$item.id`"}
<li id="{$idPrefixItem}">
    {$item->getTitleFromDisplayPattern()}
     <a id="{$idPrefixItem}Remove" href="javascript:multisitesRemoveRelatedItem('{$idPrefix}', '{$item.id}');">{$removeImage}</a>
</li>
{/foreach}
{/if}
</ul>
