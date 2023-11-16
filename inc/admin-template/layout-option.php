
<?php settings_errors(); ?>
<form action="options.php" method="POST">
    <?php

    //settings_fields('display_settings_group');
    settings_fields('layout_settings_group');


    do_settings_sections('layout-settings');


    submit_button('Save changes');  ?>

</form>