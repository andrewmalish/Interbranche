var config = {
    map: {
        '*': {
            quickview:            'Interbranche_QuickView/js/quickview',
            categoryConfigurable: 'Interbranche_QuickView/js/categoryConfigurable'
        }
    },
    config: {
        mixins: {
            'Interbranche_QuickView/js/categoryConfigurable': {
                'Interbranche_ConfigurableSkuSwitcher/js/model/skuswitch': true
            }
        }
    }
};
