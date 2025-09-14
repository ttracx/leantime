//Lets get this party started.
var safe4work = safe4work || {};

var themeColor = jQuery('meta[name=theme-color]').attr("content");
safe4work.companyColor = themeColor;

var colorScheme = jQuery('meta[name=color-scheme]').attr("content");
safe4work.colorScheme = colorScheme;

var theme = jQuery('meta[name=theme]').attr("content");
safe4work.theme = theme;

var appURL = jQuery('meta[name=identifier-URL]').attr("content");
safe4work.appUrl = appURL;

var safe4workVersion = jQuery('meta[name=safe4work-version]').attr("content");
safe4work.version = safe4workVersion;

safe4work.replaceSVGColors = function () {

    jQuery(document).ready(function () {

        if (safe4work.companyColor != "#1b75bb") {
            jQuery("svg").children().each(function () {
                if (jQuery(this).attr("fill") == "#1b75bb") {
                    jQuery(this).attr("fill", safe4work.companyColor);
                }
            });
        }

    });

};

leantime.handleAsyncResponse = function (response) {

    if (response !== undefined) {
        if (response.result !== undefined && response.result.html !== undefined) {
            var content = jQuery(response.result.html);
            jQuery("body").append(content);
        }
    }
};

jQuery.noConflict();

jQuery(document).ready(function () {

    safe4work.replaceSVGColors();

    jQuery(".confetti").click(function () {
        confetti.start();
    });

    tippy('[data-tippy-content]');

    if (jQuery('.login-alert .alert').text() !== '') {
        jQuery('.login-alert').fadeIn();
    }

    document.addEventListener('scroll', () => {
        document.documentElement.dataset.scroll = window.scrollY;
    });

});

htmx.onLoad(function(element){
    tippy('[data-tippy-content]');
});

window.addEventListener("HTMX.ShowNotification", function(evt) {
    jQuery.get(safe4work.appUrl+"/notifications/getLatestGrowl", function(data){
        let notification = JSON.parse(data);

        if(notification.notification && notification.notification !== "undefined") {
            jQuery.growl({
                message: notification.notification, style: notification.notificationType
            });
        }
    })
});
