<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view your subscriptions.";
    exit;
}

$logged_in_user = $_SESSION['username'];

// Find all users' data files
$directory = 'tbusersdat/';
$subscribed_videos = [];

// Loop through all user files
foreach (glob($directory . "*.json") as $file) {
    $user_data = json_decode(file_get_contents($file), true);
    
    // Check if the logged-in user is in the subscriber list of each user
    if (isset($user_data['subscribers']) && in_array($logged_in_user, $user_data['subscribers'])) {
        // Get the videos uploaded by this user
        $user_videos = [];
        $video_directory = 'tbvidsdat/';

        foreach (glob($video_directory . "*.json") as $video_file) {
            $video_data = json_decode(file_get_contents($video_file), true);
            if ($video_data['username'] == $user_data['username']) {
                $user_videos[] = $video_data;
            }
        }
        
        // Add videos from this user to the list
        $subscribed_videos = array_merge($subscribed_videos, $user_videos);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribed</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #2c2f37; /* Dark grey background */
        color: #f4f4f4; /* Light text color */
        margin: 0;
        padding: 0;
        text-align: left;
    }

    header {
        background-color: #1f2329;
        color: #ffffff;
        padding: 10px;
        text-align: center;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
    }

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

    .user-videos {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin: 20px;
    }

    .video-item {
        background-color: #3a3f47;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        width: calc(33.33% - 20px);
        box-sizing: border-box;
        margin-bottom: 20px;
    }

    .video-item img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .video-item h3 {
        font-size: 18px;
        margin: 10px 0;
        color: #ffffff;
    }

    /* For smaller screens, reduce video item width */
    @media (max-width: 768px) {
        .video-item {
            width: calc(50% - 20px);
        }
    }

    @media (max-width: 480px) {
        .video-item {
            width: 100%;
        }
    }
</style>
</head>
<body>

    <header>
        <h1>Vids from Subscribed Users</h1>
    </header>

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
    <div class="user-videos">
        <?php
        if (count($subscribed_videos) > 0) {
            foreach ($subscribed_videos as $video) {
                $video_filename = basename($video['video_title']) . '.json';
                echo "<div class='video-item'>";
                echo "<a href='view_video.php?video=" . urlencode($video_filename) . "'>";
                echo "<img src='" . htmlspecialchars($video['thumbnail_url']) . "' alt='Thumbnail' width='100'>";
                echo "<h3>" . htmlspecialchars($video['video_title']) . "</h3>";
                echo "</a>";
                echo "</div>";
            }
        } else {
            echo "<p>You are not subscribed to any users with videos.</p>";
        }
        ?>
    </div>

</body>
</html>