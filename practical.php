<!DOCTYPE html>
<html>
<head>
    <title>Weather History</title>
</head>
<body>
    <h1>Search for Past Weather Conditions</h1>

    <form action="" method="GET">
        <label for="city">Enter City:</label>
        <input type="text" id="city" name="city" required>
        <button type="submit">Search</button>
    </form>
    
    <?php
    // Step 1: Establish a connection to the MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "database";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Step 2: Handle the search functionality
    if (isset($_GET['city'])) {
        // Get the searched city from the form
        $searchedCity = $_GET['city'];

        

        // Step 3: Retrieve weather history data from the API for the searched city
$apiKey = "f2d3250979dd69e544ddafb8ed2e7225"; // Replace with your OpenWeatherMap API key

$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$searchedCity&dt=&appid=f2d3250979dd69e544ddafb8ed2e7225&units=metric";

$dayData = array();


$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$apiData = curl_exec($curl);
curl_close($curl);

// Step 4: Parse and extract relevant data from the API response
$data = json_decode($apiData, true); // Assuming the API response is in JSON format

if ($data && $data['cod'] === 200) {
    $date = date('Y-m-d'); // Get the current date
    $location = $searchedCity;
    $temperature = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $windSpeed = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];

    // Step 5: Insert the data into the database
    $sql = "INSERT INTO weatherapp (date, location, temperature, humidity, wind, pressure) VALUES ('$date', '$location', '$temperature', '$humidity', '$windSpeed', '$pressure')";

    if (mysqli_query($conn, $sql)) {
        echo "Data inserted successfully.";
    } else {
        echo "Error inserting data: " . mysqli_error($conn);
    }
} else {
    echo "Failed to fetch weather data for the city: $searchedCity";
}
    }

    
    if (isset($_GET['city'])) {
        // Get the searched city from the form
        $searchedCity = $_GET['city'];
    } else {
        // Set the default city
        $searchedCity = "Mesa";
    }

    // Step 3: Retrieve weather history data from the database for the searched city
    $sql = "SELECT * FROM weatherapp WHERE location = '$searchedCity'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Step 4: Display the fetched weather data
        echo "<h2>Weather Data for $searchedCity</h2>";
        echo "<table>";
        echo "<tr><th>Date</th><th>Location</th><th>Temperature</th><th>Humidity</th><th>Wind</th><th>Pressure</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" . $row['location'] . "</td>";
            echo "<td>" . $row['temperature'] . "</td>";
            echo "<td>" . $row['humidity'] . "</td>";
            echo "<td>" . $row['wind'] . "</td>";
            echo "<td>" . $row['pressure'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No weather data available for the city: $searchedCity";
    }

// Close the database connection
mysqli_close($conn);
?>
</body>
</html>
