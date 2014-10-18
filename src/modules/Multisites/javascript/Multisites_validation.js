'use strict';

function multisitesToday(format)
{
    var timestamp, todayDate, month, day, hours, minutes, seconds;

    timestamp = new Date();
    todayDate = '';
    if (format !== 'time') {
        month = new String((parseInt(timestamp.getMonth()) + 1));
        if (month.length === 1) {
            month = '0' + month;
        }
        day = new String(timestamp.getDate());
        if (day.length === 1) {
            day = '0' + day;
        }
        todayDate += timestamp.getFullYear() + '-' + month + '-' + day;
    }
    if (format === 'datetime') {
        todayDate += ' ';
    }
    if (format != 'date') {
        hours = new String(timestamp.getHours());
        if (hours.length === 1) {
            hours = '0' + hours;
        }
        minutes = new String(timestamp.getMinutes());
        if (minutes.length === 1) {
            minutes = '0' + minutes;
        }
        seconds = new String(timestamp.getSeconds());
        if (seconds.length === 1) {
            seconds = '0' + seconds;
        }
        todayDate += hours + ':' + minutes;// + ':' + seconds;
    }
    return todayDate;
}

// returns YYYY-MM-DD even if date is in DD.MM.YYYY
function multisitesReadDate(val, includeTime)
{
    // look if we have YYYY-MM-DD
    if (val.substr(4, 1) === '-' && val.substr(7, 1) === '-') {
        return val;
    }

    // look if we have DD.MM.YYYY
    if (val.substr(2, 1) === '.' && val.substr(4, 1) === '.') {
        var newVal = val.substr(6, 4) + '-' + val.substr(3, 2) + '-' + val.substr(0, 2);
        if (includeTime === true) {
            newVal += ' ' + val.substr(11, 5);
        }
        return newVal;
    }
}

function multisitesValidateNoSpace(val)
{
    var valStr;
    valStr = new String(val);

    return (valStr.indexOf(' ') === -1);
}

function multisitesValidateUploadExtension(val, elem)
{
    var fileExtension, allowedExtensions;
    if (val === '') {
        return true;
    }
    fileExtension = '.' + val.substr(val.lastIndexOf('.') + 1);
    allowedExtensions = $(elem.id + 'FileExtensions').innerHTML;
    allowedExtensions = '(.' + allowedExtensions.replace(/, /g, '|.').replace(/,/g, '|.') + ')$';
    allowedExtensions = new RegExp(allowedExtensions, 'i');

    return allowedExtensions.test(val);
}

/**
 * Adds special validation rules.
 */
function multisitesAddCommonValidationRules(objectType, id)
{
    Validation.addAllThese([
        ['validate-nospace', Zikula.__('No spaces', 'module_multisites_js'), function(val, elem) {
            return multisitesValidateNoSpace(val);
        }],
        ['validate-upload', Zikula.__('Please select a valid file extension.', 'module_multisites_js'), function(val, elem) {
            return multisitesValidateUploadExtension(val, elem);
        }],
    ]);
}
