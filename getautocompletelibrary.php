<?php
    session_start();
    include ('connection.php');

    //Run SQL query on database to create an array of names on which the autocomplete is based.
    $autocompleteArray = array();
    $autocompletePeopleQuery =  mysqli_query($link, "SELECT display_name FROM persons");
    while($row = mysqli_fetch_array($autocompletePeopleQuery, MYSQLI_ASSOC)){
        $autocompletePerson = $row['display_name'];
        array_push($autocompleteArray, $autocompletePerson);
    }
    echo json_encode($autocompleteArray);
?>