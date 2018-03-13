<?php
    session_start();
    include ('connection.php');

//Define error messages
$emptyInputMessage = '<strong>Nothing detected coming through from AJAX call.</strong>.';

//Get input
$requiredArticle = $_POST['name'];
$requiredArticle = addslashes($requiredArticle);

// UNCOMMENT ONE OF THE NEXT FOUR LINES FOR TESTING WHETHER MEDIA IS ADDED TO CERTAIN TYPES OF SEARCH OBJECT
// $requiredArticle = 'Steve Davis';
// $requiredArticle = '2012 PartyPoker German Masters';
// $requiredArticle = 'The Crucible - and its "curse"';
// echo $requiredArticle;

//Build query to search for inputted term in the database
$checkforRAinCountries = "SELECT * FROM countries WHERE country='$requiredArticle'";
$checkforRAinObjects = "SELECT * FROM objects WHERE object_name='$requiredArticle'";
$checkforRAinPersons = "SELECT * FROM persons WHERE display_name='$requiredArticle'";
$checkforRAinPictureGallery = "SELECT * FROM picture_gallery WHERE picture_caption='$requiredArticle'";
$checkforRAinTournamentCategories = "SELECT * FROM tournament_category WHERE tournament_category_name='$requiredArticle'";
$checkforRAinTournamentSeries = "SELECT * FROM tournament_series WHERE tournament_series_name='$requiredArticle'";
$checkforRAinTournamentEdition = "SELECT * FROM tournament_edition WHERE tournament_edition_name='$requiredArticle'";
$checkforRAinVideoVault = "SELECT * FROM video_vault WHERE video_caption='$requiredArticle'";

// echo $checkforRAinPersons;

//Results of search: NB These are all PHPObjects but organised as arrays, all with the same field structure. The important field for our purposes is [num_rows]
$RAinCountriesResult = mysqli_query($link, $checkforRAinCountries);
$RAinObjectsResult = mysqli_query($link, $checkforRAinObjects);
$RAinPersonsResult = mysqli_query($link, $checkforRAinPersons);
$RAinPictureGalleryResult = mysqli_query($link, $checkforRAinPictureGallery);
$RAinTournamentCategoriesResult = mysqli_query($link, $checkforRAinTournamentCategories);
$RAinTournamentSeriesResult = mysqli_query($link, $checkforRAinTournamentSeries);
$RAinTournamentEditionResult = mysqli_query($link, $checkforRAinTournamentEdition);
$RAinVideoVaultResult = mysqli_query($link, $checkforRAinVideoVault);

// Create array of table search results: NB These are PHP Objects within arrays
$RASearchinTablesArray = array($RAinCountriesResult , $RAinObjectsResult , $RAinPersonsResult , $RAinPictureGalleryResult , $RAinTournamentCategoriesResult , $RAinTournamentSeriesResult , $RAinTournamentEditionResult , $RAinVideoVaultResult);
// print_r($RASearchinTablesArray);

// Check which table contains the required article by looping through the master array of table search results - NB: only one table in the database will have returned one row on the search
foreach($RASearchinTablesArray as $correctTable){
    global $requiredArticleRow;    
    //Find the table which contains the search result and fetch the row as an associative array
    if(mysqli_num_rows($correctTable)>0){
        //Fetch the associative array - if the search item is anything besides a person, tournament or (snooker)object, this can be echoed directly back to the code.js file without modification, as a successful response to the AJAX call.
        $requiredArticleRow = mysqli_fetch_array($correctTable, MYSQLI_ASSOC);
        //If the search item is a person, country or snooker(object), the following statements determine which type it is and sets up queries to fetch media relating to that item from the database. This includes pictures and videos and, for persons, the name and flag of their country and also a list of any tournaments won.
        if(mysqli_num_rows($RAinObjectsResult)>0){
            $objectID = $requiredArticleRow['object_id'];
            addObjectMedia();
            $requiredArticleRow = array_merge($requiredArticleRow, $objectPicturesArray, $objectVideosArray);
        };
        if(mysqli_num_rows($RAinPersonsResult)>0){
            $personID = $requiredArticleRow['person_id'];
            $personCountry= $requiredArticleRow['country_id'];
            addPersonMedia();
            addPersonTournaments();
            $requiredArticleRow = array_merge($requiredArticleRow, $personPicturesArray, $personVideosArray, $personCountryArray, $personMajorTournamentWinsArray);
        };
        if(mysqli_num_rows($RAinTournamentEditionResult)>0){
            $tournamentID = $requiredArticleRow['tournament_edition_id'];
            addTournamentMedia();
            $requiredArticleRow = array_merge($requiredArticleRow, $tournamentPicturesArray, $tournamentVideosArray);
        };
    };
};

