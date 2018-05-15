/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */

(function($, $1, $app) {
    $app.ready(function($app) {
        var MODULE_NAME = 'invoice';
        if ($app.hasModule(MODULE_NAME)) return;
        var mod = {}; //implementation
        var TAB_INVOICE_PROFILE = 0;
        var TAB_INVOICE_SETTING = 1;
        var PROFILE_PRODUCT_TYPE = 'PROFILE';
        var HISTORY_PRODUCT_TYPE = 'HISTORY';

        function checkValidRecordID(id) {
            if (!id || ((typeof id != 'string') && (typeof id != 'number'))) {
                $app.tell('Invalid Bank ID');
                throw "Bank ID=" + id + ", type=" + typeof id;
            }
        }

        function addLink(onClick, text) {
            return '<a href="#" class="link" onclick="' + onClick + '">' + text + '</a>';
        }

        function title(text) {
            try {
                var title = 'Invoice Management';
                if ($.trim(text) != '') {
                    title = addLink("$app.module(\'invoice\').showInvoice()", title);
                    title += ' > ' + text;
                }
                $app.title(title);
            } catch (ex) {
                $1.error("[mod:invoice#title] Error.", ex);
            }
        }

        function initFormProduct() {
            var toggleReport = function(useReport) {
                $('.toggle-report').prop('disabled', useReport);
                $('.report-name ').toggleClass('hidden', !useReport);
            }

            $("#form-product").on('submit', function(event) {
                removeMasking();
            });

            $('#useReport').on('change', function() {
                var value = $(this).val();
                toggleReport(parseInt(value, 10) === 1);
            }).trigger('change');

            $('#productName').autocomplete({
                source: [
                    'SMS API Gateway',
                    'SMS Mobile Broadcast',
                    'SMS API Dispatcher',
                    'SMS API Interface',
                    'Email to SMS & Auto SMS to Email Forward',
                    'Minimum charge SMS API Dispatcher',
                    'Minimum charge SMS Mobile Broadcast',
                    'Minimum charge SMS API Gateway',
                    'Minimum charge SMS On Demand',
                    'Minimum charge Email to SMS',
                    'Maintenance modem',
                    'Maintenance modem Internal',
                ]
            });

            initMasking();
        }

        function initMasking() {
            $('input[data-mask]').each(function(i, input) {
                $(input).mask(input.dataset.mask, {reverse: true} )
            });
        }

        mod.showInvoice = function() {
            try {
                $app.content('invoice.view', null, function() {
                    title();
                });
            } catch (ex) {
                $1.error("[mod:invoice.view] Error.", ex);
            }
        };

        mod.showProfile = function(profileId) {
            try {
                $app.content('invoice.profile.show', {
                    profileId: profileId
                }, function() {
                    title('Detail Profile');
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.show] Error.", ex);
            }
        };

        mod.createProfile = function() {
            try {
                $app.form.openAutoDialog('invoice.profile.create', null, 'Add Invoice Profile', {
                    width: '30em',
                    height: 200
                }, function(reply) {
                    if (reply && reply.attachment && reply.attachment.profileId) {
                        mod.showProfile(reply.attachment.profileId);
                    }
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.create] Error.", ex);
            }
        };


        mod.editProfile = function(profileId) {
            try {
                $app.form.openAutoDialog('invoice.profile.edit', {
                    profileId: profileId
                }, 'Edit Invoice Profile', {
                    width: '30em',
                    height: 200
                }, function() {
                    mod.showProfile(profileId);
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.edit] Error.", ex);
            }
        };

        mod.createProfileProduct = function(profileId) {
            try {
                $app.form.openAutoDialog('invoice.product.create', {
                    ownerId: profileId,
                    ownerType: PROFILE_PRODUCT_TYPE
                }, 'Add Invoice Product', {
                    width: '30em',
                    height: 230
                }, function(reply) {
                    mod.showProfile(profileId);
                }, function() {
                    initFormProduct();
                });
            } catch (ex) {
                $1.error("[mod:invoice.product.create] Error.", ex);
            }
        };

        mod.editProfileProduct = function(profileId, productId) {
            try {
                $app.form.openAutoDialog('invoice.product.edit', {
                    productId: productId
                }, 'Edit Invoice Product', {
                    width: '30em',
                    height: 230
                }, function(reply) {
                    mod.showProfile(profileId);
                }, function() {
                    initFormProduct();
                });
            } catch (ex) {
                $1.error("[mod:invoice.product.edit] Error.", ex);
            }
        };

        mod.deleteProfileProduct = function(profileId, productId) {
            try {
                checkValidRecordID(productId);
                var title = 'Remove Product';
                $app.confirm('Remove this Product ?', title, function() {
                    $app.call('invoice.product.delete', {
                        productId: productId
                    }, function(reply) {
                        try {
                            var success = $app.form.checkServiceReply(reply, false, title);
                            if (success) {
                                if (reply && reply.summary) {
                                    $app.tell(reply.summary || 'Success!', title);
                                }
                                mod.showProfile(profileId);
                            }
                        } catch (ex) {
                            $1.error("[mod:invoice.profile.delete@ajaxsuccess] Error.", ex);
                        }
                    });
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.delete] Error.", ex);
            }
        };


        mod.editSetting = function() {
            try {
                $app.form.openAutoDialog('invoice.setting.edit', null, 'Edit Invoice Setting', {
                    width: '40em',
                    height: 320
                }, function() {
                    $('#invoice-view-tabs').tabs('load', TAB_INVOICE_SETTING);
                }, function() {
                    autosize(document.querySelector('textarea'));
                });
            } catch (ex) {
                $1.error("[mod:invoice.setting.edit] Error.", ex);
            }
        };

        mod.createBank = function() {
            try {
                $app.form.openAutoDialog('invoice.bank.create', null, 'Create Bank Account', {
                    width: '45em',
                    height: 240
                }, function(reply) {
                    $('#invoice-view-tabs').tabs('load', TAB_INVOICE_SETTING);
                }, function() {
                    autosize(document.querySelector('textarea'));
                });
            } catch (ex) {
                $1.error("[mod:invoice.bank.create] Error.", ex);
            }
        };

        mod.editBank = function(bankId) {
            try {
                $app.form.openAutoDialog('invoice.bank.edit', {
                    bankId: bankId
                }, 'Edit Bank Account', {
                    width: '40em',
                    height: 240
                }, function() {
                    $('#invoice-view-tabs').tabs('load', TAB_INVOICE_SETTING);
                }, function() {
                    autosize(document.querySelector('textarea'));
                });
            } catch (ex) {
                $1.error("[mod:invoice.bank.edit] Error.", ex);
            }
        };


        mod.deleteBank = function(bankId) {
            try {
                checkValidRecordID(bankId);
                var title = 'Remove Bank Account';
                $app.confirm('Remove this Bank account ?', title, function() {
                    $app.call('invoice.bank.delete', {
                        bankId: bankId
                    }, function(reply) {
                        try {
                            var success = $app.form.checkServiceReply(reply, false, title);
                            if (success) {
                                if (reply && reply.summary) {
                                    $app.tell(reply.summary || 'Success!', title);
                                }

                                $('#invoice-view-tabs').tabs('load', TAB_INVOICE_SETTING);
                            }
                        } catch (ex) {
                            $1.error("[mod:invoice.bank.delete@ajaxsuccess] Error.", ex);
                        }
                    });
                });
            } catch (ex) {
                $1.error("[mod:invoice.bank.delete] Error.", ex);
            }
        };

        try {
            $app.registerModule(mod, MODULE_NAME);
        } catch (ex) {
            $1.log('[mod:invoice] Failed registering module ' + MODULE_NAME);
        }
    });
})($, $1, $app);
