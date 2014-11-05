'use strict';


/**
 * Override method of Scriptaculous auto completer method.
 * Purpose: better feedback if no results are found (#247).
 * See http://stackoverflow.com/questions/657839/scriptaculous-ajax-autocomplete-empty-response for more information.
 */
Ajax.Autocompleter.prototype.updateChoices = function (choices)
{
    if (!this.changed && this.hasFocus) {
        if (!choices || choices == '<ul></ul>') {
            this.stopIndicator();
            var idPrefix = this.options.indicator.replace('Indicator', '');
            if ($(idPrefix + 'NoResultsHint') != null) {
                $(idPrefix + 'NoResultsHint').removeClassName('z-hide');
            }
        } else {
            this.update.innerHTML = choices;
            Element.cleanWhitespace(this.update);
            Element.cleanWhitespace(this.update.down());

            if (this.update.firstChild && this.update.down().childNodes) {
                this.entryCount = this.update.down().childNodes.length;
                for (var i = 0; i < this.entryCount; i++) {
                    var entry = this.getEntry(i);
                    entry.autocompleteIndex = i;
                    this.addObservers(entry);
                }
            } else {
                this.entryCount = 0;
            }

            this.stopIndicator();
            this.index = 0;

            if (this.entryCount == 1 && this.options.autoSelect) {
                this.selectEntry();
                this.hide();
            } else {
                this.render();
            }
        }
    }
}

/**
 * Resets the value of an upload / file input field.
 */
function multisitesResetUploadField(fieldName)
{
    if ($(fieldName) != null) {
        $(fieldName).setAttribute('type', 'input');
        $(fieldName).setAttribute('type', 'file');
    }
}

/**
 * Initialises the reset button for a certain upload input.
 */
function multisitesInitUploadField(fieldName)
{
    var fieldNameCapitalised;

    fieldNameCapitalised = fieldName.charAt(0).toUpperCase() + fieldName.substring(1);
    if ($('reset' + fieldNameCapitalised + 'Val') != null) {
        $('reset' + fieldNameCapitalised + 'Val').observe('click', function (evt) {
            evt.preventDefault();
            multisitesResetUploadField(fieldName);
        }).removeClassName('z-hide').setStyle({ display: 'block' });
    }
}

/**
 * Resets the value of a date or datetime input field.
 */
function multisitesResetDateField(fieldName)
{
    if ($(fieldName) != null) {
        $(fieldName).value = '';
    }
    if ($(fieldName + 'cal') != null) {
        $(fieldName + 'cal').update(Zikula.__('No date set.', 'module_multisites_js'));
    }
}

/**
 * Initialises the reset button for a certain date input.
 */
function multisitesInitDateField(fieldName)
{
    var fieldNameCapitalised;

    fieldNameCapitalised = fieldName.charAt(0).toUpperCase() + fieldName.substring(1);
    if ($('reset' + fieldNameCapitalised + 'Val') != null) {
        $('reset' + fieldNameCapitalised + 'Val').observe('click', function (evt) {
            evt.preventDefault();
            multisitesResetDateField(fieldName);
        }).removeClassName('z-hide').setStyle({ display: 'block' });
    }
}

/**
 * Toggles the fields of an auto completion field.
 */
function multisitesToggleRelatedItemForm(idPrefix)
{
    // if we don't have a toggle link do nothing
    if ($(idPrefix + 'AddLink') === null) {
        return;
    }

    // show/hide the toggle link
    $(idPrefix + 'AddLink').toggleClassName('z-hide');

    // hide/show the fields
    $(idPrefix + 'AddFields').toggleClassName('z-hide');
}

/**
 * Resets an auto completion field.
 */
function multisitesResetRelatedItemForm(idPrefix)
{
    // hide the sub form
    multisitesToggleRelatedItemForm(idPrefix);

    // reset value of the auto completion field
    $(idPrefix + 'Selector').value = '';
}

/**
 * Helper function to create new Zikula.UI.Window instances.
 * For edit forms we use "iframe: true" to ensure file uploads work without problems.
 * For all other windows we use "iframe: false" because we want the escape key working.
 */