//Encode final search item array to send back to code.js through AJAX call.
$requiredArticleRowJSONEncoded = json_encode($requiredArticleRow);
echo $requiredArticleRowJSONEncoded;

// FUNCTIONS TO ADD MEDIA TO PERSON, TOURNAMENT AND OBJECT SEARCH ITEMS

function addObjectMedia(){
    global $link;
    global $objectID;
    global $objectPicturesArray;
    global $objectVideosArray;
    // global $requiredArticleRow;    
    //Build query for finding pictures attached to object
    $checkforObjectInPictures = "SELECT * FROM picture_gallery WHERE object_id='$objectID'";
    //Build query for finding videos attached to object
    $checkforObjectInVideos = "SELECT * FROM video_vault WHERE object_id='$objectID'";    
    //Run these queries on the database
    $objectInPicturesResult = mysqli_query($link, $checkforObjectInPictures);
    $objectInVideosResult = mysqli_query($link, $checkforObjectInVideos);

    //Fetch array on picture results
    $objectPicturesArray = array();
    if(mysqli_num_rows($objectInPicturesResult)>0){
        for($m=0; $m<mysqli_num_rows($objectInPicturesResult); $m++){
            $objectInPicturesRow = mysqli_fetch_array($objectInPicturesResult, MYSQLI_ASSOC);
            //Trim row down to picture_id, picture_url, picture_caption and picture_credit, and create key/value pairs
            $objectinPicturesURLKey = "objectPictureURL" . ($m+1);
            $objectInPicturesRowURL = $objectInPicturesRow['picture_url'];
            $objectinPicturesCaptionKey = "objectPictureCaption" . ($m+1);
            $objectInPicturesRowCaption = $objectInPicturesRow['picture_caption'];
            $objectinPicturesCreditKey = "objectPictureCredit" . ($m+1);
            $objectInPicturesRowCredit = $objectInPicturesRow['picture_credit'];
            //Add key/value pairs to object's picture array
            $objectPicturesArray[$objectinPicturesURLKey] = $objectInPicturesRowURL;
            $objectPicturesArray[$objectinPicturesCaptionKey] = $objectInPicturesRowCaption;
            $objectPicturesArray[$objectinPicturesCreditKey] = $objectInPicturesRowCredit;
        };
    };
    
    // print_r($objectPicturesArray);
    
    //Fetch array on video results
    $objectVideosArray = array();
    if(mysqli_num_rows($objectInVideosResult)>0){
        for($n=0; $n<mysqli_num_rows($objectInVideosResult); $n++){
            $objectInVideosRow = mysqli_fetch_array($objectInVideosResult, MYSQLI_ASSOC);
            //Trim row down to video_id, video_url, video_caption and video_credit, and create key/value pairs
            $objectinVideosURLKey = "objectVideoURL" . ($n+1);
            $objectInVideosRowURL = $objectInVideosRow['video_url'];
            $objectinVideosCaptionKey = "objectVideoCaption" . ($n+1);
            $objectInVideosRowCaption = $objectInVideosRow['video_caption'];
            $objectinVideosCreditKey = "objectVideoCredit" . ($n+1);
            $objectInVideosRowCredit = $objectInVideosRow['video_credit'];
            //Add key/value pairs to object's video array
            $objectVideosArray[$objectinVideosURLKey] = $objectInVideosRowURL;
            $objectVideosArray[$objectinVideosCaptionKey] = $objectInVideosRowCaption;
            $objectVideosArray[$objectinVideosCreditKey] = $objectInVideosRowCredit;
        };
    };
}

