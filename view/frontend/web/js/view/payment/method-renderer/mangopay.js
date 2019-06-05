/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'mage/translate'
    ],
    function (Component, creditCardData, cardNumberValidator, $t) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Empisoft_Mangopay/payment/form',
                transactionResult: '',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardVerificationNumber: '',
                selectedCardType: null
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult',
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'selectedCardType'
                    ]);
                return this;
            },

            initialize: function() {
                var self = this;
                this._super();

                //Set credit card number to credit card data object
                this.creditCardNumber.subscribe(function(value) {
                    var result;
                    self.selectedCardType(null);

                    if (value == '' || value == null) {
                        return false;
                    }
                    result = cardNumberValidator(value);

                    if (!result.isPotentiallyValid && !result.isValid) {
                        return false;
                    }
                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }

                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    }
                });

                //Set expiration year to credit card data object
                this.creditCardExpYear.subscribe(function(value) {
                    creditCardData.expirationYear = value;
                });

                //Set expiration month to credit card data object
                this.creditCardExpMonth.subscribe(function(value) {
                    creditCardData.expirationYear = value;
                });

                //Set cvv code to credit card data object
                this.creditCardVerificationNumber.subscribe(function(value) {
                    creditCardData.cvvCode = value;
                });
            },

            getCode: function() {
                return 'mangopay';
            },

            isActive: function () {
                return true;
            },

            getCcAvailableTypes: function() {
                return window.checkoutConfig.payment.mangopay.availableTypes['mangopay'];
            },

            getCcMonths: function() {
                return window.checkoutConfig.payment.mangopay.months['mangopay'];
            },

            getCcYears: function() {
                return window.checkoutConfig.payment.mangopay.years['mangopay'];
            },

            hasVerification: function() {
                return window.checkoutConfig.payment.mangopay.hasVerification;
            },

            getCvvImageUrl: function () {
                return window.checkoutConfig.payment.mangopay.cvvImageUrl['mangopay'];
            },

            /**
             * Get image for CVV
             * @returns {String}
             */
            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="' + $t('Card Verification Number Visual Reference') +
                    '" title="' + $t('Card Verification Number Visual Reference') +
                    '" />';
            },

            getCcAvailableTypesValues: function() {
                return _.map(this.getCcAvailableTypes(), function(value, key) {
                    return {
                        'value': key,
                        'type': value
                    }
                });
            },
            getCcMonthsValues: function() {
                return _.map(this.getCcMonths(), function(value, key) {
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },
            getCcYearsValues: function() {
                return _.map(this.getCcYears(), function(value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },

            /**
             * @returns {Object}
             */
            getHostedFields: function () {
                var self = this,
                    fields = {
                        number: {
                            selector: self.getSelector('cc_number')
                        },
                        expirationMonth: {
                            selector: self.getSelector('expirationMonth'),
                            placeholder: $t('MM')
                        },
                        expirationYear: {
                            selector: self.getSelector('expirationYear'),
                            placeholder: $t('YY')
                        }
                    };

                if (self.hasVerification()) {
                    fields.cvv = {
                        selector: self.getSelector('cc_cid')
                    };
                }

                /**
                 * Triggers on Hosted Field changes
                 * @param {Object} event
                 * @returns {Boolean}
                 */
                fields.onFieldEvent = function (event) {
                    if (event.isEmpty === false) {
                        self.validateCardType();
                    }

                    if (event.type !== 'fieldStateChange') {
                        return false;
                    }

                    // Handle a change in validation or card type
                    if (event.target.fieldKey === 'number') {
                        self.selectedCardType(null);
                    }

                    if (event.target.fieldKey === 'number' && event.card) {
                        self.isValidCardNumber = event.isValid;
                        self.selectedCardType(
                            validator.getMageCardType(event.card.type, self.getCcAvailableTypes())
                        );
                    }
                };

                return fields;
            },

            /**
             * Returns state of place order button
             * @returns {Boolean}
             */
            isButtonActive: function () {
                return this.isActive() && this.isPlaceOrderActionAllowed();
            },

            /**
             * @returns {Boolean}
             */
            isShowLegend: function () {
                return true;
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.mangopay.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
        });
    }
);