function multisitesCreateRelationWindowInstance(containerElem, useIframe)
{
    var newWindow;

    // define the new window instance
    newWindow = new Zikula.UI.Window(
        containerElem,
        {
            minmax: true,
            resizable: true,
            //title: containerElem.title,
            width: 600,
            initMaxHeight: 500,
            modal: false,
            iframe: useIframe
        }
    );

    // open it
    newWindow.openHandler();

    // return the instance
    return newWindow;
}

/**
 * Observe a link for opening an inline window
 */
function multisitesinitInlineRelationWindow(objectType, containerID)
{
    var found, newItem;

    // whether the handler has been found
    found = false;

    // search for the handler
    relationHandler.each(function (relationHandler) {
        // is this the right one
        if (relationHandler.prefix === containerID) {
            // yes, it is
            found = true;
            // look whether there is already a window instance
            if (relationHandler.windowInstance !== null) {
                // unset it
                relationHandler.windowInstance.destroy();
            }
            // create and assign the new window instance
            relationHandler.windowInstance = multisitesCreateRelationWindowInstance($(containerID), true);
        }
    });

    // if no handler was found
    if (found === false) {
        // create a new one
        newItem = new Object();
        newItem.ot = objectType;
        newItem.alias = '';
        newItem.prefix = containerID;
        newItem.acInstance = null;
        newItem.windowInstance = multisitesCreateRelationWindowInstance($(containerID), true);

        // add it to the list of handlers
        relationHandler.push(newItem);
    }
}

/**
 * Removes a related item from the list of selected ones.
 */
function multisitesRemoveRelatedItem(idPrefix, removeId)
{
    var itemIds, itemIdsArr;

    itemIds = $F(idPrefix + 'ItemList');
    itemIdsArr = itemIds.split(',');

    itemIdsArr = itemIdsArr.without(removeId);

    itemIds = itemIdsArr.join(',');

    $(idPrefix + 'ItemList').value = itemIds;
    $(idPrefix + 'Reference_' + removeId).remove();
}

/**
 * Adds a related item to selection which has been chosen by auto completion.
 */
function multisitesSelectRelatedItem(objectType, idPrefix, inputField, selectedListItem)
{
    var newItemId, newTitle, includeEditing, editLink, removeLink, elemPrefix, itemPreview, li, editHref, fldPreview, itemIds, itemIdsArr;

    newItemId = selectedListItem.id;
    newTitle = $F(idPrefix + 'Selector');
    includeEditing = !!(($F(idPrefix + 'Mode') == '1'));
    elemPrefix = idPrefix + 'Reference_' + newItemId;
    itemPreview = '';

    if ($('itemPreview' + selectedListItem.id) !== null) {
        itemPreview = $('itemPreview' + selectedListItem.id).innerHTML;
    }

    var li = Builder.node('li', {id: elemPrefix}, newTitle);
    if (includeEditing === true) {
        var editHref = $(idPrefix + 'SelectorDoNew').href + '&id=' + newItemId;
        editLink = Builder.node('a', {id: elemPrefix + 'Edit', href: editHref}, 'edit');
        li.appendChild(editLink);
    }
    removeLink = Builder.node('a', {id: elemPrefix + 'Remove', href: 'javascript:multisitesRemoveRelatedItem(\'' + idPrefix + '\', ' + newItemId + ');'}, 'remove');
    li.appendChild(removeLink);
    if (itemPreview !== '') {
        fldPreview = Builder.node('div', {id: elemPrefix + 'preview', name: idPrefix + 'preview'}, '');
        fldPreview.update(itemPreview);
        li.appendChild(fldPreview);
        itemPreview = '';
    }
    $(idPrefix + 'ReferenceList').appendChild(li);

    if (includeEditing === true) {
        editLink.update(' ' + editImage);

        $(elemPrefix + 'Edit').observe('click', function (e) {
            multisitesinitInlineRelationWindow(objectType, idPrefix + 'Reference_' + newItemId + 'Edit');
            e.stop();
        });
    }
    removeLink.update(' ' + removeImage);

    itemIds = $F(idPrefix + 'ItemList');
    if (itemIds !== '') {
        if ($F(idPrefix + 'Scope') === '0') {
            itemIdsArr = itemIds.split(',');
            itemIdsArr.each(function (existingId) {
                if (existingId) {
                    multisitesRemoveRelatedItem(idPrefix, existingId);
                }
            });
            itemIds = '';
        } else {
            itemIds += ',';
        }
    }
    itemIds += newItemId;
    $(idPrefix + 'ItemList').value = itemIds;

    multisitesResetRelatedItemForm(idPrefix);
}

