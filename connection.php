<?php
    $link = mysqli_connect("localhost", "pmmweb5_vsadmin", "GaryOwen1963", "pmmweb5_vintagesnooker");
    if(mysqli_connect_error()){
        die ('ERROR: unable to connect to database: ' . mysqli_connect_error());
    }
?>