function addPersonMedia(){
    global $link;
    global $personID;
    global $personCountry;
    global $personPicturesArray;
    global $personVideosArray;
    global $personCountryArray;
    //Build query for finding pictures attached to person
    $checkforPersonInPictures = "SELECT * FROM picture_gallery WHERE person_id='$personID'";
    //Build query for finding videos attached to person
    $checkforPersonInVideos = "SELECT * FROM video_vault WHERE person_id='$personID'";
    //Build query for finding flag of person's country
    $checkforPersonCountry = "SELECT country, country_flag FROM countries WHERE country_id='$personCountry'";    
    //Run these queries on the database
    $personInPicturesResult = mysqli_query($link, $checkforPersonInPictures);
    $personInVideosResult = mysqli_query($link, $checkforPersonInVideos);
    $personCountryResult = mysqli_query($link, $checkforPersonCountry);
    //Fetch array on picture results
    $personPicturesArray = array();
    if(mysqli_num_rows($personInPicturesResult)>0){
        for($j=0; $j<mysqli_num_rows($personInPicturesResult); $j++){
            $personInPicturesRow = mysqli_fetch_array($personInPicturesResult, MYSQLI_ASSOC);
            //Trim row down to picture_id, picture_url, picture_caption and picture_credit, and create key/value pairs
            $personinPicturesURLKey = "personPictureURL" . ($j+1);
            $personInPicturesRowURL = $personInPicturesRow['picture_url'];
            $personinPicturesCaptionKey = "personPictureCaption" . ($j+1);
            $personInPicturesRowCaption = $personInPicturesRow['picture_caption'];
            $personinPicturesCreditKey = "personPictureCredit" . ($j+1);
            $personInPicturesRowCredit = $personInPicturesRow['picture_credit'];
            //Add key/value pairs to person's picture array
            $personPicturesArray[$personinPicturesURLKey] = $personInPicturesRowURL;
            $personPicturesArray[$personinPicturesCaptionKey] = $personInPicturesRowCaption;
            $personPicturesArray[$personinPicturesCreditKey] = $personInPicturesRowCredit;
        };
    };

    //Fetch array on video results
    $personVideosArray = array();
    if(mysqli_num_rows($personInVideosResult)>0){
        for($k=0; $k<mysqli_num_rows($personInVideosResult); $k++){
            $personInVideosRow = mysqli_fetch_array($personInVideosResult, MYSQLI_ASSOC);
            //Trim row down to video_id, video_url, video_caption and video_credit, and create key/value pairs
            $personinVideosURLKey = "personVideoURL" . ($k+1);
            $personInVideosRowURL = $personInVideosRow['video_url'];
            $personinVideosCaptionKey = "personVideoCaption" . ($k+1);
            $personInVideosRowCaption = $personInVideosRow['video_caption'];
            $personinVideosCreditKey = "personVideoCredit" . ($k+1);
            $personInVideosRowCredit = $personInVideosRow['video_credit'];
            //Add key/value pairs to person's video array
            $personVideosArray[$personinVideosURLKey] = $personInVideosRowURL;
            $personVideosArray[$personinVideosCaptionKey] = $personInVideosRowCaption;
            $personVideosArray[$personinVideosCreditKey] = $personInVideosRowCredit;
        };
    };

    //Fetch array on country results
    $personCountryArray = array();
    if(mysqli_num_rows($personCountryResult)>0){
        for($l=0; $l<mysqli_num_rows($personCountryResult); $l++){
            $personCountryRow = mysqli_fetch_array($personCountryResult, MYSQLI_ASSOC);
            //Trim row down to country(name), country_flag(url) and create key/value pairs
            $personCountryNameKey = 'personCountryName';
            $personCountryName = $personCountryRow['country'];
            $personCountryFlagKey = 'personCountryFlag';
            $personCountryFlagURL = $personCountryRow['country_flag'];
            //Add key/value pairs to person's country array
            $personCountryArray[$personCountryNameKey] = $personCountryName;
            $personCountryArray[$personCountryFlagKey] = $personCountryFlagURL;
        };
    };
}