/**
 * Initialise a relation field section with autocompletion and optional edit capabilities
 */
function multisitesInitRelationItemsForm(objectType, idPrefix, includeEditing)
{
    var acOptions, itemIds, itemIdsArr;

    // add handling for the toggle link if existing
    if ($(idPrefix + 'AddLink') !== null) {
        $(idPrefix + 'AddLink').observe('click', function (e) {
            multisitesToggleRelatedItemForm(idPrefix);
        });
    }
    // add handling for the cancel button
    if ($(idPrefix + 'SelectorDoCancel') !== null) {
        $(idPrefix + 'SelectorDoCancel').observe('click', function (e) {
            multisitesResetRelatedItemForm(idPrefix);
        });
    }
    // clear values and ensure starting state
    multisitesResetRelatedItemForm(idPrefix);

    acOptions = {
        paramName: 'fragment',
        minChars: 2,
        indicator: idPrefix + 'Indicator',
        callback: function (inputField, defaultQueryString) {
            var queryString;

            // modify the query string before the request
            queryString = defaultQueryString + '&ot=' + objectType;
            if ($(idPrefix + 'ItemList') !== null) {
                queryString += '&exclude=' + $F(idPrefix + 'ItemList');
            }

            if ($(idPrefix + 'NoResultsHint') != null) {
                $(idPrefix + 'NoResultsHint').addClassName('z-hide');
            }

            return queryString;
        },
        afterUpdateElement: function (inputField, selectedListItem) {
            // Called after the input element has been updated (i.e. when the user has selected an entry).
            // This function is called after the built-in function that adds the list item text to the input field.
            multisitesSelectRelatedItem(objectType, idPrefix, inputField, selectedListItem);
        }
    };
    relationHandler.each(function (relationHandler) {
        if (relationHandler.prefix === (idPrefix + 'SelectorDoNew') && relationHandler.acInstance === null) {
            relationHandler.acInstance = new Ajax.Autocompleter(
                idPrefix + 'Selector',
                idPrefix + 'SelectorChoices',
                Zikula.Config.baseURL + 'ajax.php?module=' + relationHandler.moduleName + '&func=getItemListAutoCompletion',
                acOptions
            );
        }
    });

    if (!includeEditing || $(idPrefix + 'SelectorDoNew') === null) {
        return;
    }

    // from here inline editing will be handled
    $(idPrefix + 'SelectorDoNew').href += '&theme=Printer&idp=' + idPrefix + 'SelectorDoNew';
    $(idPrefix + 'SelectorDoNew').observe('click', function(e) {
        multisitesinitInlineRelationWindow(objectType, idPrefix + 'SelectorDoNew');
        e.stop();
    });

    itemIds = $F(idPrefix + 'ItemList');
    itemIdsArr = itemIds.split(',');
    itemIdsArr.each(function (existingId) {
        var elemPrefix;

        if (existingId) {
            elemPrefix = idPrefix + 'Reference_' + existingId + 'Edit';
            $(elemPrefix).href += '&theme=Printer&idp=' + elemPrefix;
            $(elemPrefix).observe('click', function (e) {
                multisitesinitInlineRelationWindow(objectType, elemPrefix);
                e.stop();
            });
        }
    });
}

/**
 * Closes an iframe from the document displayed in it
 */
function multisitesCloseWindowFromInside(idPrefix, itemId)
{
    // if there is no parent window do nothing
    if (window.parent === '') {
        return;
    }

    // search for the handler of the current window
    window.parent.relationHandler.each(function (relationHandler) {
        // look if this handler is the right one
        if (relationHandler['prefix'] === idPrefix) {
            // do we have an item created
            if (itemId > 0) {
                // look whether there is an auto completion instance
                if (relationHandler.acInstance !== null) {
                    // activate it
                    relationHandler.acInstance.activate();
                    // show a message
                    Zikula.UI.Alert(Zikula.__('Action has been completed.', 'module_multisites_js'), Zikula.__('Information', 'module_multisites_js'), {
                        autoClose: 3 // time in seconds
                    });
                }
            }
            // look whether there is a windows instance
            if (relationHandler.windowInstance !== null) {
                // close it
                relationHandler.windowInstance.closeHandler();
            }
        }
    });
}
