<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <style>
    /* General body styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #12151a; /* Darker background */
        color: #d9e6f2; /* Softer text color */
        margin: 0;
        padding: 0;
    }

    .divider {
        height: 1px;
        background-color: #2b2f36; /* Divider color */
        margin: 20px 0;
        width: 100%;
    }

    /* Header styling */
    header {
        background-color: #0e1624; /* Darker header background */
        color: #ffffff;
        padding: 10px;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
        display: inline-block;
    }

    /* Navbar styling */
    nav {
        background-color: #0d1218; /* Navbar background */
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 0;
        text-align: center;
    }

    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: inline-flex;
    }

    nav ul li {
        margin: 0 15px;
        display: inline-block;
    }

    nav ul li a {
        text-decoration: none;
        color: #d9e6f2; /* Link text color */
        font-size: 18px;
        font-weight: bold;
        padding: 5px 10px;
        transition: color 0.3s ease, background-color 0.3s ease;
    }

    nav ul li a:hover {
        color: #32a4ff; /* Highlight text color */
        background-color: #1b2837; /* Subtle hover background */
        border-radius: 5px;
    }

    /* Scrollbar styling */
    nav::-webkit-scrollbar {
        height: 6px;
    }

    nav::-webkit-scrollbar-thumb {
        background-color: #2b2f36; /* Scrollbar thumb color */
        border-radius: 3px;
    }

    nav::-webkit-scrollbar-thumb:hover {
        background-color: #32a4ff; /* Scrollbar hover color */
    }

    /* Videos container styling */
    .videos {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
        padding: 20px;
    }

    .videos img {
        width: 100%;
        height: auto;
        aspect-ratio: 16/9;
        object-fit: cover;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Subtle shadow */
    }

    .videos a {
        display: block;
        max-width: 300px;
        color: #d9e6f2;
        text-decoration: none;
        background-color: #1b2837; /* Video card background */
        border-radius: 8px;
        padding: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: left;
    }

    .videos a:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.5); /* Enhanced shadow */
    }

    .videos h3 {
        font-size: 18px;
        margin: 10px 0;
        text-align: left;
    }

    .video-info {
        font-size: 14px;
        color: #96a9bc; /* Muted text for video info */
        text-align: left;
    }

    /* Footer styling */
    footer {
        background-color: #0e1624;
        color: #d9e6f2;
        padding: 10px;
        text-align: center;
    }
</style>
    <title> KazVooru Video Sharing Website </title>
        <link rel="icon" href="favicon.ico" type="image/x-icon"> 
</head>
<body>
    <!-- Header -->
    <header>
        <h1> KazVooru 🐺✨ </h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
            <a href="logout.php">Log Out</a>
        <?php else: ?>
            <a href="login.php">Log In</a>
        <?php endif; ?>
    </header>

    <!-- Navbar for logged-in users -->
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

    <!-- Videos Section -->
<div class="videos">
    <?php
    $videos = [];
    $directory = 'tbvidsdat/';

    // Scan for all JSON files in the directory
    foreach (glob($directory . "*.json") as $file) {
        $video_data = json_decode(file_get_contents($file), true);
        $videos[] = $video_data;
    }

    // Shuffle the videos array
    shuffle($videos);

    // Limit the number of videos to 15
    $videos = array_slice($videos, 0, 15);
    ?>

    <?php foreach ($videos as $index => $video): ?>
        <a href="view_video.php?video=<?php echo urlencode(basename($video['video_title']) . '.json'); ?>">
            <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" alt="Thumbnail">
            <h3><?php echo htmlspecialchars($video['video_title']); ?></h3>
            <div class="video-info">
                <span class="upload-date"><?php echo htmlspecialchars($video['upload_date']); ?></span>
            </div>
        </a>
        <?php if ($index < count($videos) - 1): // Add a divider, but not after the last video ?>
            <div class="divider"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Refresh Button -->
<div class="refresh-container">
    <button id="refreshButton" class="refresh-button">Refresh</button>
</div>

<!-- Modal and additional styling for button -->
<style>
    .refresh-container {
        text-align: center;
        margin-top: 20px;
    }

    .refresh-button {
        background-color: #32a4ff; /* Primary blue background */
        color: #ffffff; /* White text */
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .refresh-button:hover {
        background-color: #2486cc; /* Slightly darker blue on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Subtle hover shadow */
    }
</style>

<!-- JavaScript for Refresh Button -->
<script>
    // When the user clicks the refresh button, reload the page
    document.getElementById("refreshButton").onclick = function() {
        location.reload();
    };
</script>
</body>
</html>