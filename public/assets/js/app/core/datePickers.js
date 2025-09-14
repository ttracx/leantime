safe4work.dateController = (function () {

    function getBaseDatePickerConfig(callback)
    {

        return {
            numberOfMonths: 1,
            dateFormat:  safe4work.dateHelper.getFormatFromSettings("dateformat", "jquery"),
            dayNames: safe4work.i18n.__("language.dayNames").split(","),
            dayNamesMin:  safe4work.i18n.__("language.dayNamesMin").split(","),
            dayNamesShort: safe4work.i18n.__("language.dayNamesShort").split(","),
            monthNames: safe4work.i18n.__("language.monthNames").split(","),
            monthNamesShort: safe4work.i18n.__("language.monthNamesShort").split(","),
            currentText: safe4work.i18n.__("language.currentText"),
            closeText: safe4work.i18n.__("language.closeText"),
            buttonText: safe4work.i18n.__("language.buttonText"),
            isRTL: safe4work.i18n.__("language.isRTL") === "true" ? 1 : 0,
            nextText: safe4work.i18n.__("language.nextText"),
            prevText: safe4work.i18n.__("language.prevText"),
            weekHeader: safe4work.i18n.__("language.weekHeader"),
            firstDay: safe4work.i18n.__("language.firstDayOfWeek"),
            onSelect: callback

        };
    }

    function getDate( element )
    {

        var dateFormat =  safe4work.dateHelper.getFormatFromSettings("dateformat", "jquery");
        var date;

        try {
            date = jQuery.datepicker.parseDate(dateFormat, element.value);
        } catch ( error ) {
            date = null;
            console.log(error);
        }

        return date;
    }

    var initDateRangePicker = function (fromElement, toElement, minDistance) {

        Date.prototype.addDays = function (days) {
            this.setDate(this.getDate() + days);
            return this;
        };

        //Check for readonly status and disable datepicker if readonly
        jQuery.datepicker.setDefaults({
            beforeShow: function (i) {
                if (jQuery(i).attr('readonly')) {
                    return false;
                }
            }
        });

        var from = jQuery(fromElement).datepicker(getBaseDatePickerConfig())
                   .on(
                       "change",
                       function (date) {
                           to.datepicker("option", "minDate", getDate(this));

                           if (jQuery(toElement).val() == '') {
                               jQuery(toElement).val(jQuery(fromElement).val());
                           }
                       }
                   );

        var to = jQuery(toElement).datepicker(getBaseDatePickerConfig())
                 .on(
                     "change",
                        function () {
                            from.datepicker("option", "maxDate", getDate(this));
                        }
                 );
    };

    var initDatePicker = function (element, callback) {
        jQuery(element).datepicker(
            getBaseDatePickerConfig(callback)
        );
    }

    // Make public what you want to have public, everything else is private
    return {
        initDateRangePicker:initDateRangePicker,
        initDatePicker:initDatePicker,
    };

})();
