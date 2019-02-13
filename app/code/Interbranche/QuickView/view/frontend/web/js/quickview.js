/*
 * @package     Interbranche\QuickView
 * @version     1.0.0
 */

define([
    "jquery",
    'mage/translate',
    'mage/template',
    'Magento_Customer/js/customer-data',
    'mage/gallery/gallery',
    'matchMedia',
    'Magento_Catalog/product/view/validation',
], function ($, $t, mageTemplate, $cd, gallery, mediaCheck) {
    "use strict";

    $.widget(
        'ba.QuickView',
        {
            options: {
                baseUrl: '/',
                currentText: $t('Product {current} of {total}'),
                closeText: $t('X'),
                maxWidth: "1260",
                initialWidth: "75",
                initialHeight: "75",
                itemClass: '.products.grid .item.product-item, .products.list .item.product-item',
                btnLabel: $t('QUICKVIEW'),
                btnContainer: '.product-item-info',
                handlerClassName: 'quick-view-button',
                additionClass: '',
                addToCartButtonSelector: '.action.tocart',
                addToCartButtonDisabledClass: 'disabled',
                addToCartButtonTextWhileAdding: $t('Adding...'),
                addToCartButtonTextAdded: $t('Added'),
                addToCartButtonTextDefault: $t('Add to Cart'),
                addToCartStatusSelector: 'script-add-cart-status',
                minicartSelector: '[data-block="minicart"]',
                templates: {
                    quickViewBlock:  '[data-template="quick-view-block"]'
                },
                quickViewContainer: '[data-container="product-quick-view"]',
                closeBtn: '[data-role=quick-view-close]'
            },

            _create: function () {
            },

            _init: function () {
                var self = this;
                mediaCheck({
                    media: '(min-width: 768px)',
                    entry: function () {
                        self._initialButtons(self.options);
                        self._viewQuickProduct(self.options);
                        self._calculateLIsInRow(self.options);
                        $(window).resize(self._calculateLIsInRow(self.options));
                        $('#quick-view').on('contentUpdated', function () {
                            mediaCheck({
                                media: '(min-width: 768px)',
                                entry: function () {
                                    self._initialButtons(self.options);
                                    self._viewQuickProduct(self.options);
                                    self._calculateLIsInRow(self.options);
                                }
                            });
                        });
                    }
                });
            },

            _initialButtons: function (config) {
                $(config.itemClass).not(".product-item-widget").each(function () {
                    if (!$(this).find('.' + config.handlerClassName).length) {
                        var groupName = $(this).parent().attr('class').replace(' ', '-');
                        var productId = $(this).find('.product-item-details').data('product-id');
                        if (typeof productId !== 'undefined' && productId !== undefined && productId !== null) {
                            var url = config.baseUrl + 'quickview/catalog_product/view/id/' + productId;
                            var btnQuickView = '<div class="quickview-btn-container">';
                            btnQuickView += '<a rel="' + groupName + '" class="' + config.handlerClassName + '" href="' + url + '" ' +
                                'data-role="quick-view-link" data-product-id="' + productId + '">';
                            btnQuickView += '<span>' + config.btnLabel + '</span></a>';
                            btnQuickView += '</div>';
                            $(this).find(config.btnContainer).prepend(btnQuickView);
                        }
                    }
                });
            },

            _viewQuickProduct: function (config){
                var template,
                    rowIndex,
                    itemIndex;
                $('[data-role=quick-view-link]').on("click", function (event) {
                    event.preventDefault();
                    $.ajax({
                        url: $(this).attr('href'),
                        data: { id: $(this).data('product-id') },
                        type: 'post',
                        dataType: 'json',
                        context: this,
                        showLoader: true,

                        /** @inheritdoc */
                        beforeSend: function () {
                            $(this).attr('disabled', 'disabled');
                        },

                        /** @inheritdoc */
                        complete: function () {
                            $(this).attr('disabled', null);
                        }
                    })
                        .done(function (response) {
                            var msg;

                            if (response.success) {
                                Math.trunc = Math.trunc || function(x) {
                                    if (isNaN(x)) {
                                        return NaN;
                                    }
                                    if (x > 0) {
                                        return Math.floor(x);
                                    }
                                    return Math.ceil(x);
                                };
                                rowIndex = Math.trunc($(this).closest(config.itemClass).index('.product-items li') / config.lisInRow) + 1;
                                itemIndex = (($(this).closest(config.itemClass).index('.product-items li') + 1) % config.lisInRow)
                                    ? (($(this).closest(config.itemClass).index('.product-items li') + 1) % config.lisInRow) : config.lisInRow;
                                if($('[data-role=product-quickview-popup]').length && config.curRow == rowIndex)
                                {
                                    $('[data-role=product-quickview-popup]').removeClass().addClass('product-quickview-popup product-' + itemIndex);
                                }
                                else
                                {
                                    $('[data-role=product-quickview-popup]').detach();
                                    $("<div class='product-quickview-popup product-" + itemIndex + "' data-role='product-quickview-popup'></div>").insertAfter($(config.itemClass).get(rowIndex * config.lisInRow - 1));
                                    config.curRow = rowIndex;
                                }
                                template = $(config.quickViewContainer)
                                    .find(config.templates.quickViewBlock)
                                    .html();
                                template = mageTemplate($.trim(template), {
                                    data: response
                                });
                                $('[data-role=product-quickview-popup]').html($(template));
                                $('[data-role=product-media]').append(response.galleryJs);
                                $('[data-role=product-media]').append(response.videoBlock);
                                $(response.productPrice).insertAfter($('[data-role=product-quickview-popup] .product-envelopes-attribute'));
                                if (response.hasSpecialPrice) {
                                    $('[data-role=product-quickview-popup]').find('[data-role=priceBox] > span').not(".old-price").addClass('special-price');
                                }
                                $(response.priceBoxJs).insertAfter($('[data-role=priceBox]'));
                                $('#product_addtocart_form').append(response.formKey);
                                $('[data-role=options-wrapper]').append(response.productOptions);
                                $('[data-role=options-wrapper]').append(response.addToCart);
                                $('[data-role=product-quickview-popup]').trigger('contentUpdated');
                                $('[data-role=product-quickview-popup]').show();
                                $(config.closeBtn).on("click", function (event) {
                                    $('[data-role=product-quickview-popup]').hide();
                                });
                                $('#quick-view').trigger('contentUpdated');

                            } else {
                                msg = response['error_message'];

                                if (msg) {
                                    alert({
                                        content: msg
                                    });
                                }
                            }
                        })
                        .fail(function (error) {
                            console.log(JSON.stringify(error));
                        });
                });

            },

            _calculateLIsInRow: function (config){
                var lisInRow = 0;
                $(config.itemClass).each(function() {
                    if($(this).prev().length > 0) {
                        if($(this).position().top != $(this).prev().position().top)
                            return false;
                        lisInRow++;
                    }
                    else {
                        lisInRow++;
                    }
                });

                var lisInLastRow = $(config.itemClass).length % lisInRow;
                if(lisInLastRow == 0) lisInLastRow = lisInRow;

                config.lisInRow = lisInRow;
                config.lisInLastRow = lisInLastRow;
            },

            _initClose: function (config) {
                $(config.closeBtn).on("click", function (event) {
                    $('[data-role=product-quickview-popup]').hide();
                });
            },
        }
    );

    return $.ba.QuickView;
});
