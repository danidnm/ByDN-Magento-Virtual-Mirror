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
            var self = this;

            // Initialize the modal
            this.modal = $('#virtual-mirror-modal').modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'virtual-mirror-modal',
                title: $.mage.__('Virtual Mirror'),
                buttons: [{
                    text: $.mage.__('Close'),
                    class: 'action-primary',
                    click: function() {
                        self.modal.modal('closeModal');
                    }
                }]
            });

            // Bind the button to the method execute
            if (this.options.buttonSelector) {
                this._on($(this.options.buttonSelector), {
                    'click': this.execute
                });
            }
        },

        execute: function() {
            var self = this;
            
            // Reset modal state
            $('.virtual-mirror-modal .loading-message').show();
            $('.virtual-mirror-modal .error-message').hide();
            $('.virtual-mirror-modal .virtual-mirror-image-container').empty();
            
            // Show modal immediately
            self.modal.modal('openModal');
            
            $.ajax({
                url: self.options.generateImageEndpoint,
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: self.options.productId
                },
                success: function(response) {
                    if (response.success) {
                        // Hide loading message
                        $('.virtual-mirror-modal .loading-message').hide();
                        
                        // Create and append the image
                        var img = $('<img>', {
                            src: response.url,
                            alt: 'Virtual Mirror Image',
                            style: 'max-width: 100%; height: auto;'
                        });
                        
                        $('.virtual-mirror-modal .virtual-mirror-image-container').append(img);
                    } else {
                        $('.virtual-mirror-modal .loading-message').hide();
                        $('.virtual-mirror-modal .error-message')
                            .text(response.message || $.mage.__('An error occurred while generating your image.'))
                            .show();
                    }
                },
                error: function(xhr, status, error) {
                    $('.virtual-mirror-modal .loading-message').hide();
                    $('.virtual-mirror-modal .error-message').show();
                    console.error('Request failed:', error);
                }
            });
        }
    });

    // Devolvemos el widget que acabamos de crear.
    return $.bydn.virtualMirrorProductAlert;
});
