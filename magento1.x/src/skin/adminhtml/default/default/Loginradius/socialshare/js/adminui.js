// get trim() worked in IE 
if (typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    }
}
// validate numeric data 
function loginRadiusIsNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
var $loginRadiusJquery = jQuery.noConflict();

// prepare admin UI on window load
function loginRadiusPrepareAdminUI() {
    var horizontalSharingTheme, verticalSharingTheme;
    // fetch horizontal and vertical sharing providers dynamically from LoginRadius on window load 
    var sharingType = ['horizontal', 'vertical'];
    var sharingModes = ['sharing', 'counter'];
    // show the sharing/counter providers according to the selected sharing theme
    for (var j = 0; j < sharingType.length; j++) {
        if (document.getElementById('row_socialshare_' + sharingType[j] + 'sharing_' + sharingType[j] + 'sharingtheme') != null) {
            var loginRadiusHorizontalSharingThemes = document.getElementById('row_socialshare_' + sharingType[j] + 'sharing_' + sharingType[j] + 'sharingtheme').getElementsByTagName('input');
            for (var i = 0; i < loginRadiusHorizontalSharingThemes.length; i++) {
                if (sharingType[j] == 'horizontal') {
                    loginRadiusHorizontalSharingThemes[i].onclick = function () {
                        loginRadiusToggleSharingProviders(this, 'horizontal');
                    }
                } else if (sharingType[j] == 'vertical') {
                    loginRadiusHorizontalSharingThemes[i].onclick = function () {
                        loginRadiusToggleSharingProviders(this, 'vertical');
                    }
                }
                if (loginRadiusHorizontalSharingThemes[i].checked == true) {
                    if (sharingType[j] == 'horizontal') {
                        horizontalSharingTheme = loginRadiusHorizontalSharingThemes[i].value;
                    } else if (sharingType[j] == 'vertical') {
                        verticalSharingTheme = loginRadiusHorizontalSharingThemes[i].value;
                    }
                    loginRadiusToggleSharingProviders(loginRadiusHorizontalSharingThemes[i], sharingType[j]);
                }
            }
        }
    }

    // if selected sharing theme is worth showing rearrange icons, then show rearrange icons and manage sharing providers in hidden field
    for (var j = 0; j < sharingType.length; j++) {
        for (var jj = 0; jj < sharingModes.length; jj++) {
            // get sharing providers table-row reference
            var loginRadiusHorizontalSharingProvidersRow = document.getElementById('row_socialshare_' + sharingType[j] + 'sharing_' + sharingType[j] + sharingModes[jj] + 'providers');
            if (loginRadiusHorizontalSharingProvidersRow != null) {
                // get sharing providers checkboxes reference
                var loginRadiusHorizontalSharingProviders = loginRadiusHorizontalSharingProvidersRow.getElementsByTagName('input');
                for (var i = 0; i < loginRadiusHorizontalSharingProviders.length; i++) {
                    if (sharingType[j] == 'horizontal') {
                        if (sharingModes[jj] == 'sharing') {
                            loginRadiusHorizontalSharingProviders[i].onclick = function () {
                                loginRadiusShowIcon(false, this, 'horizontal');
                            }
                        } else {
                            loginRadiusHorizontalSharingProviders[i].onclick = function () {
                                loginRadiusPopulateCounter(this, 'horizontal');
                            }
                        }
                    } else if (sharingType[j] == 'vertical') {
                        if (sharingModes[jj] == 'sharing') {
                            loginRadiusHorizontalSharingProviders[i].onclick = function () {
                                loginRadiusShowIcon(false, this, 'vertical');
                            }
                        } else {
                            loginRadiusHorizontalSharingProviders[i].onclick = function () {
                                loginRadiusPopulateCounter(this, 'vertical');
                            }
                        }
                    }
                }
            }
            // check the sharing providers that were saved previously in the hidden field
            if (document.getElementById('socialshare_' + sharingType[j] + 'sharing_' + sharingType[j] + sharingModes[jj] + 'providershidden') != null) {
                var loginRadiusSharingProvidersHidden = document.getElementById('socialshare_' + sharingType[j] + 'sharing_' + sharingType[j] + sharingModes[jj] + 'providershidden').value.trim();
                if (loginRadiusSharingProvidersHidden != "") {
                    var loginRadiusSharingProviderArray = loginRadiusSharingProvidersHidden.split(',');
                    if (sharingModes[jj] == 'sharing') {
                        for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                            if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                                document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                                loginRadiusShowIcon(true, document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]), sharingType[j]);
                            }
                        }
                    } else {
                        for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                            if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                                document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                            }
                        }
                    }
                } else {
                    if (sharingModes[jj] == 'sharing') {
                        var loginRadiusSharingProviderArray = ["Facebook", "GooglePlus", "Twitter", "Pinterest", "Email", "Print"];
                        for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                            if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                                document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                                loginRadiusShowIcon(true, document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]), sharingType[j], true);
                            }
                        }
                    } else {
                        var loginRadiusSharingProviderArray = ["Facebook Like", "Google+ +1", "Twitter Tweet", "Pinterest Pin it", "LinkedIn Share"];
                        for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                            if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                                document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                                loginRadiusPopulateCounter(document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]), sharingType[j]);
                            }
                        }
                    }
                }
            }
        }
    }
}
// show sharing themes according to the selected option
function loginRadiusToggleSharing(theme) {
    if (typeof this.value == "undefined") {
        var sharingTheme = theme;
    } else {
        var sharingTheme = this.value;
    }
    if (sharingTheme == "horizontal") {
        document.getElementById('row_socialshare_sharing_horizontalsharing').style.display = 'table-row';
        document.getElementById('row_socialshare_sharing_verticalsharing').style.display = document.getElementById('row_socialshare_sharing_sharingverticalalignment').style.display = document.getElementById('row_socialshare_sharing_sharingoffset').style.display = 'none';
    } else if (sharingTheme == "vertical") {
        document.getElementById('row_socialshare_sharing_horizontalsharing').style.display = 'none';
        document.getElementById('row_socialshare_sharing_verticalsharing').style.display = document.getElementById('row_socialshare_sharing_sharingverticalalignment').style.display = document.getElementById('row_socialshare_sharing_sharingoffset').style.display = 'table-row';
    }
}
// show counter themes according to the selected option
function loginRadiusToggleCounter(theme) {
    if (typeof this.value == "undefined") {
        var counterTheme = theme;
    } else {
        var counterTheme = this.value;
    }
    if (counterTheme == "horizontal") {
        document.getElementById('row_socialshare_counter_verticalcounter').style.display = document.getElementById('row_socialshare_counter_counterverticalalignment').style.display = document.getElementById('row_socialshare_counter_counteroffset').style.display = 'none';
        document.getElementById('row_socialshare_counter_horizontalcounter').style.display = 'table-row';
    } else if (counterTheme == "vertical") {
        document.getElementById('row_socialshare_counter_horizontalcounter').style.display = 'none';
        document.getElementById('row_socialshare_counter_verticalcounter').style.display = document.getElementById('row_socialshare_counter_counterverticalalignment').style.display = document.getElementById('row_socialshare_counter_counteroffset').style.display = 'table-row';
    }
}
// limit maximum number of providers selected in sharing
function loginRadiusSharingLimit(elem, sharingType) {
    var checkCount = 0;
    // get providers table-row reference
    var loginRadiusSharingProvidersRow = document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingproviders');
    // get sharing providers checkboxes reference
    var loginRadiusSharingProviders = loginRadiusSharingProvidersRow.getElementsByTagName('input');
    for (var i = 0; i < loginRadiusSharingProviders.length; i++) {
        if (loginRadiusSharingProviders[i].checked) {
            // count checked providers
            checkCount++;
            if (checkCount >= 10) {
                elem.checked = false;
                if (document.getElementById('loginRadius' + sharingType + 'errordiv') == null) {
                    // create and show div having error message
                    var errorDiv = document.createElement('div');
                    errorDiv.setAttribute('id', 'loginRadius' + sharingType + 'errordiv');
                    errorDiv.innerHTML = "You can select only 9 providers.";
                    errorDiv.style.color = 'red';
                    errorDiv.style.marginBottom = '10px';
                    // append div to the <td> containing sharing provider checkboxes
                    var rearrangeTd = loginRadiusSharingProvidersRow.getElementsByTagName('td');
                    $loginRadiusJquery(rearrangeTd[1]).find('ul').before(errorDiv);
                }
                return;
            }
        }
    }
}
// add/remove icons from counter hidden field
function loginRadiusPopulateCounter(elem, sharingType, lrDefault) {
    // get providers hidden field value
    var providers = document.getElementById('socialshare_' + sharingType + 'sharing_' + sharingType + 'counterprovidershidden');
    if (elem.value != 1) {
        if (elem.checked) {
            // add selected providers in the hiddem field value
            if (typeof elem.checked != "undefined" || lrDefault == true) {
                if (providers.value == "") {
                    providers.value = elem.value;
                } else {
                    providers.value += "," + elem.value;
                }
            }
        } else {
            if (providers.value.indexOf(',') == -1) {
                providers.value = providers.value.replace(elem.value, "");
            } else {
                if (providers.value.indexOf("," + elem.value) == -1) {
                    providers.value = providers.value.replace(elem.value + ",", "");
                } else {
                    providers.value = providers.value.replace("," + elem.value, "");
                }
            }
        }
    }
}
// show selected providers in rearrange option
function loginRadiusShowIcon(pageRefresh, elem, sharingType, lrDefault) {
    loginRadiusSharingLimit(elem, sharingType);
    // get providers hidden field value
    var providers = document.getElementById('socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingprovidershidden');
    if (elem.value != 1) {
        if (elem.checked) {
            // get reference to "rearrange providers" <ul> element
            var ul = document.getElementById('loginRadius' + sharingType + 'rearrangesharing');
            // if <ul> is not already created
            if (ul == null) {
                // create <ul> element
                var ul = document.createElement('ul');
                ul.setAttribute('id', 'loginRadius' + sharingType + 'rearrangesharing');
                $loginRadiusJquery(ul).sortable({
                    update: function (e, ui) {
                        var val = $loginRadiusJquery(this).children().map(function () {
                            return $loginRadiusJquery(this).attr('title');
                        }).get().join();
                        $loginRadiusJquery(providers).val(val);
                    },
                    revert: true});
            }
            // create list items
            var listItem = document.createElement('li');
            listItem.setAttribute('id', 'loginRadius' + sharingType + 'LI' + elem.value);
            listItem.setAttribute('title', elem.value);
            listItem.setAttribute('class', 'ossshare_iconsprite32 ossshare_' + elem.value.toLowerCase());
            ul.appendChild(listItem);
            // add selected providers in the hiddem field value
            if (!pageRefresh || lrDefault == true) {
                if (providers.value == "") {
                    providers.value = elem.value;
                } else {
                    providers.value += "," + elem.value;
                }
            }
            // append <ul> to the <td>
            var rearrangeRow = document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingprovidershidden');
            var rearrangeTd = rearrangeRow.getElementsByTagName('td');
            rearrangeTd[1].appendChild(ul);
        } else {
            var remove = document.getElementById('loginRadius' + sharingType + 'LI' + elem.value);
            if (remove) {
                remove.parentNode.removeChild(remove);
            }
            if (providers.value.indexOf(',') == -1) {
                providers.value = providers.value.replace(elem.value, "");
            } else {
                if (providers.value.indexOf("," + elem.value) == -1) {
                    providers.value = providers.value.replace(elem.value + ",", "");
                } else {
                    providers.value = providers.value.replace("," + elem.value, "");
                }
            }
        }
    }
}


