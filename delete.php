<?php
session_start();

// Ensure that the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check if the video title is passed and sanitize it
if (isset($_POST['video_title'])) {
    // The video file to delete
    $video_title = basename($_POST['video_title']); // Use basename to avoid directory traversal issues
    $video_path = 'tbvidsdat/' . $video_title . '.json'; // Path to the JSON file

    // Check if the file exists before attempting to delete
    if (file_exists($video_path)) {
        if (unlink($video_path)) {
            echo "Video deleted successfully!";
            // Redirect back to profile page after deletion
            header('Location: profile.php');
            exit();
        } else {
            echo "Error deleting the video.";
        }
    } else {
        echo "Video file does not exist.";
    }
} else {
    echo "No video selected for deletion.";
}
?>