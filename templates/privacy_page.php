<?php

function config_VisString( $k, $specialv, $specialret ) {
    $v = Config::get($k);
    if($v == $specialv) {
        return $specialret;
    }
    return $v;
}
?>

<div id="dialog-privacy" title="Privacy" class="fs-base-page">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="fs-base-page__header">
                    <?php
                    if (Auth::isAuthenticated()) {
                        echo "<a id='fs-back-link' class='fs-link fs-link--circle'>
                                <i class='fa fa-angle-left'></i>
                            </a>";
                    }
                    ?>
                    <h1>{tr:privacy_title}</h1>
                </div>

                <div class="fs-base-page__content">
                    {tr:privacy_text}
                </div>
            </div>
        </div>
    </div>
</div>


