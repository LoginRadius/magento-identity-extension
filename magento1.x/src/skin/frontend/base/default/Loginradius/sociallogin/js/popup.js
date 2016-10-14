// variable to check if submit button of popup is clicked
var loginRadiusPopupSubmit = true;
// get trim() worked in IE
if (typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
    }
}
// validate numeric data
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
// validate required fields form
function loginRadiusValidateForm() {
    var loginRadiusForm = document.getElementById('loginRadiusForm');
    if (!loginRadiusPopupSubmit) {
        loginRadiusForm.setAttribute('action', window.location.href);
        return true;
    }
    var loginRadiusErrorDiv = document.getElementById('lr-message');
    if (document.getElementById('loginRadiusCountry') != null && document.getElementById('loginRadiusCountry').value.trim() == "US") {
        var validateProvince = true;
    } else {
        var validateProvince = false;
    }
    for (var i = 0; i < loginRadiusForm.elements.length; i++) {
        if (!validateProvince && loginRadiusForm.elements[i].id == "loginRadiusProvince") {
            continue;
        }
        if (loginRadiusForm.elements[i].value.trim() == "" && loginRadiusForm.elements[i].id != "loginRadiusEmail") {
            loginRadiusErrorDiv.innerHTML = LRpopupErrorMessage;
            loginRadiusErrorDiv.style.backgroundColor = "rgb(255, 235, 232)";
            loginRadiusErrorDiv.style.textAlign = "left";
            return false;
        }
        if (loginRadiusForm.elements[i].id == "loginRadiusEmail") {
            var email = loginRadiusForm.elements[i].value.trim();
            var atPosition = email.indexOf("@");
            var dotPosition = email.lastIndexOf(".");
            if (atPosition < 1 || dotPosition < atPosition + 2 || dotPosition + 2 >= email.length) {
                loginRadiusErrorDiv.innerHTML = "Please enter a valid email address.";
                loginRadiusErrorDiv.style.backgroundColor = "rgb(255, 235, 232)";
                loginRadiusErrorDiv.style.textAlign = "left";
                return false;
            }
        }
    }
    return true;
}