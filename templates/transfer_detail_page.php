<?php
if(!isset($mode)) $mode = 'user';
$show_guest = isset($show_guest) ? (bool)$show_guest : false;
$extend = (bool)Config::get('allow_transfer_expiry_date_extension');
$haveNext = 0;
$havePrev = 0;

$transfer_not_found = <<<HEREDOC
      <div class="fs-transfer-detail transfer_details">
        <div class="container">
            {tr:transfer_not_found}
        </div>
      </div>
HEREDOC;

$transfer_id  = Utilities::arrayKeyOrDefault($_GET, 'transfer_id',  0, FILTER_VALIDATE_INT  );
$isEncrypted = false;
$downloadsCount = 0;
$audit = (bool)Config::get('auditlog_lifetime') ? '1' : '';
$transfer = null;

if ($transfer_id) {
    try {
        $transfer = Transfer::fromId($transfer_id);
        $downloadsCount = count($transfer->downloads);
    } catch( Exception  $e ) {
        echo $transfer_not_found;
        return;
    }
}

$extend = (bool)Config::get('allow_transfer_expiry_date_extension');

$found = true;
$user = Auth::user();
if( !Auth::isAuthenticated() || !$transfer ) {
    $found = false;
}
if( $found ) {
    if( !Auth::isAdmin()) {
        // non admin user can only view their own stuff
        if( $transfer->userid != $user->id ) {
            $found = false;
        }
        
    }
}

if( !$found ) {
    echo $transfer_not_found;
    return;
}
?>

