define(['jquery', 'jquery/ui'], function($) {
    'use strict';

    $.widget('bydn.virtualMirrorProductAlert', {

        // Default options
        options: {
            buttonSelector: '',  // selector that triggers the event
            generateImageEndpoint: '' // endpoint to generate the image
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
            var self = this;
            
            $.ajax({
                url: self.options.generateImageEndpoint,
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('body').trigger('processStart');
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Success:', response);
                    } else {

                        console.error('Error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Request failed:', error);
                },
                complete: function() {
                    $('body').trigger('processStop');
                }
            });
        }
    });

    // Devolvemos el widget que acabamos de crear.
    return $.bydn.virtualMirrorProductAlert;
});
