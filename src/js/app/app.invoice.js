/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */

(function($, $1, $app) {
    $app.ready(function($app) {
        var MODULE_NAME = 'invoice';
        if ($app.hasModule(MODULE_NAME)) return;
        var mod = {}; //implementation
        var SERVICE_URL = 'services/';
        var TAB_INVOICE_PROFILE = 0;
        var TAB_INVOICE_SETTING = 1;
        var PROFILE_PRODUCT_TYPE = 'PROFILE';
        var HISTORY_PRODUCT_TYPE = 'HISTORY';
        var mainTitle = 'Invoice Management';

        /**
         * Resolve service url
         */
        function resolveServiceUrl(serviceName) {
            try {
                if (typeof serviceName != 'string' || serviceName == '')
                    throw "Invalid service name: " + serviceName;
                return SERVICE_URL + serviceName + '.php';
            } catch (ex) {
                $1.error("[$app#resolveServiceUrl] Error.", ex);
                return null;
            }
        }

        function checkValidRecordID(id) {
            if (!id || ((typeof id != 'string') && (typeof id != 'number'))) {
                $app.tell('Invalid Bank ID');
                throw "Bank ID=" + id + ", type=" + typeof id;
            }
        }

        function addLink(functionName, text) {
            return '<a href="#" class="link" onclick="$app.module(\'invoice\').' + functionName + '">' + text + '</a>';
        }

        function title(titleData) {
            try {
                var data = [];

                if ($.trim(titleData) == '') {
                    data.push(mainTitle);
                } else {
                    data.push({
                        title: mainTitle,
                        action: 'showInvoiceManagement()'
                    });
                    if ($.isArray(titleData)) {
                        for (var i = 0; i < titleData.length; i++) {
                            if (i === titleData.length - 1) {
                                data.push(titleData[i].title);
                            } else {
                                data.push(titleData[i]);
                            }
                        }
                    } else {
                        data.push(titleData);
                    }
                }

                var titleText = data.map(function(item) {
                    if (typeof item === 'object') {
                        return addLink(item.action, item.title);
                    }
                    return item;
                }).join(' > ');

                $app.title(titleText);
            } catch (ex) {
                $1.error("[mod:invoice#title] Error.", ex);
            }
        }

        function initMasking() {
            var replaceMent = function(val) {
                return val.replace(/(?!^)-/g, '').replace(/^,/, '').replace(/^-,/, '-');
            }

            $('input[data-mask]').each(function(i, input) {
                var mask = $(input).mask(input.dataset.mask, {
                    reverse: true,
                    translation: {
                        '#': {
                            pattern: /-|\d/,
                            recursive: true
                        }
                    },
                    onChange: function(value, e) {
                        e.target.value = replaceMent(value);
                    }
                });

                mask.val(replaceMent(mask.val()))
            });
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

            $('#manualInput').on('change', function(event) {
                var isChecked = event.currentTarget.checked;
                var useReport = $('#useReport').val() == 1;
                if (useReport) {
                    $('.toggle-report').prop('disabled', isChecked === false);
                }
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

        function initInvoiceForm() {
            var dateFormat = 'YYYY-MM-DD';
            var startDateValue = $('#startDate').val();
            var dateNow = (startDateValue !== '' ? moment(startDateValue) : moment()).format(dateFormat);

            $(".datepicker")
                .datepicker({
                    dateFormat: "yy-mm-dd"
                });
            $('#startDate')
                .on('change', function(event) {
                    $('#paymentPeriod').trigger('change');
                })
                .val(dateNow);
            $('#paymentPeriod')
                .on('change', function(event) {
                    var period = event.currentTarget.value;
                    var startDate = $('#startDate').val() || dateNow;
                    var dueDate = moment(startDate, dateFormat).add(period, 'days').format(dateFormat);
                    $('#dueDate').val(dueDate);
                })
                .trigger('change')
        }

        mod.showInvoiceManagement = function(callback) {
            try {
                $app.content('invoice.view', null, function() {
                    title();
                    if (typeof callback === 'function') {
                        callback();
                    }
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
                    title('Profile Details');
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.show] Error.", ex);
            }
        };

        function showInvoiceTable(type) {
            return $('#invoice-view-tabs')
                .tabs('url', 0, resolveServiceUrl('invoice.table') + "?type="+(type||''));
        }

        mod.showInvoiceTable = function(type, reload) {
            if (reload) {
                mod.showInvoiceManagement(function(){
                    showInvoiceTable(type).tabs('select', 0);
                });
            } else {
                showInvoiceTable(type).tabs('load', 0);
            }
        }

        mod.showHistory = function(profileId) {
            try {
                $app.content('invoice.history', {
                    profileId: profileId
                }, function() {
                    title('Invoice History');
                });
            } catch (ex) {
                $1.error("[mod:invoice.history] Error.", ex);
            }
        };

        mod.showInvoice = function(invoiceId, profileId) {
            try {
                $app.content('invoice.history.show', {
                    invoiceId: invoiceId
                }, function() {
                    var titleLength = $('#titlePanel').children().length;
                    if (titleLength !== 2) {
                        title([{
                            title: 'Invoice History',
                            action: 'showHistory(' + profileId + ')'
                        }, {
                            title: 'Invoice Detail'
                        }]);
                    }
                });
            } catch (ex) {
                $1.error("[mod:invoice.history.show] Error.", ex);
            }
        };

        mod.downloadInvoice = function(invoiceId, download) {
            var url = resolveServiceUrl('invoice.print') + '?download=' + download + '&invoiceId=' + invoiceId;

            if (download === 1) {
                window.open(url);
            } else {
                window.open(url, 'Print Preview', 'width=1000, height=800, top=100, left=200, toolbar=1');
            }
        }

        /**
         * Show lock confirmation dialog
         */
        mod.lockInvoice = function(invoiceId, invoiceTable) {
            try {
                var title = "Confirm Lock Invoice";
                var msg = "Are you sure want to Lock this invoice ?<br>You can not make changes to the invoice again";

                $app.confirm(msg, title,
                    function() {
                        $.post(resolveServiceUrl('invoice.history.lock'), {
                            invoiceId: invoiceId
                        }, function(reply) {
                            try {
                                var success = $app.form.checkServiceReply(reply, false, title);
                                if (success) {
                                    if (reply && reply.summary) {
                                        $app.tell(reply.summary);
                                    }

                                    if (invoiceTable) {
                                        mod.showInvoiceTable(null, true);
                                    } else if (reply && reply.attachment && reply.attachment.invoice) {
                                        mod.showHistory(reply.attachment.invoice.profileId);
                                    }
                                }
                            } catch (ex) {
                                $1.error("[$app.logout@post] Error.", ex);
                            }
                        })
                    });
            } catch (ex) {
                $1.error("[$app.login] Error.", ex);
            }
        };


        mod.copyInvoice = function(invoiceId, invoiceTable) {
            try {
                var title = "Copied Invoice";
                var msg = "Are you sure want to copied this invoice ?<br>You can not make changes to the invoice again";

                $app.confirm(msg, title,
                    function() {
                        $.post(resolveServiceUrl('invoice.history.lock'), {
                            invoiceId: invoiceId,
                            type: 'copy',
                        }, function(reply) {
                            try {
                                var success = $app.form.checkServiceReply(reply, false, title);
                                if (success) {
                                    if (reply && reply.summary) {
                                        $app.tell(reply.summary, title);
                                    }

                                    if (invoiceTable) {
                                        $('.btn-active').click();
                                    } else if (reply && reply.attachment && reply.attachment.invoice) {
                                        mod.showHistory(reply.attachment.invoice.profileId);
                                    }
                                } else {

                                }

                            } catch (ex) {
                                $1.error("[$app.logout@post] Error.", ex);
                            }
                        })
                    });
            } catch (ex) {
                $1.error("[$app.login] Error.", ex);
            }
        };

        mod.reviseInvoice = function(invoiceId) {
            try {
                var title = "Revise Invoice";
                var msg = "Are you sure want to revise this invoice ?";

                $app.confirm(msg, title,
                    function() {
                        $.post(resolveServiceUrl('invoice.history.lock'), {
                            invoiceId: invoiceId,
                            type: 'revise',
                        }, function(reply) {
                            try {
                                var success = $app.form.checkServiceReply(reply, false, title);

                                if (success && reply) {
                                    if (reply.summary) {
                                        $app.tell(reply.summary, title);
                                    }

                                    if (reply.attachment && reply.attachment.invoice) {
                                        var invoice = reply.attachment.invoice;
                                        mod.showInvoice(invoice.invoiceId, invoice.profileId);
                                    }
                                }
                            } catch (ex) {
                                $1.error("[$app.logout@post] Error.", ex);
                            }
                        })
                    });
            } catch (ex) {
                $1.error("[$app.login] Error.", ex);
            }
        };

        mod.addInvoice = function(profileId) {
            try {
                $app.form.openAutoDialog('invoice.history.create', {
                    profileId: profileId
                }, 'Add Invoice', {
                    width: '40em',
                    height: 300
                }, function(reply) {
                    if (reply && reply.attachment && reply.attachment.invoiceId) {
                        mod.showInvoice(reply.attachment.invoiceId, reply.attachment.profileId);
                    }
                }, initInvoiceForm);
            } catch (ex) {
                $1.error("[mod:invoice.profile.create] Error.", ex);
            }
        };


        mod.editInvoice = function(invoiceId) {
            try {
                $app.form.openAutoDialog('invoice.history.edit', {
                    invoiceId: invoiceId
                }, 'Edit Invoice', {
                    width: '30em',
                    height: 250
                }, function() {
                    mod.showInvoice(invoiceId);
                }, initInvoiceForm);
            } catch (ex) {
                $1.error("[mod:invoice.profile.edit] Error.", ex);
            }
        };

        mod.addInvoiceProduct = function(invoiceId) {
            try {
                $app.form.openAutoDialog('invoice.product.create', {
                    ownerId: invoiceId,
                    ownerType: HISTORY_PRODUCT_TYPE
                }, 'Add Invoice Product', {
                    width: '30em',
                    height: 250
                }, function(reply) {
                    mod.showInvoice(invoiceId);
                }, initFormProduct);
            } catch (ex) {
                $1.error("[mod:invoice.product.create] Error.", ex);
            }
        };

        mod.editInvoiceProduct = function(productId) {
            try {
                $app.form.openAutoDialog('invoice.product.edit', {
                    productId: productId
                }, 'Edit Invoice Product', {
                    width: '30em',
                    height: 250
                }, function(reply) {
                    if (reply && reply.attachment && reply.attachment.ownerId) {
                        mod.showInvoice(reply.attachment.ownerId);
                    }
                }, initFormProduct);
            } catch (ex) {
                $1.error("[mod:invoice.profile.edit] Error.", ex);
            }
        };

        mod.deleteInvoiceProduct = function(productId, invoiceId) {
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
                                mod.showInvoice(invoiceId);
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

        mod.deleteInvoice = function(profileId, invoiceId) {
            try {
                checkValidRecordID(invoiceId);
                var title = 'Remove Invoice';
                $app.confirm('Remove this Invoice ?', title, function() {
                    $app.call('invoice.history.delete', {
                        invoiceId: invoiceId
                    }, function(reply) {
                        try {
                            var success = $app.form.checkServiceReply(reply, false, title);
                            if (success) {
                                if (reply && reply.summary) {
                                    $app.tell(reply.summary || 'Success!', title);
                                }

                                if (profileId) {
                                    mod.showHistory(profileId);
                                } else {
                                    mod.showInvoiceTable(null, true);
                                }
                            }
                        } catch (ex) {
                            $1.error("[mod:invoice.history.delete@ajaxsuccess] Error.", ex);
                        }
                    });
                });
            } catch (ex) {
                $1.error("[mod:invoice.history.delete] Error.", ex);
            }
        };

        mod.createProfile = function() {
            try {
                $app.form.openAutoDialog('invoice.profile.create', null, 'Add Invoice Profile', {
                    width: '35em',
                    height: 250
                }, function(reply) {
                    if (reply && reply.attachment && reply.attachment.profileId) {
                        mod.showProfile(reply.attachment.profileId);
                    }
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.create] Error.", ex);
            }
        };

        mod.showDownloadAll = function() {
            try {
                $app.form.openAutoDialog('invoice.downloadAll.form', null, 'Download All Invoice', {
                    width: '30em',
                    height: 150,
                    btnText: 'Download',
                    btnAction: function() {
                        var $form = $('form:first', this);
                        $form.attr('target', '_blank').submit();
                        $app.form.closeDialog();
                    },
                }, function(reply) {
                    if (reply && reply.attachment && reply.attachment.profileId) {
                        mod.showProfile(reply.attachment.profileId);
                    }
                });
            } catch (ex) {
                $1.error("[mod:invoice.profile.create] Error.", ex);
            }
        };


        mod.editProfile = function(profileId, changePage) {
            try {
                $app.form.openAutoDialog('invoice.profile.edit', {
                    profileId: profileId
                }, 'Edit Invoice Profile', {
                    width: '35em',
                    height: 250
                }, function() {
                    if (changePage !== false) {
                        mod.showProfile(profileId);
                    }
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
                    width: '35em',
                    height: 400
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
                    width: '25em',
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
                    width: '25em',
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
