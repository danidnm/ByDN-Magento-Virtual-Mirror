define(['jquery', 'jquery/ui'], function($) {
    'use strict';

    $.widget('bydn.virtualMirrorProductAlert', {

        // Default options
        options: {
            buttonSelector: ''  // selector that triggers the event
        },

        _create: function() {

            // Bind the button to the method execute
            if (this.options.buttonSelector) {
                this._on($(this.options.buttonSelector), {
                    'click': this.execute
                });
            }
        },

        execute: function() {
            alert('¡Has pulsado el botón y llamado al método showAlert()!');
        }
    });

    // Devolvemos el widget que acabamos de crear.
    return $.bydn.virtualMirrorProductAlert;
});
