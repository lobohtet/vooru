<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    // Check if the user is logged in
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $video_url = $_POST['video_url'];
        $video_title = $_POST['video_title'];
        $video_description = $_POST['video_description'];
        $thumbnail_url = $_POST['thumbnail_url'];
        $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : false;
        $no_copyright = isset($_POST['no_copyright']) ? $_POST['no_copyright'] : false;

        // Server-side validation
        if (!$captcha || !$no_copyright) {
            echo "Please verify you're not a robot and confirm no copyright issues.";
            exit;
        }

        if (!preg_match('/\.(mp4|avi|mov|wmv|mkv|flv|webm)$/i', $video_url)) {
            echo "Invalid video URL format. Must end with a valid video extension.";
            exit;
        }

        if (!preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $thumbnail_url)) {
            echo "Invalid thumbnail URL format. Must end with a valid image extension.";
            exit;
        }

        if (empty($video_title) || empty($video_description)) {
            echo "Title and description cannot be empty.";
            exit;
        }

        if (strlen($video_title) > 96) {
            echo "Title exceeds maximum character limit of 96.";
            exit;
        }

        if (strlen($video_description) > 2000) {
            echo "Description exceeds maximum character limit of 2000.";
            exit;
        }

        // Create video data array
        $video_data = [
            'username' => $username,
            'video_url' => $video_url,
            'video_title' => $video_title,
            'video_description' => $video_description,
            'thumbnail_url' => $thumbnail_url,
            'upload_date' => date('Y-m-d'),
            'views' => 0  // Initialize views count
        ];

        // Save video data to individual JSON file based on the video title
        $video_file = 'tbvidsdat/' . basename($video_title) . '.json'; // Use video title as file name
        file_put_contents($video_file, json_encode($video_data, JSON_PRETTY_PRINT));

        // Redirect to home page after successful upload
        header('Location: index.php');
        exit;
    } else {
        echo "You must be logged in to upload a video.";
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
        </ul>
    </nav>
<?php endif; ?>

<!-- Upload Form -->
<form method="POST" id="uploadForm">
    <b><p>You can get your Video/Thumb URLs using File to Link services like eg. 
        <a href="https://catbox.moe" target="_blank">Catbox.Moe</a> or other link hostings.
    </p></b>
    
    <label for="video_url">Video URL:</label>
    <input type="text" name="video_url" id="video_url" placeholder="e.g. example.com/video.mp4" required><br>

    <label for="video_title">Video Title:</label>
    <input type="text" name="video_title" id="video_title" maxlength="96" required><br>

    <label for="video_description">Short Description:</label>
    <textarea name="video_description" id="video_description" maxlength="2000" required></textarea><br>

    <label for="thumbnail_url">Thumbnail URL:</label>
    <input type="text" name="thumbnail_url" id="thumbnail_url" placeholder="e.g. example.com/thumbnail.jpg" required><br>

    <label for="captcha">I'm not a robot:</label>
    <input type="checkbox" name="captcha" id="captcha" required><br>

    <label for="no_copyright">My content is safe for +13 audience:</label>
    <input type="checkbox" name="no_copyright" id="no_copyright" required><br>

    <button type="submit" name="upload" id="uploadBtn" disabled>Upload Video</button>
</form>

<script>
    const videoUrlInput = document.getElementById('video_url');
    const thumbnailUrlInput = document.getElementById('thumbnail_url');
    const videoTitleInput = document.getElementById('video_title');
    const videoDescriptionInput = document.getElementById('video_description');
    const captchaCheckbox = document.getElementById('captcha');
    const noCopyrightCheckbox = document.getElementById('no_copyright');
    const uploadBtn = document.getElementById('uploadBtn');

    function validateForm() {
        const videoUrlValid = /\.(mp4|avi|mov|wmv|mkv|flv|webm)$/i.test(videoUrlInput.value);
        const thumbnailUrlValid = /\.(jpg|jpeg|png|gif|bmp)$/i.test(thumbnailUrlInput.value);
        const titleNotEmpty = videoTitleInput.value.trim().length > 0 && videoTitleInput.value.length <= 96;
        const descriptionNotEmpty = videoDescriptionInput.value.trim().length > 0 && videoDescriptionInput.value.length <= 2000;
        const captchaChecked = captchaCheckbox.checked;
        const noCopyrightChecked = noCopyrightCheckbox.checked;

        // Enable button only if all validations pass
        uploadBtn.disabled = !(videoUrlValid && thumbnailUrlValid && titleNotEmpty && descriptionNotEmpty && captchaChecked && noCopyrightChecked);
    }

    // Add event listeners for real-time validation
    [videoUrlInput, thumbnailUrlInput, videoTitleInput, videoDescriptionInput, captchaCheckbox, noCopyrightCheckbox].forEach(element => {
        element.addEventListener('input', validateForm);
        element.addEventListener('change', validateForm);
    });
</script>

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
        width: 100px;
        height: 100px;
        border-radius: 5px;
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
        display: flex;
        justify-content: center;
    }

    nav ul li {
        margin: 0 15px;
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

    /* Fix for footer */
    footer {
        z-index: 999;
    }
</style>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">