{* purpose of this template: sites xml inclusion template *}
<site id="{$item.id}" createdon="{$item.createdDate|dateformat}" updatedon="{$item.updatedDate|dateformat}">
    <id>{$item.id}</id>
    <name><![CDATA[{$item.name}]]></name>
    <description><![CDATA[{$item.description}]]></description>
    <siteAlias><![CDATA[{$item.siteAlias}]]></siteAlias>
    <siteName><![CDATA[{$item.siteName}]]></siteName>
    <siteDescription><![CDATA[{$item.siteDescription}]]></siteDescription>
    <siteAdminName><![CDATA[{$item.siteAdminName}]]></siteAdminName>
    <siteAdminPassword><![CDATA[]]></siteAdminPassword>
    <siteAdminRealName><![CDATA[{$item.siteAdminRealName}]]></siteAdminRealName>
    <siteAdminEmail>{$item.siteAdminEmail}</siteAdminEmail>
    <siteCompany><![CDATA[{$item.siteCompany}]]></siteCompany>
    <siteDns><![CDATA[{$item.siteDns}]]></siteDns>
    <databaseName><![CDATA[{$item.databaseName}]]></databaseName>
    <databaseUserName><![CDATA[{$item.databaseUserName}]]></databaseUserName>
    <databasePassword><![CDATA[]]></databasePassword>
    <databaseHost><![CDATA[{$item.databaseHost}]]></databaseHost>
    <databaseType><![CDATA[{$item.databaseType}]]></databaseType>
    <logo{if $item.logo ne ''} extension="{$item.logoMeta.extension}" size="{$item.logoMeta.size}" isImage="{if $item.logoMeta.isImage}true{else}false{/if}"{if $item.logoMeta.isImage} width="{$item.logoMeta.width}" height="{$item.logoMeta.height}" format="{$item.logoMeta.format}"{/if}{/if}>{$item.logo}</logo>
    <favIcon{if $item.favIcon ne ''} extension="{$item.favIconMeta.extension}" size="{$item.favIconMeta.size}" isImage="{if $item.favIconMeta.isImage}true{else}false{/if}"{if $item.favIconMeta.isImage} width="{$item.favIconMeta.width}" height="{$item.favIconMeta.height}" format="{$item.favIconMeta.format}"{/if}{/if}>{$item.favIcon}</favIcon>
    <allowedLocales>{$item.allowedLocales}</allowedLocales>
    <parametersCsvFile{if $item.parametersCsvFile ne ''} extension="{$item.parametersCsvFileMeta.extension}" size="{$item.parametersCsvFileMeta.size}" isImage="{if $item.parametersCsvFileMeta.isImage}true{else}false{/if}"{if $item.parametersCsvFileMeta.isImage} width="{$item.parametersCsvFileMeta.width}" height="{$item.parametersCsvFileMeta.height}" format="{$item.parametersCsvFileMeta.format}"{/if}{/if}>{$item.parametersCsvFile}</parametersCsvFile>
    <parametersArray>{$item.parametersArray}</parametersArray>
    <active>{if !$item.active}0{else}1{/if}</active>
    <workflowState>{$item.workflowState|multisitesObjectState:false|lower}</workflowState>
    <template>{if isset($item.Template) && $item.Template ne null}{$item.Template->getTitleFromDisplayPattern()|default:''}{/if}</template>
    <project>{if isset($item.Project) && $item.Project ne null}{$item.Project->getTitleFromDisplayPattern()|default:''}{/if}</project>
    <extensions>
    {if isset($item.Extensions) && $item.Extensions ne null}
        {foreach name='relationLoop' item='relatedItem' from=$item.Extensions}
        <siteExtension>{$relatedItem->getTitleFromDisplayPattern()|default:''}</siteExtension>
        {/foreach}
    {/if}
    </extensions>
</site>
