var $loginRadiusJquery = jQuery.noConflict();
$loginRadiusJquery(document).ready(function () {
    $loginRadiusJquery('#mailchimp_mailchimp_getListButton').val('Get Lists');
    $loginRadiusJquery('#mailchimp_mailchimp_getListButton').attr('onclick', 'lrMailChimpLists()');
    $loginRadiusJquery('#mailchimp_mailchimp_enable').attr('onclick', 'enablelrMailchimpOption()');
    $loginRadiusJquery('#mailchimp_mailchimp_getListButton').addClass('form-button');
    $loginRadiusJquery('<div class="getListButtonMessage"></div>').insertAfter('#mailchimp_mailchimp_getListButton');
    var mailchimpApikey = $loginRadiusJquery('#mailchimp_mailchimp_apikey').val();
    if ((mailchimpApikey != null) && (enablelrMailchimpOption())) {
        lrMailChimpLists();
        mailChimpApiMultisite();
    }
        mailchimpApikeyInherit();
        mailchimpListInherit();
        mailchimpMappingFieldsInherit();
        
});
function mailChimpApiMultisite() {
    $loginRadiusJquery('#mailchimp_mailchimp_apikey_inherit').click(function () {
        mailchimpApikeyInherit();
    });
    $loginRadiusJquery('#mailchimp_mailchimp_mappingFields_inherit').click(function () {
        mailchimpMappingFieldsInherit();
    });
    $loginRadiusJquery('#mailchimp_mailchimp_lists_inherit').click(function () {
        mailchimpListInherit();
    });
}
function mailchimpApikeyInherit() {
    if ($loginRadiusJquery('#mailchimp_mailchimp_apikey_inherit').is(':checked')) {
        $loginRadiusJquery('#mailchimp_mailchimp_getListButton_inherit').attr('checked', true);
        $loginRadiusJquery("#mailchimp_mailchimp_getListButton,.lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").attr("disabled", true);
        $loginRadiusJquery("#mailchimp_mailchimp_lists_inherit,#mailchimp_mailchimp_mappingFields_inherit,#mailchimp_mailchimp_mappingFieldsTag_inherit,#mailchimp_mailchimp_mappingFieldsValue_inherit").attr("checked", true);
        $loginRadiusJquery("#mailchimp_mailchimp_getListButton,#mailchimp_mailchimp_lists").addClass("disabled");
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").addClass("disabled");
    } else {
        $loginRadiusJquery('#mailchimp_mailchimp_getListButton_inherit').attr('checked', false);
        $loginRadiusJquery("#mailchimp_mailchimp_getListButton,.lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").attr("disabled", false);
        $loginRadiusJquery("#mailchimp_mailchimp_lists_inherit,#mailchimp_mailchimp_mappingFields_inherit,#mailchimp_mailchimp_mappingFieldsTag_inherit,#mailchimp_mailchimp_mappingFieldsValue_inherit").attr("checked", false);
        $loginRadiusJquery("#mailchimp_mailchimp_getListButton,#mailchimp_mailchimp_lists").removeClass("disabled");
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").removeClass("disabled");
    }
}
function mailchimpMappingFieldsInherit() {
    if ($loginRadiusJquery('#mailchimp_mailchimp_mappingFields_inherit').is(':checked')) {
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").attr("disabled", true);
        $loginRadiusJquery("#mailchimp_mailchimp_lists_inherit,#mailchimp_mailchimp_mappingFieldsTag_inherit,#mailchimp_mailchimp_mappingFieldsValue_inherit").attr("checked", true);
        $loginRadiusJquery("#mailchimp_mailchimp_lists,.lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").addClass("disabled");
    } else {
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").attr("disabled", false);
        $loginRadiusJquery("#mailchimp_mailchimp_lists_inherit,#mailchimp_mailchimp_mappingFieldsTag_inherit,#mailchimp_mailchimp_mappingFieldsValue_inherit").attr("checked", false);
        $loginRadiusJquery("#mailchimp_mailchimp_lists,.lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").removeClass("disabled");
    }
}

function mailchimpListInherit() {
    if ($loginRadiusJquery('#mailchimp_mailchimp_lists_inherit').is(':checked')) {
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").attr("disabled", true);
        $loginRadiusJquery("#mailchimp_mailchimp_mappingFields_inherit,#mailchimp_mailchimp_mappingFieldsTag_inherit,#mailchimp_mailchimp_mappingFieldsValue_inherit").attr("checked", true);
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").addClass("disabled");
    } else {
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").attr("disabled", false);
        $loginRadiusJquery("#mailchimp_mailchimp_mappingFields_inherit,#mailchimp_mailchimp_mappingFieldsTag_inherit,#mailchimp_mailchimp_mappingFieldsValue_inherit").attr("checked", false);
        $loginRadiusJquery(".lrmappingselect,#mailchimp_mailchimp_lists,#mailchimp_mailchimp_mappingFieldsTag,#mailchimp_mailchimp_mappingFieldsValue").removeClass("disabled");
    }
}
function enablelrMailchimpOption() {
    var enableLRMailchimp = $loginRadiusJquery('#mailchimp_mailchimp_enable').val();
    if (enableLRMailchimp == '0') {
        $loginRadiusJquery('#row_mailchimp_mailchimp_update,#row_mailchimp_mailchimp_apikey,#row_mailchimp_mailchimp_getListButton,#row_mailchimp_mailchimp_lists,#row_mailchimp_mailchimp_mappingFieldsHeading,#row_mailchimp_mailchimp_mappingFields').hide();
        return false;
    } else {
        $loginRadiusJquery('#row_mailchimp_mailchimp_update,#row_mailchimp_mailchimp_apikey,#row_mailchimp_mailchimp_getListButton').show();
        return true;
    }
}

function fillMappingFields() {
    var values = '';
    $loginRadiusJquery('#row_mailchimp_mailchimp_mappingFields .lrmappingselect').each(function () {
        values += $loginRadiusJquery(this).val() + ",";
    });
    $loginRadiusJquery('#mailchimp_mailchimp_mappingFieldsValue').val(values.substring(0, values.length - 1));

    values = '';
    $loginRadiusJquery('#row_mailchimp_mailchimp_mappingFields .lrmappinglabel input').each(function () {
        values += $loginRadiusJquery(this).val() + ",";
    });
    $loginRadiusJquery('#mailchimp_mailchimp_mappingFieldsTag').val(values.substring(0, values.length - 1));

}