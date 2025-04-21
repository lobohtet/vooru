<?php
session_start();


// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to comment.";
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $comment_text = trim($_POST['comment_text']);
    $video = basename($_POST['video']); // Get the video filename

    if (strlen($comment_text) > 128) {
        echo "Comment is too long. Maximum 128 characters allowed.";
        exit;
    }

    // Prepare comment data
    $comment_data = [
        'cmter' => $_SESSION['username'],
        'cmt' => $comment_text,
    ];

    // Define the path to the video file
    $video_file = 'tbvidsdat/' . $video;

    // Load the existing video data
    if (file_exists($video_file)) {
        $video = json_decode(file_get_contents($video_file), true);

        // Add the new comment to the video
        if (!isset($video['comments'])) {
            $video['comments'] = []; // Initialize the comments array if it doesn't exist
        }

        // Add the comment to the beginning of the comments array
        array_unshift($video['comments'], $comment_data);

        // Save the updated video data back to the JSON file
        file_put_contents($video_file, json_encode($video, JSON_PRETTY_PRINT));

        // Redirect back to the video page
        header("Location: view_video.php?video=" . urlencode($video['video_title']) . ".json");
        exit;
    } else {
        echo "Video not found.";
    }
}