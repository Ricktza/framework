<?php
include "config.php";
include "framework.php";
$functions = new functions();
$functions->errors(0); // 0 off, 1 on
$database = new database($DBhostname, $DBusername, $DBpassword, $DBdatabase);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Examples...</title>
    </head>
    <body>
        <?php
        echo '<br>';
        echo $functions->unique_id("ID");
        echo '<br>';
        echo $functions->html('<a href="http://google.com" target="_blank">Google.</a>');
        echo '<br>';
        echo $functions->remove_html('<a href="http://google.com" target="_blank">Google.</a>');
        echo '<br>';
        
        
        
        
        
        // Prepared statement example (experimental)
        // Fetching from the database
        $database->prepared_connect($DBhostname, $DBusername, $DBpassword, $DBdatabase); // Temp
        $username = "ricktza";
        $id = 1;
        $sql = $database->prepared_query("SELECT * FROM test WHERE username=? AND id=?", array($username, $id));
        while ($r = $sql->fetch()) {
            $id = $r['id'];
            $username = $r['username'];
        }
        echo $id;
        echo $username;
        ?>
    </body>
</html>