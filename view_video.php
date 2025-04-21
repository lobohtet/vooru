	<?php session_start(); ?>

<!-- Nav bar visible only for logged-in users -->
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
<?php
if (isset($_GET['video'])) {
    $video_file = 'tbvidsdat/' . basename($_GET['video']);
    if (file_exists($video_file)) {
        $video = json_decode(file_get_contents($video_file), true);
        
        // Increment view count and save it back to the file
        $video['views'] = isset($video['views']) ? $video['views'] + 1 : 1;
        file_put_contents($video_file, json_encode($video, JSON_PRETTY_PRINT));

        // Display video details
        echo "<div class='video-detail'>";
        echo "<video controls><source src='" . htmlspecialchars($video['video_url']) . "' type='video/mp4'></video>";
        echo "<h1>" . htmlspecialchars($video['video_title']) . "</h1>";
        echo "<p>" . htmlspecialchars($video['video_description']) . "</p>";
        echo "<p>Uploaded by: <a href='public.php?user=" . urlencode($video['username']) . ".json'>" . htmlspecialchars($video['username']) . "</a> on " . htmlspecialchars($video['upload_date']) . "</p>";
        echo "<p>Views: " . htmlspecialchars($video['views']) . "</p>";
        echo "</div>";

     // Comment form
        echo "<form method='POST' action='comment_submit.php'>";
        echo "<textarea name='comment_text' placeholder='Write a comment' maxlength='128' required></textarea><br>";
        echo "<input type='hidden' name='video' value='" . htmlspecialchars($_GET['video']) . "'>";
        echo "<input type='submit' value='Submit'>";
        echo "</form>";

        // Display Comments
        echo "<div class='comments-section'>";
        echo "<h2>Comments</h2>";
        
        // Check if there are comments and display them
        if (isset($video['comments']) && count($video['comments']) > 0) {
            // Show comments, most recent on top
            foreach (array_reverse($video['comments']) as $comment) {
                echo "<div class='comment'>";
                echo "<p><strong>" . htmlspecialchars($comment['cmter']) . "</strong> commented:</p>";
                echo "<p>" . htmlspecialchars($comment['cmt']) . "</p>";
                echo "<hr>";
                echo "</div>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }

   
        echo "</div>";
    } else {
        echo "<p>Video not found.</p>";
    }
} else {
    echo "<p>No video specified.</p>";
}
?>

	<style>
    /* General body styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #2c2f37; /* Dark grey background */
        color: #f4f4f4; /* Light text color */
        margin: 0;
        padding: 0;
        text-align: left;
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
        background-color: #32a4ff; /* Mild blue background for buttons */
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
        width: auto;
        height: auto; /* Adjust to fit container */
        border-radius: 5px;
    }

    .video-content h3 {
        color: #ffffff;
        font-size: 18px;
        margin: 10px 0;
        text-align: left;
    }

    .video-content p {
        color: #cccccc;
        font-size: 14px;
        text-align: left;
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
        text-align: left;
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

    /* Custom styles for darker color palette */
    .video-item {
        background-color: #3a3f47; /* Slightly darker grey for video items */
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .video-item:hover {
        background-color: #4f535d; /* Dark grey when hovered */
    }

    .subscribe-button {
        background-color: #76c7c0; /* 32a4ff color for subscribe buttons */
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .subscribe-button:hover {
        background-color: #56a29a; /* Slightly darker 32a4ff color */
    }

    /* Optional: Style scrollbar for navigation */
    nav::-webkit-scrollbar {
        height: 6px;
    }

    nav::-webkit-scrollbar-thumb {
        background-color: #555;
        border-radius: 3px;
    }

    nav::-webkit-scrollbar-thumb:hover {
        background-color: #76c7c0;
    }
</style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">