<div class="fs-transfer-detail transfer_details"
     id="transfer_<?php echo $transfer->id ?>"
     data-id="<?php echo $transfer->id ?>"
     data-status="<?php echo $transfer->status ?>"
     data-recipients-enabled="<?php echo $transfer->getOption(TransferOptions::GET_A_LINK) ? '' : '1' ?>"
     data-errors="<?php echo count($transfer->recipients_with_error) ? '1' : '' ?>"
     data-expiry-extension="<?php echo $transfer->expiry_date_extension ?>"
     data-key-version="<?php echo $transfer->key_version; ?>"
     data-key-salt="<?php echo $transfer->salt; ?>"
     data-password-version="<?php echo $transfer->password_version; ?>"
     data-password-encoding="<?php echo $transfer->password_encoding_string; ?>"
     data-password-hash-iterations="<?php echo $transfer->password_hash_iterations; ?>"
     data-client-entropy="<?php echo $transfer->client_entropy; ?>">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="fs-transfer-detail__header">
                    <a id='fs-back-link' class='fs-link fs-link--circle'>
                        <i class='fa fa-angle-left'></i>
                    </a>
                    <h1>{tr:transfer_details}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col col-sm-12 col-md-6 col-lg-6">
                <div class="fs-transfer-detail__details">
                    <h2>{tr:transfer_details}</h2>
                    <div class="fs-info fs-info--aligned">
                        <strong>{tr:transfer_sent_on}:</strong>
                        <span><?php echo Utilities::sanitizeOutput(Utilities::formatDate($transfer->created)) ?></span>
                    </div>
                    <div class="fs-info fs-info--aligned">
                        <strong>{tr:expiration_date}:</strong>
                        <span><?php echo Utilities::sanitizeOutput(Utilities::formatDate($transfer->expires)) ?></span>
                    </div>
                    <div class="fs-info fs-info--aligned">
                        <strong>{tr:from}:</strong>
                        <span><?php echo Template::replaceTainted($transfer->user_email) ?></span>
                    </div>
                    <?php if($transfer->subject) { ?>
                        <div class="fs-info fs-info--aligned">
                            <strong>{tr:subject}:</strong>
                            <span><?php echo Template::replaceTainted($transfer->subject) ?></span>
                        </div>
                    <?php } ?>
                    <?php if($transfer->message) { ?>
                        <div class="fs-info fs-info--aligned">
                            <strong>{tr:message}:</strong>
                            <span><?php echo Template::replaceTainted($transfer->message) ?></span>
                        </div>
                    <?php } ?>
                    <div class="fs-info fs-info--aligned">
                        <strong>{tr:encryption}:</strong>
                        <span>
                            <?php if ($isEncrypted) {
                                echo Lang::tr('yes');
                            } else {
                                echo Lang::tr('no');
                            } ?>
                        </span>
                    </div>
                    <div class="fs-info fs-info--aligned">
                        <strong>{tr:downloads}:</strong>
                        <span>
                            <?php echo $downloadsCount ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col col-sm-12 col-md-6 col-lg-6">
                <div class="fs-transfer-detail__files">
                    <h2>{tr:transferred_files}</h2>
                    <div class="fs-transfer__list">
                        <div class="fs-transfer__files"">
                            <table class="fs-table files">
                                <tbody>
                                <?php foreach($transfer->files as $file) { ?>
                                    <tr class="file"
                                        data-id="<?php echo $file->id ?>"
                                        data-key-version="<?php echo $transfer->key_version; ?>"
                                        data-key-salt="<?php echo $transfer->salt; ?>"
                                        data-password-version="<?php echo $transfer->password_version; ?>"
                                        data-password-encoding="<?php echo $transfer->password_encoding_string; ?>"
                                        data-password-hash-iterations="<?php echo $transfer->password_hash_iterations; ?>"
                                        data-client-entropy="<?php echo $transfer->client_entropy; ?>"
                                        data-fileiv="<?php echo $file->iv; ?>"
                                        data-fileaead="<?php echo $file->aead; ?>"
                                        data-transferid="<?php echo $transfer->id; ?>"
                                    >
                                        <td>
                                            <div>
                                                <span class="name"><?php echo Utilities::sanitizeOutput($file->path) ?></span>
                                                <span class="size"><?php echo Utilities::formatBytes($file->size) ?></span>

                                                <?php if(!$transfer->is_expired) { ?>

                                                    <?php if(isset($transfer->options['encryption']) && $transfer->options['encryption'] === true) { ?>
                                                        <span class="fs-button fs-button--small fs-button--transparent fs-button--info fs-button--no-text download" title="{tr:download}"
                                                              data-id="<?php echo $file->id ?>"
                                                              data-encrypted="<?php echo isset($transfer->options['encryption'])?$transfer->options['encryption']:'false'; ?>"
                                                              data-mime="<?php echo Template::sanitizeOutput($file->mime_type); ?>"
                                                              data-name="<?php echo Template::sanitizeOutput($file->path); ?>"
                                                              data-size="<?php echo $file->size; ?>"
                                                              data-encrypted-size="<?php echo $file->encrypted_size; ?>"
                                                              data-key-version="<?php echo $transfer->key_version; ?>"
                                                              data-key-salt="<?php echo $transfer->salt; ?>"
                                                              data-password-version="<?php echo $transfer->password_version; ?>"
                                                              data-password-encoding="<?php echo $transfer->password_encoding_string; ?>"
                                                              data-password-hash-iterations="<?php echo $transfer->password_hash_iterations; ?>"
                                                              data-client-entropy="<?php echo $transfer->client_entropy; ?>"
                                                              data-fileiv="<?php echo $file->iv; ?>"
                                                              data-fileaead="<?php echo $file->aead; ?>"
                                                              data-transferid="<?php echo $transfer->id; ?>"
                                                        >
                                                            <i class="fa fa-download"></i>
                                                        </span>

                                                    <?php } else {?>
                                                        <a class="fs-button fs-button--small fs-button--transparent fs-button--info fs-button--no-text download" title="{tr:download}" href="download.php?files_ids=<?php echo $file->id ?>">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    <?php } ?>
                                                <?php } ?>

                                                <span data-action="delete" class="fs-button fs-button--small fs-button--transparent fs-button--danger fs-button--no-text download" title="{tr:delete}">
                                                    <i class="fa fa-trash-o"></i>
                                                </span>

                                                <?php if($audit) { ?>
                                                    <span data-action="auditlog" class="fs-button fs-button--small fs-button--transparent fs-button--info fs-button--no-text download" title="{tr:open_file_auditlog}">
                                                        <i class="fa fa-history"></i>
                                                    </span>
                                                <?php } ?>

                                                
                                            </div>
                                        </td>
                                    </tr>

                                <?php } ?>

                                </tbody>
                            </table>

                            <div class="transfer" data-id="<?php echo $transfer->id ?>"></div>
                        </div>
                    </div>
                    <div class="fieldcontainer" id="encryption_description_not_supported">
                        {tr:file_encryption_disabled}
                    </div>
                    <div class="fs-transfer-detail__total-size">
                        <strong>{tr:total_transfer_size}:</strong>
                        <span>
                            <?php echo Utilities::formatBytes($transfer->size) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!$transfer->getOption(TransferOptions::GET_A_LINK)) { ?>
            <div class="row">
                <div class="col">
                    <div class="fs-transfer-detail__recipients">
                        <h2>Recipients</h2>

                        <div class="fs-transfer__upload-recipients fs-transfer__upload-recipients--show">
                            <span>
                                Your transfer has been sent to the following email addresses
                            </span>
                            <div class="fs-badge-buttons-listv recipients">
                                <br/>
                                
                                <?php foreach($transfer->recipients as $recipient) { ?>
                                    <div class="fs-badge-buttons recipient" data-id="<?php echo $recipient->id ?>" data-email="<?php echo Template::sanitizeOutputEmail($recipient->email) ?>" data-errors="<?php echo count($recipient->errors) ? '1' : '' ?>">
                                        <?php
                                        if(in_array($recipient->email, Auth::user()->email_addresses)) {
                                            echo '<abbr title="'.Template::sanitizeOutputEmail($recipient->email).'">'.Lang::tr('me').'</abbr>';
                                        } else {
                                            echo '<span>'.Template::sanitizeOutput($recipient->identity).'</span>';
                                        }
                                        ?>

                                        <span class="fs-badge-buttons-shell" >
                                            <span data-action="remind" class="fa    fa-lg fa-repeat" title="{tr:send_reminder}"></span>
                                            <span data-action="delete" class="fa    fa-lg fa-trash-o" title="{tr:delete}"></span>
                                            <span data-action="auditlog" class="fa  fa-lg fa-history" title="{tr:open_recipient_auditlog}"></span>
                                        </span>
                                        
                                    </div>
                                    <br/>
                                <?php } ?>

                                <button type="button" class="fs-button" data-action="add_recipient" title="{tr:add_recipient}">
                                    <i class="fa fa-lg fa-envelope-o"></i>
                                    <span>Add recipient</span>
                                </button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if($transfer->getOption(TransferOptions::GET_A_LINK)) { ?>
            <div class="row">
                <div class="col col-sm-12 col-md-8">
                    <div class="fs-transfer-detail__link">
                        <h2>{tr:download_link}</h2>
                        <div class="fs-copy">
                            <span class="download_link"><?php echo $transfer->first_recipient->download_link ?></span>

                            <button id="copy-to-clipboard" type="button" class="fs-button">
                                <i class="fa fa-copy"></i>
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($transfer->options) { ?>
            <div class="row">
                <div class="col">
                    <div class="fs-transfer-detail__options">
                        <h2>{tr:transfer_selected_options}</h2>
                        <div class="row">
                            <div class="col col-sm-12">
                                <h3>{tr:advanced_transfer_options}</h3>

                                <?php
                                    $optionshtml = "";
                                    if(count(array_filter($transfer->options))) {
                                        foreach (array_keys(array_filter($transfer->options)) as $o) {
                                            if ($o == TransferOptions::STORAGE_CLOUD_S3_BUCKET) {
                                                // this option will never be shown to the user
                                            } else {
                                                $checkboxClass = "fs-checkbox--disabled";

                                                $optionshtml .= "<div class='fs-transfer-detail__check'>";
                                                $optionshtml .= "<div class='fs-checkbox ".$checkboxClass."'>";
                                                $optionshtml .= "<label for='".$o."'>".Lang::tr($o)."</label>";

                                                if( $o == TransferOptions::EMAIL_DAILY_STATISTICS ) {
                                                    $optionshtml .= "<input id='".$o."' data-option='".TransferOptions::EMAIL_DAILY_STATISTICS."' type='checkbox' checked disabled>";
                                                } else {
                                                    $optionshtml .= "<input id='".$o."' type='checkbox' checked disabled>";
                                                }

                                                $optionshtml .= "<span class='fs-checkbox__mark'></span>";
                                                $optionshtml .= "</label>";
                                                $optionshtml .= "</div>";
                                                $optionshtml .= "</div>";
                                            }
                                        }
                                    }

                                    if($optionshtml != '') {
                                        echo $optionshtml;
                                    } else {
                                        echo Lang::tr('none') ;
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col">
                <div class="fs-transfer-detail__actions" >
                    <?php if(!$transfer->getOption(TransferOptions::GET_A_LINK)) { ?>
                        <button type="button" data-action="remind" class="fs-button">
                            <i class="fa fa-repeat"></i>
                            <span>{tr:send_reminder}</span>
                        </button>
                    <?php } ?>

                    <?php if($audit) { ?>
                        <button type="button" data-action="auditlog" class="fs-button">
                            <i class="fa fa-history"></i>
                            <span>{tr:see_transfer_logs}</span>
                        </button>
                    <?php } ?>

                    <button type="button" data-action="delete" class="fs-button fs-button--danger">
                        <i class="fa fa-trash"></i>
                        <span>{tr:delete_transfer}</span>
                    </button>

                    <?php if($extend) { ?>
                        <button type="button" data-action="extend" class="fs-button" data-id="<?php echo $transfer->id ?>" data-expiry-extension="<?php echo $transfer->expiry_date_extension ?>" >
                            <i class="fa fa-calendar-plus-o"></i>
                            <span>{tr:extend_expires}</span>
                        </button>                        
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{path:js/transfer_detail_page.js}"></script>