$loginRadiusJquery(document).ready(function () {
    var sharingType = ['horizontal', 'vertical'];
    var sharingModes = ['sharing', 'counter'];
    for (var i = 0; i < sharingType.length; i++) {
        for (var j = 0; j < sharingModes.length; j++) {
            if (sharingModes[j] == 'counter') {
                var providers = ["Facebook Like","Facebook Recommend","Facebook Send","Twitter Tweet","Pinterest Pin it","LinkedIn Share","StumbleUpon Badge","Reddit","Google+ +1","Google+ Share"];
            } else {
                var providers = ["Facebook","GooglePlus","LinkedIn","Twitter","Pinterest","Email","Google","Digg","Reddit","Vkontakte","Tumblr","Myspace","Delicious","Print"];
            }
            if (document.getElementById('row_socialshare_' + sharingType[i] + 'sharing_' + sharingType[i] + sharingModes[j] + 'providers') != null) {
                // populate sharing providers checkbox
                loginRadiusCounterHtml = "<ul class='checkboxes'>";
                // prepare HTML to be shown as Vertical Counter Providers
                for (var ii = 0; ii < providers.length; ii++) {
                    loginRadiusCounterHtml += '<li><input type="checkbox" id="' + sharingType[i] + '_' + sharingModes[j] + '_' + providers[ii] + '" ';
                    loginRadiusCounterHtml += 'value="' + providers[ii] + '"> <label for="' + sharingType[i] + '_' + sharingModes[j] + '_' + providers[ii] + '">' + providers[ii] + '</label></li>';
                }
                loginRadiusCounterHtml += "</ul>";

                var tds = document.getElementById('row_socialshare_' + sharingType[i] + 'sharing_' + sharingType[i] + sharingModes[j] + 'providers').getElementsByTagName('td');
                tds[1].innerHTML = loginRadiusCounterHtml;
                document.getElementById('row_socialshare_' + sharingType[i] + 'sharing_' + sharingType[i] + 'counterprovidershidden').style.display = 'none';
            }
        }
    }
    loginRadiusPrepareAdminUI();
    loginradiusChangeInheritCheckboxHidden('horizontalsharing', 'horizontalcounter');
    loginradiusChangeInheritCheckboxHidden('verticalsharing', 'verticalcounter');
    loginradiusChangeInheritCheckboxHidden('horizontalsharing', 'horizontalsharing');
    loginradiusChangeInheritCheckboxHidden('verticalsharing', 'verticalsharing');
    $loginRadiusJquery("#socialshare_horizontalsharing_horizontalcounterproviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('horizontalsharing', 'horizontalcounter');
    });
    $loginRadiusJquery("#socialshare_verticalsharing_verticalcounterproviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('verticalsharing', 'verticalcounter');
    });
    $loginRadiusJquery("#socialshare_horizontalsharing_horizontalsharingproviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('horizontalsharing', 'horizontalsharing');
    });
    $loginRadiusJquery("#socialshare_verticalsharing_verticalsharingproviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('verticalsharing', 'verticalsharing');
    });
    $loginRadiusJquery("#socialshare_horizontalsharing_horizontalsharingprovidershidden_inherit").click(function () {
        loginradiusChangeInheritCheckboxHidden('horizontalsharing', 'horizontalsharing');
    });
    $loginRadiusJquery("#socialshare_verticalsharing_verticalsharingprovidershidden_inherit").click(function () {
        loginradiusChangeInheritCheckboxHidden('verticalsharing', 'verticalsharing');
    });
    if($loginRadiusJquery('#googleanalytics_settings_enable').val() == '1'){
        $loginRadiusJquery('#row_googleanalytics_settings_enablesocialshare').show();
    }
});