function addTournamentMedia(){
    global $link;
    global $tournamentID;
    global $tournamentPicturesArray;
    global $tournamentVideosArray;
    // global $requiredArticleRow;
    //Build query for finding pictures attached to tournament
    $checkforTournamentInPictures = "SELECT * FROM picture_gallery WHERE tournament_edition_id='$tournamentID'";
    //Build query for finding videos attached to tournament
    $checkforTournamentInVideos = "SELECT * FROM video_vault WHERE tournament_edition_id='$tournamentID'";    
    //Run these queries on the database
    $tournamentInPicturesResult = mysqli_query($link, $checkforTournamentInPictures);
    $tournamentInVideosResult = mysqli_query($link, $checkforTournamentInVideos);

    //Fetch array on picture results
    $tournamentPicturesArray = array();
    if(mysqli_num_rows($tournamentInPicturesResult)>0){
        for($m=0; $m<mysqli_num_rows($tournamentInPicturesResult); $m++){
            $tournamentInPicturesRow = mysqli_fetch_array($tournamentInPicturesResult, MYSQLI_ASSOC);
            //Trim row down to picture_id, picture_url, picture_caption and picture_credit, and create key/value pairs
            $tournamentinPicturesURLKey = "tournamentPictureURL" . ($m+1);
            $tournamentInPicturesRowURL = $tournamentInPicturesRow['picture_url'];
            $tournamentinPicturesCaptionKey = "tournamentPictureCaption" . ($m+1);
            $tournamentInPicturesRowCaption = $tournamentInPicturesRow['picture_caption'];
            $tournamentinPicturesCreditKey = "tournamentPictureCredit" . ($m+1);
            $tournamentInPicturesRowCredit = $tournamentInPicturesRow['picture_credit'];
            //Add key/value pairs to tournament's picture array
            $tournamentPicturesArray[$tournamentinPicturesURLKey] = $tournamentInPicturesRowURL;
            $tournamentPicturesArray[$tournamentinPicturesCaptionKey] = $tournamentInPicturesRowCaption;
            $tournamentPicturesArray[$tournamentinPicturesCreditKey] = $tournamentInPicturesRowCredit;
        };
    };
        
    //Fetch array on video results
    $tournamentVideosArray = array();
    if(mysqli_num_rows($tournamentInVideosResult)>0){
        for($n=0; $n<mysqli_num_rows($tournamentInVideosResult); $n++){
            $tournamentInVideosRow = mysqli_fetch_array($tournamentInVideosResult, MYSQLI_ASSOC);
            //Trim row down to video_id, video_url, video_caption and video_credit, and create key/value pairs
            $tournamentinVideosURLKey = "tournamentVideoURL" . ($n+1);
            $tournamentInVideosRowURL = $tournamentInVideosRow['video_url'];
            $tournamentinVideosCaptionKey = "tournamentVideoCaption" . ($n+1);
            $tournamentInVideosRowCaption = $tournamentInVideosRow['video_caption'];
            $tournamentinVideosCreditKey = "tournamentVideoCredit" . ($n+1);
            $tournamentInVideosRowCredit = $tournamentInVideosRow['video_credit'];
            //Add key/value pairs to tournament's video array
            $tournamentVideosArray[$tournamentinVideosURLKey] = $tournamentInVideosRowURL;
            $tournamentVideosArray[$tournamentinVideosCaptionKey] = $tournamentInVideosRowCaption;
            $tournamentVideosArray[$tournamentinVideosCreditKey] = $tournamentInVideosRowCredit;
        };
    };
}

