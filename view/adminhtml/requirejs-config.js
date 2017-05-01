// https://magento.stackexchange.com/questions/172366/override-and-replace-default-js-component
var config = {
    map: {
        '*': {
            'Magento_Ui/js/grid/columns/actions': 'Flancer32_LoginAs/ui/js/grid/columns/actions',
            uiGridColumnsActions: 'Magento_Ui/js/grid/columns/actions'
        }
    }
};