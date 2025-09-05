define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function($) {
    'use strict';

    $.widget('bydn.virtualMirrorProductAlert', {

        // Default options
        options: {
            buttonSelector: '',  // selector that triggers the event
            generateImageEndpoint: '', // endpoint to generate the image
            productId: '' // current product ID
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
                data: {
                    product_id: self.options.productId
                },
                beforeSend: function() {
                    $('body').trigger('processStart');
                },
                success: function(response) {
                    if (response.success) {
                        // Create modal content with the image
                        var modalContent = $('<div>').append(
                            $('<img>', {
                                src: response.url,
                                alt: 'Virtual Mirror Image',
                                style: 'max-width: 100%; height: auto;'
                            })
                        );

                        // Initialize and open modal
                        modalContent.modal({
                            type: 'popup',
                            responsive: true,
                            innerScroll: true,
                            modalClass: 'virtual-mirror-modal',
                            title: $.mage.__('Virtual Mirror'),
                            buttons: [{
                                text: $.mage.__('Close'),
                                class: 'action-primary',
                                click: function() {
                                    this.closeModal();
                                }
                            }]
                        }).modal('openModal');
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
