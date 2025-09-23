<?php 
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
   // Returns JSON data to Javascript file
    header("Content-type:application/json");

    // Connect to db 
    $pgsqlOptions = "host='localhost' dbname='geog5871' user='geog5871student' password='Geibeu9b'";
    $dbconn = pg_connect($pgsqlOptions) or die ('connection failure');

// 获取筛选条件 Get filter criteria
$search = isset($_POST['search']) ? $_POST['search'] : '';
$service = isset($_POST['service']) ? $_POST['service'] : '';

	
    // Define sql query (added siteID)
    $query = 'SELECT "siteID", "leisureCentre", "addressLine1", "addressLine2", "addressLine3", "poscode", "Latitude", "Longitude", 
    "telephone", "email", "website", 
    "mondayOpen", "mondayClosed", "tuesdayOpen", "tuesdayClosed", 
    "wednesdayOpen", "wednesdayClosed", "thursdayOpen", "thursdayClosed", 
    "fridayOpen", "fridayClosed", "saturdayOpen", "saturdayClosed", 
    "sundayOpen", "sundayClosed" ,"accessibilityChangingAndShowerFacilities","adaptedChangingRoomAndToilet","adaptedToilet","adultSocialCareAndDisabilityServices","automaticDoor","disabledToilet","handrailsOnStairsAndSpectatorAreas","inductionLoop","levelEntrance","liftAccess","poolHoist","rampedAccess","tennisWheelchairs","archery","astroTurfPitch","badmintonCentre","bellBoat","boatHire","bowlsHall","cafeBar","canoeing","climbing","climbingWall",	"footballPitch","freeParking","gym","hydrotherapyPool","indoorTrackAndField","kayaking","meetingRoom","offRoadBiking","orienteering","outDoorTrackAndField","powerboat","Sailing","sportsHall","squashCourts","studio","swimmingPool","tennisCourts","wiFi","windsurfing"
FROM leisure_centres2 WHERE 1=1';

