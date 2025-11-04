// JavaScript Document

/*
 * FileSender www.filesender.org
 * 
 * Copyright (c) 2009-2012, AARNet, Belnet, HEAnet, SURF, UNINETT
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * *	Redistributions of source code must retain the above copyright
 * 	notice, this list of conditions and the following disclaimer.
 * *	Redistributions in binary form must reproduce the above copyright
 * 	notice, this list of conditions and the following disclaimer in the
 * 	documentation and/or other materials provided with the distribution.
 * *	Neither the name of AARNet, Belnet, HEAnet, SURF and UNINETT nor the
 * 	names of its contributors may be used to endorse or promote products
 * 	derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

$(function() {
    var page = $('.download_page');
    if(!page.length) return;

    // Get recipient token
    var m = window.location.search.match(/token=([0-9a-f-]+)/);
    var token = m[1];
    var $this = this;

    page.find('.toggle-select-all').trigger('mousedown');

    filesender.client.setPage( $(this) );
    filesender.client.setToken( token );
    filesender.client.bindDownloadButton('.file .download');
    filesender.client.bindDownloadArchive();
    filesender.client.bindFileCheckButtons();

    var button_zipdl = page.find('.archive_download_frame');
    var button_tardl = page.find('.archive_tar_download_frame');
    if( macos || linuxos ) {
        button_tardl.addClass('fs-button');
    } else {
        button_zipdl.addClass('fs-button');
    }

    if( window.filesender.config.download_verification_code_enabled ) {
        var transferid = $('.transfer').attr('data-id');
        var rid = $('.rid').attr('data-id');

        page.find('.verificationcodesendtoemail').button().on('click', function () {
            filesender.client.sendVerificationCodeToYourEmailAddress(
                transferid,
                function () {
                    window.filesender.ui.notify("info", lang.tr("email_sent"));
                });
            return true;
        });
        page.find('.verificationcodesend').button().on('click', function () {
            var pass = $('#verificationcode').val();
            if (!pass.length) {
                // nothing, could have just returned true here.
            } else {
                try {
                    var options = {
                        error: function (e) {
                            if (e.message == 'rest_data_stale') {
                                window.filesender.ui.alert("error", lang.tr("verification_code_is_too_old"));
                                return;
                            }
                            filesender.ui.error(e);
                        }
                    };


                    filesender.client.checkVerificationCodeWithServer(
                        transferid, pass,
                        function (args) {
                            if (args.ok === true) {
                                verificationCodePassed = true;
                                $(".verify_email_to_download").dialog("close");

                                var encrypted = verificationCodeObjectThatTiggeredEvent.closest('.file').attr('data-encrypted');
                                var msg = "downloading";
                                if (!encrypted) {
                                    window.filesender.ui.notify("info", lang.tr(msg));
                                }
                                verificationCodeObjectThatTiggeredEvent.click();
                            } else {
                                window.filesender.ui.alert("error", lang.tr("verification_code_did_not_match"));
                            }
                        }
                        , options
                    );
                } catch (exception) {
                }
            }
            return true;
        });
    }

    $('#check-all').click();
});
