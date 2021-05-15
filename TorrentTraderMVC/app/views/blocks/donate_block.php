<?php
if ($_SESSION['loggedin'] == true) {
        Block::begin(Lang::T("DONATE"));
        ?>
            <p class="text-center">This would need to contain your donation code, or something. maybe even a paypal link</p>
        
            <!-- end content -->

        <?php block::end();
    }