// 添加过滤条件// Add filter criteria
if (!empty($search)) {
    $searchEscaped = pg_escape_string($search);
    $query .= " AND (\"leisureCentre\" ILIKE '%$searchEscaped%' OR \"addressLine1\" ILIKE '%$searchEscaped%')";
}
if (!empty($service)) {
    $serviceEscaped = pg_escape_string($service);
    $query .= " AND \"$serviceEscaped\" = true";
}
    //Execute query
	$result = pg_query($dbconn, $query) or die ('Query failed: '.pg_last_error());

    // Define new array to store results
    $leisureCentresData = array();

    // Loop through query results
   while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    // Populate leisureCentresData array with desired field names
    $leisureCentresData[] = array(
        "id" => $row["siteID"], // Adding the siteID
        "leisureCentre" => $row["leisureCentre"],
        "address" => $row["addressLine1"] . " " . $row["addressLine2"] . " " . $row["addressLine3"],
        "postcode" => $row["poscode"],
        "lat" => $row["Latitude"],
        "lon" => $row["Longitude"],
        "telephone" => $row["telephone"],
        "email" => $row["email"],
        "website" => $row["website"],
        "hours" => array(
            "monday" => ($row["mondayOpen"] ? $row["mondayOpen"] : 'N/A') . " - " . ($row["mondayClosed"] ? $row["mondayClosed"] : 'N/A'),
            "tuesday" => ($row["tuesdayOpen"] ? $row["tuesdayOpen"] : 'N/A') . " - " . ($row["tuesdayClosed"] ? $row["tuesdayClosed"] : 'N/A'),
            "wednesday" => ($row["wednesdayOpen"] ? $row["wednesdayOpen"] : 'N/A') . " - " . ($row["wednesdayClosed"] ? $row["wednesdayClosed"] : 'N/A'),
            "thursday" => ($row["thursdayOpen"] ? $row["thursdayOpen"] : 'N/A') . " - " . ($row["thursdayClosed"] ? $row["thursdayClosed"] : 'N/A'),
            "friday" => ($row["fridayOpen"] ? $row["fridayOpen"] : 'N/A') . " - " . ($row["fridayClosed"] ? $row["fridayClosed"] : 'N/A'),
            "saturday" => ($row["saturdayOpen"] ? $row["saturdayOpen"] : 'N/A') . " - " . ($row["saturdayClosed"] ? $row["saturdayClosed"] : 'N/A'),
            "sunday" => ($row["sundayOpen"] ? $row["sundayOpen"] : 'N/A') . " - " . ($row["sundayClosed"] ? $row["sundayClosed"] : 'N/A')
        ),
        // Handle the boolean fields (e.g., accessibility features)
        "accessibilityChangingAndShowerFacilities" => $row["accessibilityChangingAndShowerFacilities"] ? true : false,
        "adaptedChangingRoomAndToilet" => $row["adaptedChangingRoomAndToilet"] ? true : false,
        "adaptedToilet" => $row["adaptedToilet"] ? true : false,
        "adultSocialCareAndDisabilityServices" => $row["adultSocialCareAndDisabilityServices"] ? true : false,
        "automaticDoor" => $row["automaticDoor"] ? true : false,
        "disabledToilet" => $row["disabledToilet"] ? true : false,
        "handrailsOnStairsAndSpectatorAreas" => $row["handrailsOnStairsAndSpectatorAreas"] ? true : false,
        "inductionLoop" => $row["inductionLoop"] ? true : false,
        "levelEntrance" => $row["levelEntrance"] ? true : false,
        "liftAccess" => $row["liftAccess"] ? true : false,
        "poolHoist" => $row["poolHoist"] ? true : false,
        "rampedAccess" => $row["rampedAccess"] ? true : false,
        "tennisWheelchairs" => $row["tennisWheelchairs"] ? true : false,
        "archery" => $row["archery"] ? true : false,
        "astroTurfPitch" => $row["astroTurfPitch"] ? true : false,
        "badmintonCentre" => $row["badmintonCentre"] ? true : false,
        "bellBoat" => $row["bellBoat"] ? true : false,
        "boatHire" => $row["boatHire"] ? true : false,
        "bowlsHall" => $row["bowlsHall"] ? true : false,
        "cafeBar" => $row["cafeBar"] ? true : false,
        "canoeing" => $row["canoeing"] ? true : false,
        "climbing" => $row["climbing"] ? true : false,
        "climbingWall" => $row["climbingWall"] ? true : false,
        "footballPitch" => $row["footballPitch"] ? true : false,
        "freeParking" => $row["freeParking"] ? true : false,
        "gym" => $row["gym"] ? true : false,
        "hydrotherapyPool" => $row["hydrotherapyPool"] ? true : false,
        "indoorTrackAndField" => $row["indoorTrackAndField"] ? true : false,
        "kayaking" => $row["kayaking"] ? true : false,
        "meetingRoom" => $row["meetingRoom"] ? true : false,
        "offRoadBiking" => $row["offRoadBiking"] ? true : false,
        "orienteering" => $row["orienteering"] ? true : false,
        "outDoorTrackAndField" => $row["outDoorTrackAndField"] ? true : false,
        "powerboat" => $row["powerboat"] ? true : false,
        "Sailing" => $row["Sailing"] ? true : false,
        "sportsHall" => $row["sportsHall"] ? true : false,
        "squashCourts" => $row["squashCourts"] ? true : false,
        "studio" => $row["studio"] ? true : false,
        "swimmingPool" => $row["swimmingPool"] ? true : false,
        "tennisCourts" => $row["tennisCourts"] ? true : false,
        "wiFi" => $row["wiFi"] ? true : false,
        "windsurfing" => $row["windsurfing"] ? true : false
    );
}

	
    // Encode leisureCentresData array in JSON
    echo json_encode($leisureCentresData); 
    // Close db connection
    pg_close($dbconn);
?>
