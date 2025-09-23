var leisureCentresData ;
var map;

//  Initialize the map
function initialize() {
    map = L.map('mapdiv', { zoomControl: true });
    map.setView([53.8008, -1.5491], 11);  // Leeds 

    // Load OpenStreetMap tiles
    L.tileLayer('http://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data ©OpenStreetMap contributors, CC-BY-SA, Imagery ©CloudMade',
        maxZoom: 18
    }).addTo(map);
}

// Customize marker icon
var customIcon = L.icon({
    iconUrl: 'image/marker.gif', 
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
});

// showMap
function showMap() {
    const mapContainer = document.getElementById('map-container');
    mapContainer.style.height = '450px'; 

    if (!map) {
        initialize();
    }
}

//  hideMap
function hideMap() {
    const mapContainer = document.getElementById('map-container');
    mapContainer.style.height = '0'; 
}

// fetchData
function fetchData() {
    // Define array to hold results returned from server
    leisureCentresData = [];
    
    // AJAX request to server
    $.getJSON("fetchData.php", function(results) {
        console.log(results); // Check the returned data
        // Add data one by one using a for loop
        for (var i = 0; i < results.length; i++) {
            leisureCentresData.push({
                id: results[i].id,
                name: results[i].leisureCentre,
                lat: results[i].lat,
                lon: results[i].lon,
                address: results[i].address,
				postcode: results[i].postcode,
                phone: results[i].telephone,
                website: results[i].website,

            });
        }

        plotLeisureCentres(); // Plot map markers
    });
}

// Plot map markers
function plotLeisureCentres() {
    for (var i = 0; i < leisureCentresData.length; i++) {
        console.log(leisureCentresData[i].lat, leisureCentresData[i].lon); //Output coordinate data
        var markerLocation = new L.LatLng(leisureCentresData[i].lat, leisureCentresData[i].lon);
        var marker = new L.Marker(markerLocation, { icon: customIcon }).addTo(map)
            .bindPopup(
                "<b>" + leisureCentresData[i].name + "</b><br>" +
                leisureCentresData[i].address + "<br>postcode: " +
				leisureCentresData[i].postcode + "<br>Phone: " +
                leisureCentresData[i].phone + "<br><a href='" +
                leisureCentresData[i].website + "' target='_blank'>Website</a>"
            );
    }
}

//  Clear data
function clearData() {
    map.eachLayer(function(layer) {
        if (layer.getLatLng) {
            map.removeLayer(layer); 
        }
    });
}

// Browse centres
function browseCentres() {
    showMap();     //  First expand the map container
    fetchData();   // Then fetch data and plot markers
}

// Filter centres
function filterCentres(event) {
    event && event.preventDefault(); //Prevent default form submission behavior
    
    let searchQuery = document.getElementById('searchBox').value;
    let serviceFilter = document.getElementById('serviceFilter').value;

    $.ajax({
      url: 'fetchData.php',  // PHP file to handle backend requests
      type: 'POST',
      dataType: 'json', 
      data: {
        search: searchQuery,
        service: serviceFilter
      },
      success: function(response) {
        let html = '';

        //  Assume response is an array object returned via PHP json_encode
        for (let i = 0; i < response.length; i++) {
            html += `
              <div class="centre-item">
                <h4>${response[i].leisureCentre}</h4>
                <p>${response[i].address}</p>
                <p>${response[i].postcode}</p>
                <p>${response[i].telephone}</p>
                <p>${response[i].email}</p>
            </div>
          `;
        }

        $('#centreList').html(html);
        
      }
    });
}

//Filter centres
function filterCentres1(event) {
    event && event.preventDefault(); // Prevent default form submission behavior
    
    let searchQuery = document.getElementById('searchBox').value;
    let serviceFilter = document.getElementById('serviceFilter').value;

    $.ajax({
      url: 'fetchData.php',  // PHP file to handle backend requests
      type: 'POST',
      dataType: 'json', 
      data: {
        search: searchQuery,
        service: serviceFilter
      },
      success: function(response) {
        let html = '';

        // Assume response is an array object returned via PHP json_encode
for (let i = 0; i < response.length; i++) {
    html += `
      <div class="centre-item">
        <h4>${response[i].leisureCentre}</h4>
        <p>${response[i].address}</p>
        <table class="opening-hours-table">
          <tr><th>Day</th><th>Opening Hours</th></tr>
          <tr><td>Monday</td><td>${response[i].hours.monday || 'N/A'}</td></tr>
          <tr><td>Tuesday</td><td>${response[i].hours.tuesday || 'N/A'}</td></tr>
          <tr><td>Wednesday</td><td>${response[i].hours.wednesday || 'N/A'}</td></tr>
          <tr><td>Thursday</td><td>${response[i].hours.thursday || 'N/A'}</td></tr>
          <tr><td>Friday</td><td>${response[i].hours.friday || 'N/A'}</td></tr>
          <tr><td>Saturday</td><td>${response[i].hours.saturday || 'N/A'}</td></tr>
          <tr><td>Sunday</td><td>${response[i].hours.sunday || 'N/A'}</td></tr>
        </table>
      </div>
    `;
}

        $('#openHoursContainer').html(html);
        
      }
    });
}



// Wait for the whole page to load before binding button events
window.onload = function () {
    // Bind click event for View Map button
    document.getElementById('showMapButton').onclick = function () {
        showMap();
    };
	
	// Collapse the map when clicking the Hide Map button
    document.getElementById('hideMapButton').onclick = function () {
        hideMap();
    };
  
    // Bind click event for Browse Centres button
    document.getElementById('browseCentres').onclick = function () {
        browseCentres();  // Expand the map first, then fetch data
    };
	
    document.getElementById('clearData').onclick = function () {
        clearData(); //Clear all markers individually
    };
};