<?php session_start(); ?>

<!-- Nav bar visible only for logged-in users -->
<?php if (isset($_SESSION['username'])): ?>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="upload.php">Upload</a></li>
            <li><a href="profile.php">Me</a></li>
            <li><a href="search.php">Search</a></li> <!-- Link to Search Page -->
        </ul>
    </nav>
<?php endif; ?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view your profile.";
    exit;
}

// Get the username from the session
$username = $_SESSION['username'];

// Directory where video JSON files are stored
$directory = 'tbvidsdat/';

// Find all video files uploaded by the logged-in user
$user_videos = [];

// Loop through each JSON file in the directory
foreach (glob($directory . "*.json") as $file) {
    // Read the content of each video JSON file
    $video_data = json_decode(file_get_contents($file), true);
    
    // Check if the video's username matches the logged-in user
    if ($video_data['username'] == $username) {
        $user_videos[] = $video_data;
    }
}

// Directory where user data JSON files are stored
$user_directory = 'tbusersdat/';

// Fetch user data
$user_data = json_decode(file_get_contents($user_directory . $username . '.json'), true);

// Fetch list of subscribers
$subscribers = $user_data['subscribers'] ?? [];
?>

<!-- Display user's profile -->
<div class="user-profile">
    <h2>Your Profile: <?php echo htmlspecialchars($username); ?></h2>
    <p>Subscribers: <?php echo count($subscribers); ?></p>

    <!-- My Subscribers Button -->
    <button id="subscribersButton" class="subscribers-button">My Subscribers</button>

    <!-- The Modal -->
    <div id="subscribersModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Subscribers of <?php echo htmlspecialchars($username); ?>:</h3>
            <ul>
                <?php
                if (count($subscribers) > 0) {
                    foreach ($subscribers as $subscriber) {
                        echo "<li>" . htmlspecialchars($subscriber) . "</li>";
                    }
                } else {
                    echo "<li>No subscribers found.</li>";
                }
                ?>
            </ul>
        </div>
    </div>

    <!-- Display user's uploaded videos -->
    <div class="user-videos">
        <h2>Your Uploaded Videos</h2>
        <?php
        if (count($user_videos) > 0) {
            foreach ($user_videos as $video) {
                // Generate a link to view the video page
                $video_filename = basename($video['video_title']) . '.json';
                echo "<div class='video-item'>";
                echo "<a href='view_video.php?video=" . urlencode($video_filename) . "'>";
                echo "<img src='" . htmlspecialchars($video['thumbnail_url']) . "' alt='Thumbnail' width='100'>";
                echo "<h3>" . htmlspecialchars($video['video_title']) . "</h3>";
                echo "</a>";

                // Add the delete button
                echo "<form action='delete.php' method='post' onsubmit='return confirm(\"Are you sure you want to delete this video?\");'>";
                echo "<input type='hidden' name='video_title' value='" . htmlspecialchars($video['video_title']) . "'>";
                echo "<button type='submit' class='delete-button'>Delete</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>You have not uploaded any videos yet.</p>";
        }
        ?>
    </div>
</div>

<!-- Modal Popup Styling -->
<style>
    /* Button styling */
    .subscribers-button {
        background-color: #32a4ff; /* Mild blue background */
        color: #ffffff; /* White text */
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 20px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .subscribers-button:hover {
        background-color: #2486cc; /* Slightly darker blue */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Subtle hover shadow */
    }

    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6); /* Black with higher opacity */
        padding-top: 60px;
    }

    .modal-content {
        background-color: #1f2329; /* Dark background for modal */
        color: #ffffff; /* White text */
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #32a4ff; /* Blue border */
        width: 80%;
        max-width: 400px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); /* Subtle shadow for modal */
    }

    .modal-content ul {
        list-style-type: none;
        padding: 0;
    }

    .modal-content ul li {
        margin: 10px 0;
        color: #d9e6f2; /* Softer light text */
    }

    /* Close button styling */
    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 25px;
        font-family: sans-serif;
        transition: color 0.3s ease;
    }

    .close:hover,
    .close:focus {
        color: #32a4ff; /* Mild blue on hover */
        text-decoration: none;
        cursor: pointer;
    }
