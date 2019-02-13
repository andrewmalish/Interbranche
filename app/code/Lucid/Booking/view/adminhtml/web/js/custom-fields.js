requirejs([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/text'
], function (_, uiRegistry, text) {
    'use strict';
    return text.extend({


        visible: false,
        disabled:true,

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value)
        {

            if (value != 'undefined')
            {
                //Do your Ajx stuff here
            }
            return this._super();
        },
    });
});