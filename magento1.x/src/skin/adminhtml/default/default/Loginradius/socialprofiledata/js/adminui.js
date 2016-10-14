var $loginRadiusJquery = jQuery.noConflict();

$loginRadiusJquery(window).load(function () {
    $loginRadiusJquery('#system_config_tabs').find('.active').each(function () {
        if ($loginRadiusJquery(this).text().replace(/\s/g, "") == "") {
            $loginRadiusJquery(this).parents('dd').hide();
        }
    });
    var loginRadiusProfileFields = [
        {value: 'basic',
            text: 'Basic Profile Data <a href="javascript:void(0)" title="Data fields include: Social ID, Social ID Provider, First Name, Middle Name, Last Name, Full Name, Nick Name, Profile Name, Birthdate, Gender, Country Code, Country Name, Thumbnail Image Url, Image Url, Local Country, Profile Country" style="text-decoration:none">(?)</a>'},
        {value: 'ex_location',
            text: 'Extended Location Data <a href="javascript:void(0)" title="Data fields include: Main Address, Hometown, State, City, Local City, Profile City, Profile Url, Local Language, Language" style="text-decoration:none">(?)</a>'},
        {value: 'ex_profile',
            text: 'Extended Profile Data <a href="javascript:void(0)" title="Data fields include: Website, Favicon, Industry, About, Timezone, Verified, Last Profile Update, Created, Relationship Status, Favorite Quote, Interested In, Interests, Religion, Political View, HTTPS Image Url, Followers Count, Friends Count, Is Geo Enabled, Total Status Count, Number of Recommenders, Honors, Associations, Hirable, Repository Url, Age, Professional Headline, Provider Access Token, Provider Token Secret, Positions, Companies, Education, Phone Numbers, IM Accounts, Addresses, Sports, Inspirational People, Skills, Current Status, Certifications, Courses, Volunteer, Recommendations Received, Languages, Patents, Favorites" style="text-decoration:none">(?)</a>'},
        {value: 'linkedin_companies',
            text: 'Companies <a href="javascript:void(0)" title="A list of all the companies this user follows." style="text-decoration:none">(?)</a>'},
        {value: 'events',
            text: 'Facebook Profile Events <a href="javascript:void(0)" title="A list of events (birthdays, invitation, etc.) on the Facebook profile of user" style="text-decoration:none">(?)</a>'},
        {value: 'status',
            text: 'Status Messages <a href="javascript:void(0)" title="Facebook wall activity, Twitter tweets and LinkedIn status of the user, including links" style="text-decoration:none">(?)</a>'},
        {value: 'posts',
            text: 'Facebook Posts <a href="javascript:void(0)" title="Facebook posts of the user, including links" style="text-decoration:none">(?)</a>'},
        {value: 'mentions',
            text: 'Twitter Mentions <a href="javascript:void(0)" title="A list of tweets that the user is mentioned in." style="text-decoration:none">(?)</a>'},
        {value: 'groups',
            text: 'Groups <a href="javascript:void(0)" title="A list of the Facebook groups of user." style="text-decoration:none">(?)</a>'},
        {value: 'contacts',
            text: 'Contacts/Friend Data <a href="javascript:void(0)" title="For email providers (Google and Yahoo), a list of the contacts of user in his/her address book. For social networks (Facebook, Twitter, and LinkedIn), a list of the people in the network of user." style="text-decoration:none">(?)</a>'},
        {value: 'likes',
            text: 'Facebook Likes Data <a href="javascript:void(0)" title="Likes of the user For social networks (Facebook)." style="text-decoration:none">(?)</a>'},
    ];
    // get the reference to the <td> corressponding to the Social Profile Data option
    if (document.getElementById('row_socialprofiledata_profiledataoption_profiledatacheckboxes') != null) {
        var loginRadiusSocialProfileTds = document.getElementById('row_socialprofiledata_profiledataoption_profiledatacheckboxes').getElementsByTagName('td');
        // list these profile fields in the Social Profile Data option
        for (var ps = 0; ps < loginRadiusProfileFields.length; ps++) {
            var checkbox = document.createElement('input');
            checkbox.setAttribute('type', 'checkbox');
            checkbox.setAttribute('value', loginRadiusProfileFields[ps].value);
            checkbox.setAttribute('id', 'login_radius_social_profile_' + loginRadiusProfileFields[ps].value);
            checkbox.onclick = function () {
                loginRadiusPopulateProfileFields(this);
            }
            var label = document.createElement('label');
            label.setAttribute('for', 'login_radius_social_profile_' + loginRadiusProfileFields[ps].value);
            label.innerHTML = loginRadiusProfileFields[ps].text;
            loginRadiusSocialProfileTds[1].appendChild(checkbox);
            loginRadiusSocialProfileTds[1].appendChild(label);
            loginRadiusSocialProfileTds[1].appendChild(document.createElement('br'));
        }
        // append help text
        var helpText = document.createElement('div');
        helpText.setAttribute('style', 'clear: both !important;');
        helpText.innerHTML = '<p class="note"><span>Please select the user profile data fields you would like to save in your database. For a list of all fields: <a target="_blank" href="http://www.loginradius.com/profile-data">http://www.loginradius.com/profile-data</a></span></p>';
        loginRadiusSocialProfileTds[1].appendChild(helpText);
        // increase the width of the container <td>
        loginRadiusSocialProfileTds[1].style.width = '400px';

        // show profile fields checkbox checked according to the options saved.
        var loginRadiusProfileDataHidden = document.getElementById('socialprofiledata_profiledataoption_profiledata').value.trim();
        if (loginRadiusProfileDataHidden != "") {
            var loginRadiusProfileOptionsArray = loginRadiusProfileDataHidden.split(',');
            for (var i = 0; i < loginRadiusProfileOptionsArray.length; i++) {
                document.getElementById('login_radius_social_profile_' + loginRadiusProfileOptionsArray[i]).checked = true;
            }
        }
        loginradiusAdvancedProfile();
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes_inherit").click(function () {
            loginradiusAdvancedProfileDataCheckbox();
        });
        $loginRadiusJquery("#row_socialprofiledata_profiledataoption_profiledatacheckboxes .value input[type='checkbox']").click(function () {
            $loginRadiusJquery('#socialprofiledata_profiledataoption_profiledata').attr("disabled", false);
        });
    }
});

