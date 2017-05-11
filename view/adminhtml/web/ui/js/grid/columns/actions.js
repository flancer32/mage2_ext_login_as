/**
 * Add target="_blank" for LoginAs actions in grid.
 */
define([
    "jquery",
    'ko',
    "uiGridColumnsActions"
], function ($, ko, Component) {
    "use strict"

    var ACTION_LOGINAS = 'loginas' // see \Flancer32\LoginAs\Config::GRID_ACTION_NAME

    /* https://magento.stackexchange.com/questions/172366/override-and-replace-default-js-component */
    return Component.extend({
        defaultCallback: function (actionIndex, recordId, action) {
            // \Flancer32\LoginAs\Plugin\Customer\Ui\Component\Listing\Column\Actions::afterPrepareDataSource
            // \Flancer32\LoginAs\Plugin\Sales\Ui\Component\Listing\Column\ViewAction::afterPrepareDataSource
            if (actionIndex == ACTION_LOGINAS) {
                /* open action link in new tab/window */
                var url = action.url
                window.open(url, '_blank');
                /* then collapse all action panels */
                var actionPanels = $('div.action-select-wrap')
                actionPanels.each(function (index) {
                    var ctx = ko.contextFor(this)
                    if (ctx && ctx.$collapsible) ctx.$collapsible.close()
                })

            } else {
                /* use parent function  */
                this.__proto__.defaultCallback(actionIndex, recordId, action)
            }
        }

    })
})