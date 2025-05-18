<?php
include 'handel/database.php';

$continent = $_GET['continent'] ?? "Asia";

$sql = "SELECT * FROM trips";
if ($continent) {
    $sql .= " WHERE continent = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $continent);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$trips = [];
while ($row = $result->fetch_assoc()) {
    $trips[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Continent Tours</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        #notification {
            position: fixed !important;
            top: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 10001 !important;
            min-width: 300px;
            max-width: 80%;
            text-align: center;
        }

        nav ul {
            display: flex;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding-left: 0;
            padding-right: 10px;
            justify-content: flex-start;
            overflow: visible;
            white-space: nowrap;
            margin-left: -350px;
        }

        nav ul li a {
            color: #fff;
            font-size: 1rem;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        nav ul li a:hover {
            opacity: 0.7;
        }

        .hero {
            position: relative;
            height: 100vh;
            background-size: cover;
            background-position: center;
            transition: background 0.5s ease;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            padding: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .hero-content h1 {
            font-size: 60px;
            margin: 0;
        }

        .continent-tabs {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            position: absolute;
            bottom: 20px;
            width: 100%;
            z-index: 2;
        }

        .continent-tab {
            background: rgba(255, 255, 255, 0.8);
            color: black;
            margin: 0 10px;
            padding: 12px 30px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s, color 0.3s;
            font-size: 1rem;
        }

        .continent-tab.active {
            background: #fff;
            color: #333;
        }

        .continent-tab:hover {
            opacity: 0.7;
        }

        .places-container {
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;
            padding: 40px 0;
            background-color: #f5f5f5;
            transition: 0.5s ease-in-out;
        }

        .place {
            display: flex;
            flex-direction: column;
            width: 250px;
            margin: 15px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            overflow: hidden;
            text-align: center;
        }

        .place:hover {
            transform: scale(1.05);
        }

        .place img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .place-info {
            padding: 10px;
        }

        .place-info h3 {
            font-size: 18px;
            margin: 0;
        }

        .place-info p {
            margin: 10px 0;
            color: #777;
        }

        .place-info a {
            background: #007BFFFF;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .learn-more {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .learn-more:hover {
            background-color: #0056b3;
        }

        /* Add Trip Modal Styles */
        #addTripModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-y: auto;
        }

        #addTripModal>div {
            background: white;
            border-radius: 15px;
            width: 380px;
            max-width: 95%;
            max-height: 90vh;
            padding: 25px 30px 35px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            position: relative;
            animation: fadeInScale 0.4s ease forwards;
            overflow-y: auto;
        }

        #addTripModal h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-weight: 700;
        }

        #addTripModal label {
            font-weight: 600;
            color: #555;
            display: block;
            margin-bottom: 6px;
            margin-top: 15px;
        }

        #addTripModal select,
        #addTripModal input[type="number"],
        #addTripModal input[type="text"],
        #addTripModal input[type="date"] {
            width: 100%;
            padding: 8px 12px;
            border: 1.8px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        #addTripModal select:focus,
        #addTripModal input[type="number"]:focus,
        #addTripModal input[type="text"]:focus,
        #addTripModal input[type="date"]:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 6px rgba(74, 144, 226, 0.5);
        }

        #addTripModal button[type="submit"],
        #addTripModal button[type="button"] {
            padding: 10px 25px;
            border: none;
            border-radius: 0;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        #addTripModal button[type="submit"] {
            background: #4a90e2;
            color: white;
            margin-right: 15px;
        }

        #addTripModal button[type="submit"]:hover {
            background: #357ABD;
        }

        #addTripModal button[type="button"] {
            background: #ddd;
            color: #444;
        }

        #addTripModal button[type="button"]:hover {
            background: #bbb;
        }

        .date-pair {
            border: 1.5px solid #eee;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 15px;
            background: #fafafa;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Hide number input spinners */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        #tripMessage {
            display: none;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .add-trip-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px auto;
            justify-content: center;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            max-width: 90%;
        }

        .add-trip-btn {
            background: linear-gradient(45deg, #4a90e2, #007aff);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 22px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 6px 15px rgba(0, 122, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .add-trip-btn:hover {
            background: linear-gradient(45deg, #007aff, #0051a8);
            box-shadow: 0 8px 20px rgba(0, 81, 168, 0.6);
        }

        #addTripBox {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 300px;
            transition: all 0.3s ease;
        }

        #addTripBox input,
        #addTripBox textarea {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        #addTripBox button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #addTripBox button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo" style="color: white; font-size: 1.5rem; font-weight: 700;">
            &nbsp;&nbsp;&nbsp;&nbsp;✈ LIMITLESS JOURNEYS
        </div>

        <nav>
            <ul>
                <li><a href="project.html">Home</a></li>
                <li><a href="data.html">Profile</a></li>
                <li><a href="about2.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="hero" id="hero" style="background-image: url('imges/asia.jpeg');">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1 id="continentName">Asia</h1>
        </div>
    </div>

    <div class="continent-tabs">
    <a href="page2.php?continent=Asia" class="continent-tab <?= $continent === 'Asia' ? 'active' : '' ?>">Asia</a>
    <a href="page2.php?continent=Europe" class="continent-tab <?= $continent === 'Europe' ? 'active' : '' ?>">Europe</a>
    <a href="page2.php?continent=North%20America" class="continent-tab <?= $continent === 'North America' ? 'active' : '' ?>">North America</a>
    <a href="page2.php?continent=South%20America" class="continent-tab <?= $continent === 'South America' ? 'active' : '' ?>">South America</a>
    <a href="page2.php?continent=Africa" class="continent-tab <?= $continent === 'Africa' ? 'active' : '' ?>">Africa</a>
    <a href="page2.php?continent=Australia" class="continent-tab <?= $continent === 'Australia' ? 'active' : '' ?>">Australia</a>
    </div>





    <div id="notification"
        style="display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10001; padding: 12px 24px; background-color: #2E7D32; color: white; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); font-family: Arial, sans-serif; font-weight: bold; text-align: center;">
        <span id="notificationText"></span>
    </div>

    <div class="places-container" id="placesContainer">
        <?php if (empty($trips)): ?>
            <p>No trips found<?php echo $continent ? " for $continent" : ''; ?>.</p>
        <?php else: ?>
            <?php foreach ($trips as $trip): ?>
                <div class="place">
                    <img src="imges/<?php echo htmlspecialchars($trip['img']); ?>"
                        alt="<?php echo htmlspecialchars($trip['destination']); ?>">
                    <div class="place-info">
                        <h3><?php echo htmlspecialchars($trip['destination']); ?></h3>
                        <p><?php echo htmlspecialchars($trip['description']); ?></p>
                        <a href="istanbul.html" class="learn-more">Learn More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>



    <!-- Add Trip Modal -->
    <div id="addTripModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.5); z-index:10000; justify-content:center; align-items:center;">
        <div style="background:#fff; padding:20px; border-radius:10px; width:350px; position:relative;">
            <h3 style="text-align:center;">Add New Trip</h3>

            <form id="tripForm" onsubmit="submitTrip(event)">
                <label for="continentSelect">Continent:</label><br />

                <select id="destinationSelect" required>
                    <option value="" disabled selected>Select Destination</option>
                </select><br /><br />

                <h3>Choose Start Dates (multiple):</h3>
                <input type="text" id="multiStartDates" placeholder="Select start dates" readonly><br /><br />

                <div id="datesContainer"></div>

                <label for="availableSeats">Available Seats:</label><br />
                <input type="number" id="availableSeats" min="1" required /><br /><br />

                <label for="pricePerPerson">Price per Person:</label><br />
                <input type="number" id="pricePerPerson" min="0" step="0.01" required /><br /><br />

                <div style="text-align:center;">
                    <button type="submit" style="margin-right:10px;">Save</button>
                    <button type="button" onclick="closeAddTripModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="add-trip-section">

        <button class="add-trip-btn" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
            onclick="clearErrors('exampleModal')">
            + Add New Trip
        </button>


    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">


                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Trip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="myForm" onsubmit="SubmitHandler(event)" action="handel/addtrial.php"
                    enctype="multipart/form-data" method="POST">
                    <div class="modal-body">
                        <div id="errorMessages" style="color: red; margin-bottom: 10px;"></div>

                        <div class="mb-3">
                            <label for="tripName" class="form-label">Trip Name:</label>
                            <input type="text" id="tripName" name="tripName" class="form-control"
                                placeholder="Enter trip name" required>
                        </div>

                        <div class="mb-3">
                            <label for="tripDesc" class="form-label">Description:</label>
                            <textarea id="tripDesc" name="tripDesc" class="form-control" placeholder="Enter description"
                                rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tripImg" class="form-label">Image URL:</label>
                            <input type="file" id="tripImg" name="tripImg" class="form-control" accept="image/*"
                                required>

                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="price_per_person" class="form-label">Price Per Person:</label>
                            <input type="number" step="0.01" id="price_per_person" name="price_per_person"
                                class="form-control" placeholder="Enter price" required>
                        </div>

                        <div class="mb-3">
                            <label for="available_seats" class="form-label">Available Seats:</label>
                            <input type="number" id="available_seats" name="available_seats" class="form-control"
                                placeholder="Enter number of seats" required>
                        </div>
                        <div class="mb-3">
                            <label for="continentSelect" class="form-label">Continent:</label>
                            <select id="continentSelect" name="continent" class="form-select" required>
                                <option value="">Select Continent</option>
                                <option value="Asia">Asia</option>
                                <option value="Europe">Europe</option>
                                <option value="Africa">Africa</option>
                                <option value="North America">North America</option>
                                <option value="South America">South America</option>
                                <option value="Australia">Australia</option>
                            </select>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
        function clearErrors(modalid) {
            const modal = document.getElementById(modalid);

            const errorMessages = modal.querySelector("#errorMessages");

            if (errorMessages) {
                errorMessages.innerHTML = "";
            }
        }


        function showNotification(message, isSuccess) {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');

            notification.style.display = 'block';
            notification.style.backgroundColor = isSuccess ? '#2E7D32' : '#C62828';
            notification.style.color = 'white';
            notificationText.textContent = message;

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                    notification.style.opacity = '1';
                }, 300);
            }, 5000);
        }




    </script>

</body>

</html>