function loginradiusChangeInheritCheckbox(shareId1, shareId2) {
    if ($loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providers_inherit").is(':checked')) {
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden_inherit").attr('checked', true);
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden").attr("disabled", true);
    } else {
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden_inherit").attr('checked', false);
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden").attr("disabled", false);
    }
}

function loginradiusChangeInheritCheckboxHidden(shareId1, shareId2) {
    if ($loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden_inherit").is(':checked')) {
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providers_inherit").attr('checked', true);
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden").attr("disabled", true);
    } else {
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providers_inherit").attr('checked', false);
        $loginRadiusJquery("#socialshare_" + shareId1 + "_" + shareId2 + "providershidden").attr("disabled", false);
    }
}

// toggle sharing/counter providers according to the theme and sharing type
function loginRadiusToggleSharingProviders(element, sharingType) {
    if (element.value == '32' || element.value == '16' || element.value == 'responsive') {
        document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingproviders').style.display = document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingprovidershidden').style.display = 'table-row';
        document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'counterproviders').style.display = 'none';
    } else if (element.value == 'single_large' || element.value == 'single_small') {
        document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingproviders').style.display = document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'counterproviders').style.display = 'none';
        document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingprovidershidden').style.display = 'none';
    } else {
        document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingproviders').style.display = document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'sharingprovidershidden').style.display = 'none';
        document.getElementById('row_socialshare_' + sharingType + 'sharing_' + sharingType + 'counterproviders').style.display = 'table-row';
    }
}