</style>
<!-- JavaScript for Modal Popup -->
<script>
    // Get the modal
    var modal = document.getElementById("subscribersModal");

    // Get the button that opens the modal
    var btn = document.getElementById("subscribersButton");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

    	 <style>
    /* General body styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #2c2f37; /* Dark grey background */
        color: #f4f4f4; /* Light text color */
        margin: 0;
        padding: 0;
    }

    /* Header */
    header {
        background-color: #1f2329; /* Darker grey for header */
        color: #ffffff;
        padding: 10px;
        text-align: center;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
    }

    /* Links in header */
    a {
        color: #32a4ff; /* Mild blue for links */
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Footer */
    footer {
        background-color: #1f2329; /* Dark footer */
        color: #ffffff;
        padding: 10px;
        text-align: center;
        position: fixed;
        bottom: 0;
        width: 100%;
    }

    /* Button Styling */
    button {
        background-color: #32a4ff; /* Mild blue background */
        color: #fff;
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #1f80d5; /* Darker blue when hovered */
    }

    /* Video content styling */
    .video-content {
        background-color: #3d434d; /* Slightly lighter grey */
        border-radius: 8px;
        padding: 15px;
        margin: 10px 0;
        text-align: center;
    }

    .video-content img {
        width: 100%; /* Ensures the thumbnail takes up the full width of its container */
        height: auto; /* Keeps the image's aspect ratio */
        object-fit: cover; /* Ensures the image covers the container without stretching */
        border-radius: 5px; /* Optional for rounded corners */
    }

    .video-content h3 {
        color: #ffffff;
        font-size: 18px;
        margin: 10px 0;
    }

    .video-content p {
        color: #cccccc;
        font-size: 14px;
    }

    /* Form input fields */
    input[type="text"], input[type="password"], textarea {
        background-color: #333a42;
        color: #f4f4f4;
        border: 1px solid #555;
        padding: 10px;
        width: 100%;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    input[type="checkbox"] {
        margin-right: 10px;
    }

    /* Video Detail Page */
    .video-detail {
        text-align: center;
        background-color: #3d434d;
        padding: 20px;
        border-radius: 10px;
    }

    /* Navbar styles */
    nav {
        background-color: #1e1e1e;
        padding: 10px 0;
        text-align: center;
        overflow-x: auto; /* Make navbar horizontally scrollable */
        white-space: nowrap; /* Prevent line breaks */
    }

    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: inline-flex;
        justify-content: center;
    }

    nav ul li {
        margin: 0 15px;
        display: inline-block;
    }

    nav ul li a {
        text-decoration: none;
        color: #e0e0e0;
        font-size: 18px;
        font-weight: bold;
        padding: 5px 10px;
        transition: color 0.3s ease;
    }

    nav ul li a:hover {
        color: #76c7c0;
    }

    .video-detail video {
        width: 80%;
        max-width: 700px;
        margin-bottom: 20px;
    }

    .video-detail h1 {
        color: #ffffff;
    }

    .video-detail p {
        color: #cccccc;
    }

    /* Center the form elements */
    form {
        max-width: 500px;
        margin: 30px auto;
        padding: 20px;
        background-color: #3d434d;
        border-radius: 10px;
    }

    form label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    form input[type="text"], form input[type="password"], form textarea {
        margin-bottom: 15px;
    }

    /* Fix for footer */
    footer {
        z-index: 999;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #2c2f37;
        font-weight: bold;
    }

    td {
        background-color: #2c2f37;
    }

    tr:nth-child(even) {
        background-color: #2c2f37;
    }

    tr:hover {
        background-color: #2c2f37;
    }

    /* Style for warning pop-up */
    .confirmation-popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #ffffff;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .popup-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .popup-buttons {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
    }

    .popup-buttons button {
        padding: 6px 12px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
    }
</style>