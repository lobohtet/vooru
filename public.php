<?php
session_start();

// Check if a user is specified in the URL
if (!isset($_GET['user'])) {
    echo "User not specified.";
    exit;
}

// Get the username from the URL (for example, public.php?user=jack.json)
$username_file = $_GET['user'];

// Assuming the username is stored in the filename without the .json extension
$username = basename($username_file, ".json");

// Path to the user data in the tbusersdat folder
$user_data_file = 'tbusersdat/' . $username_file;

// Check if the user's data file exists
if (!file_exists($user_data_file)) {
    echo "User data not found.";
    exit;
}

// Read user data from the JSON file
$user_data = json_decode(file_get_contents($user_data_file), true);

// If the user data is not valid, show an error
if (!$user_data) {
    echo "Invalid user data.";
    exit;
}

// Get the username from the data (use the "username" key here)
$user_name = isset($user_data['username']) ? htmlspecialchars($user_data['username']) : 'User Name Not Found';

// Get the subscribers list (initialize as empty array if not present)
$subscribers = isset($user_data['subscribers']) ? $user_data['subscribers'] : [];

// Check if the user is logged in and handle subscription
if (isset($_SESSION['username'])) {
    $logged_in_user = $_SESSION['username'];

    // Check if the user is already subscribed
    if (isset($_POST['subscribe'])) {
        // Subscribe the logged-in user to this user
        if (!in_array($logged_in_user, $subscribers)) {
            $subscribers[] = $logged_in_user; // Add to the subscriber list
            $user_data['subscribers'] = $subscribers; // Update the user's data

            // Save the updated user data back to the file
            file_put_contents($user_data_file, json_encode($user_data, JSON_PRETTY_PRINT));
        }
    }
}

// Directory where video JSON files are stored
$directory = 'tbvidsdat/';

// Find all videos uploaded by this user
$user_videos = [];

// Loop through all video files and check if the username matches
foreach (glob($directory . "*.json") as $file) {
    $video_data = json_decode(file_get_contents($file), true);
    if ($video_data['username'] == $username) {
        $user_videos[] = $video_data;
    }
}
?>
	
	<?php if (isset($_SESSION['username'])): ?>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="upload.php">Upload</a></li>
            <li><a href="profile.php">Me</a></li>
            <li><a href="search.php">Search</a></li>
   <li><a href="subbed.php">Subscriptions</a></li>
 <!-- Link to Search Page -->
        </ul>
    </nav>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Profile - <?php echo $user_name; ?></title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #2c2f37; /* Dark grey background */
        color: #f4f4f4; /* Light text color */
        margin: 0;
        padding: 0;
        text-align: left; /* Align all text to the left */
    }

    header {
        background-color: #1f2329;
        color: #ffffff;
        padding: 10px;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
    }

    .user-videos {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .video-item {
        background-color: #3a3f47;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }

    .video-item img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .video-item h3 {
        font-size: 18px;
        margin: 10px 0;
    }

    .subscribe-button {
        background-color: #76c7c0;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .subscribe-button:hover {
        background-color: #56a29a;
    }

    /* Navigation Bar */
    nav {
        background-color: #1e1e1e;
        overflow-x: auto; /* Makes the navbar horizontally scrollable */
        white-space: nowrap; /* Prevents items from wrapping to the next line */
        padding: 10px 0;
        text-align: center;
    }

    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: inline-flex; /* Flexbox for horizontal alignment */
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
        color: #76c7c0; /* Highlight color when hovering */
    }

    /* Optional: Add a scrollbar style */
    nav::-webkit-scrollbar {
        height: 6px; /* Horizontal scrollbar height */
    }

    nav::-webkit-scrollbar-thumb {
        background-color: #555; /* Scrollbar thumb color */
        border-radius: 3px;
    }

    nav::-webkit-scrollbar-thumb:hover {
        background-color: #76c7c0; /* Scrollbar thumb hover color */
    }
</style>
</head>
<body>

    <h1>Profile of <?php echo $user_name; ?></h1>

    <h2>Subscriber Count: <?php echo count($subscribers); ?> </h2>

    <?php if (isset($_SESSION['username'])): ?>
        <form method="post">
            <button class="subscribe-button" type="submit" name="subscribe">Subscribe</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to subscribe.</p>
    <?php endif; ?>

    <h2>Uploaded Videos</h2>
    <div class="user-videos">
        <?php
        if (count($user_videos) > 0) {
            foreach ($user_videos as $video) {
                $video_filename = basename($video['video_title']) . '.json';
                echo "<div class='video-item'>";
                echo "<a href='view_video.php?video=" . urlencode($video_filename) . "'>";
                echo "<img src='" . htmlspecialchars($video['thumbnail_url']) . "' alt='Thumbnail' width='100'>";
                echo "<h3>" . htmlspecialchars($video['video_title']) . "</h3>";
                echo "</a>";
                echo "</div>";
            }
        } else {
            echo "<p>This user has not uploaded any videos yet.</p>";
        }
        ?>
    </div>

</body>
</html>