function loginradiusAdvancedProfileDataCheckbox() {
    if ($loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes_inherit").is(':checked')) {
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledata_inherit").attr('checked', true);
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes,#socialprofiledata_profiledataoption_profiledata").attr("disabled", true);
    } else {
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledata_inherit").attr('checked', false);
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes,#socialprofiledata_profiledataoption_profiledata").attr("disabled", false);
    }
}
function loginradiusAdvancedProfile() {
    if ($loginRadiusJquery("#socialprofiledata_profiledataoption_profiledata_inherit").is(':checked')) {
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes_inherit").attr('checked', true);
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes,#socialprofiledata_profiledataoption_profiledata").attr("disabled", false);
        $loginRadiusJquery("#row_socialprofiledata_profiledataoption_profiledatacheckboxes .value input[type='checkbox']").attr("disabled", true);
        $loginRadiusJquery("#row_socialprofiledata_profiledataoption_profiledatacheckboxes .value input[type='checkbox']").attr("class", "disabled");
    } else {
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes_inherit").attr('checked', false);
        $loginRadiusJquery("#socialprofiledata_profiledataoption_profiledatacheckboxes,#socialprofiledata_profiledataoption_profiledata").attr("disabled", true);
        $loginRadiusJquery("#row_socialprofiledata_profiledataoption_profiledatacheckboxes .value input[type='checkbox']").attr("disabled", false);
    }
}



// add/remove icons from counter hidden field
function loginRadiusPopulateProfileFields(elem) {
    // get providers hidden field value
    var providers = document.getElementById('socialprofiledata_profiledataoption_profiledata');
    if (elem.checked) {
        // add selected providers in the hiddem field value
        //if(typeof elem.checked != "undefined" || lrDefault == true){
        if (providers.value == "") {
            providers.value = elem.value;
        } else {
            providers.value += "," + elem.value;
        }
        //}
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