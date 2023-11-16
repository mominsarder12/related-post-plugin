<h1>Related Post General Settings</h1>
<?php settings_errors(); ?>
<form action="options.php" method="POST">
    <?php

    //settings_fields('display_settings_group');
    settings_fields('general_settings_group');


    do_settings_sections('related_post_settings');


    submit_button('Save changes');  ?>

</form>