function addPersonTournaments(){
    global $link;
    global $personID;
    global $personMajorTournamentWinsArray;
    //Build query for finding tournaments won by person
    $checkforPersonInTournaments = "SELECT * FROM tournament_edition WHERE person_id='$personID' ORDER BY end_date ASC";
    //Run this query on the database
    $personInTournamentsResult = mysqli_query($link, $checkforPersonInTournaments);
    //Fetch array on tournament series in which person won at least one edition
    $personWonInTheseSeriesIDs = array();
    if(mysqli_num_rows($personInTournamentsResult)>0){
        for($t=0; $t<mysqli_num_rows($personInTournamentsResult); $t++){
            $personInTournamentRow = mysqli_fetch_array($personInTournamentsResult, MYSQLI_ASSOC);
            array_push($personWonInTheseSeriesIDs, $personInTournamentRow['tournament_series_id']);
        };
    };
    //Remove Duplicates from array of tournament series in which person won at least one edition - NB: This is still sorted in chronological order
    $personWonInTheseSeriesIDs = array_unique($personWonInTheseSeriesIDs);
    // print_r($personWonInTheseSeriesIDs);
    //Go through array of tournament series IDs to get names and when the person won an edition in that series. Start by setting variable
    foreach($personWonInTheseSeriesIDs as $personWonInThisSeriesID){
        //Build queries for getting names of editions in the tournament series where the person in the search was the winner, and in what years they won
        $personWonInThisSeriesNameQuery = "SELECT * FROM tournament_series WHERE tournament_series_id='$personWonInThisSeriesID'";
        $personWonInThisSeriesInTheseYearsQuery = "SELECT end_date FROM tournament_edition WHERE tournament_series_id='$personWonInThisSeriesID' AND person_id='$personID' ORDER BY end_date";
        //Run these queries on the database
        $personWonInThisSeriesIDResult = mysqli_query($link, $personWonInThisSeriesNameQuery);
        $personWonInThisSeriesInTheseYearsResult = mysqli_query($link, $personWonInThisSeriesInTheseYearsQuery);
        //Fetch array of names of series in which person won at least one edition
        $personWonInThisSeriesIDRow = mysqli_fetch_array($personWonInThisSeriesIDResult, MYSQLI_ASSOC);
        //Get every year when person won an edition in this series and add them to an array for each series
        $yearsPersonWonInThisSeries = array();
        for($u=0; $u<mysqli_num_rows($personWonInThisSeriesInTheseYearsResult); $u++){
            $personWonInThisSeriesInTheseYearsRow = mysqli_fetch_array($personWonInThisSeriesInTheseYearsResult, MYSQLI_ASSOC);
            $yearPersonWonInThisSeries = substr($personWonInThisSeriesInTheseYearsRow['end_date'],0,4);
            array_push($yearsPersonWonInThisSeries, $yearPersonWonInThisSeries);
            //Convert array of years into string of year, separated by spaces
            $yearsPersonWonInThisSeriesInlineList = implode(" ", $yearsPersonWonInThisSeries);
            // print_r($yearsPersonWonInThisSeriesInlineList);
            //Build list of years, separated by spaces, when the person won an edition in this series
        };
        //Build HTML showing person's tournament victories
        $personWonInTheseSeriesNamesAndYears .= $personWonInThisSeriesIDRow['tournament_series_name'] . " " . $yearsPersonWonInThisSeriesInlineList ."<br />";
    };
    $personMajorTournamentWinsArray = array();
    // print_r($personWonInTheseSeriesNamesAndYears);
    $personMajorTournamentWinsArray['majorTournamentWins'] = $personWonInTheseSeriesNamesAndYears;
}

?>