<?php 

if ($_POST){
    $f = fopen('../_THEME.php', 'w') or die("can't open file");
    fwrite($f, '<?php define("BOOTSTRAP_THEME","' . htmlspecialchars($_POST['theme_val']) . '");');
    fclose($f);
}
?>
<p class="lead">Please select a theme.</p>
<select id="themeChooser">
<?php

if ($handle = opendir('../public/bower_components/bootstrap/dist/css')) {

    while (false !== ($entry = readdir($handle))) {
        $theme_parts = explode('.', $entry);
        $theme_name = ucFirst($theme_parts[0]);
        if ($entry === BOOTSTRAP_THEME){
            echo "<option value='$entry' selected=selected>$theme_name</option>";
        } else {
            echo "<option value='$entry'>$theme_name</option>";
        }
    }

    closedir($handle);
}
?>
</select>
