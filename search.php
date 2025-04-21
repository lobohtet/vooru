<?php
session_start();

// Handle the search query when the form is submitted
$search_term = "";
$search_results_videos = [];
$search_results_profiles = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_term = trim($_POST['search']);

    // Search for videos in the 'tbvidsdat' directory
    if (!empty($search_term)) {
        $directory = 'tbvidsdat/';
        foreach (glob($directory . "*.json") as $file) {
            $video_data = json_decode(file_get_contents($file), true);
            if (strpos(strtolower($video_data['video_title']), strtolower($search_term)) !== false || 
                strpos(strtolower($video_data['video_description']), strtolower($search_term)) !== false) {
                $search_results_videos[] = $video_data;
            }
        }
    }

    // Search for profiles in the 'tbusersdat' directory
    $directory_profiles = 'tbusersdat/';
    foreach (glob($directory_profiles . "*.json") as $file) {
        $user_data = json_decode(file_get_contents($file), true);
        if (strpos(strtolower($user_data['username']), strtolower($search_term)) !== false) {
            $search_results_profiles[] = $user_data;
        }
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
    <title>Search - KazVooru</title>
<style>
    /* General body styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #2c2f37; /* Dark grey background */
        color: #f4f4f4; /* Light text color */
        margin: 0;
        padding: 0;
        text-align: left; /* Align all text to the left */
    }

    /* Header */
    header {
        background-color: #1f2329; /* Darker grey for header */
        color: #ffffff;
        padding: 10px;
        position: relative;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
        text-align: left; /* Ensure header text is left-aligned */
        display: inline-block;
    }

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
        object-fit: cover;
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

    /* Profile Item */
    .profile-item {
        background-color: #3a3f47;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-item img {
        width: 100px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }

    .profile-item h3 {
        font-size: 18px;
        margin: 10px 0;
        color: #ffffff;
    }

    /* Search Container */
    .search-container {
        padding: 20px;
    }

    .search-results {
        margin-top: 20px;
    }

</style>
</head>
<body>

<div class="search-container">
    <h1>Search Videos and Channels</h1>

    <form method="POST" action="search.php">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Search..." required>
        <button type="submit">Search</button>
    </form>

    <div class="search-results">
        <h2>Video Results</h2>
        <?php if (count($search_results_videos) > 0): ?>
            <?php foreach ($search_results_videos as $video): ?>
                <div class="video-item">
                    <a href="view_video.php?video=<?php echo urlencode(basename($video['video_title']) . '.json'); ?>">
                        <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" alt="Thumbnail">
                        <h3><?php echo htmlspecialchars($video['video_title']); ?></h3>
                    </a>
                    <p><?php echo htmlspecialchars($video['video_description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No videos found matching your search.</p>
        <?php endif; ?>

        <h2>Channel Results</h2>
        <?php if (count($search_results_profiles) > 0): ?>
            <?php foreach ($search_results_profiles as $profile): ?>
                <div class="profile-item">
                    <a href="public.php?user=<?php echo urlencode(basename($profile['username']) . '.json'); ?>">
                        <h3><?php echo htmlspecialchars($profile['username']); ?></h3>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No profiles found matching your search.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
