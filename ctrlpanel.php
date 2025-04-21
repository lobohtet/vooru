<?php
session_start();

// Authenticate the admin login
$admFile = 'adm/adm.json';
if (!file_exists($admFile)) {
    die("Admin configuration missing.");
}

$admData = json_decode(file_get_contents($admFile), true);

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $id = $_POST['id'] ?? '';
    $opt = $_POST['opt'] ?? '';

    if ($id === $admData['id'] && $opt === $admData['opt']) {
        $_SESSION['is_admin'] = true;
        header('Location: ctrlpanel.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}

// Handle session logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ctrlpanel.php');
    exit;
}

// Require login for accessing the control panel
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    ?>
    <h1>Control Panel Login</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label for="id">ID:</label>
        <input type="text" name="id" id="id" required><br>
        <label for="opt">OPT:</label>
        <input type="password" name="opt" id="opt" required><br>
        <button type="submit" name="login">Login</button>
    </form>
    <?php
    exit;
}

// Inodes capacity calculation
$totalFiles = count(glob('tbvidsdat/*')) + count(glob('tbusersdat/*'));
$inodeCapacity = $totalFiles . " / 32768";

// Function to fetch all videos
function getAllVideos() {
    $videos = [];
    foreach (glob('tbvidsdat/*.json') as $file) {
        $video = json_decode(file_get_contents($file), true);
        $video['filename'] = basename($file);
        $videos[] = $video;
    }
    return $videos;
}

// Function to fetch all users
function getAllUsers() {
    $users = [];
    foreach (glob('tbusersdat/*.json') as $file) {
        $user = json_decode(file_get_contents($file), true);
        $user['filename'] = basename($file);
        $users[] = $user;
    }
    return $users;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #2c2f37; /* Dark grey background */
        color: #f4f4f4; /* Light text color */
        padding: 20px;
    }

    h1, h2 {
        color: #ffffff; /* White for headings */
    }

    button {
        padding: 10px 20px;
        margin: 5px 0;
        background-color: #007bff; /* Blue background for buttons */
        color: #fff; /* White text for buttons */
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3; /* Darker blue when hovered */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #444; /* Dark background for table headers */
    }

    a {
        color: #1e90ff; /* Blue links */
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
    <h1>Control Panel</h1>
    <h2>Inodes Capacity</h2>
    <p><?php echo $inodeCapacity; ?></p>

    <button onclick="window.location.href='?view=videos'">Videos</button>
    <button onclick="window.location.href='?view=users'">Users</button>
    <button onclick="window.location.href='?logout=true'">End Session</button>

    <?php
    if (isset($_GET['view']) && $_GET['view'] === 'videos') {
        echo '<h2>Video Management</h2>';
        echo '<table>';
        echo '<tr><th>Video Name</th><th>Date</th><th>Watch</th><th>Delete</th></tr>';
        foreach (getAllVideos() as $video) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($video['video_title']) . '</td>';
            echo '<td>' . htmlspecialchars($video['upload_date']) . '</td>';
            echo '<td><a href="view_video.php?video=' . urlencode($video['filename']) . '">Watch</a></td>';
            echo '<td><a href="?delete_video=' . urlencode($video['filename']) . '" onclick="return confirm(\'Are you sure you want to delete this video?\')">Delete</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    if (isset($_GET['view']) && $_GET['view'] === 'users') {
        echo '<h2>User Management</h2>';
        echo '<table>';
        echo '<tr><th>Username</th><th>Delete</th><th>Delete with Videos</th></tr>';
        foreach (getAllUsers() as $user) {
            echo '<tr>';
            echo '<td><a href="public.php?user=' . urlencode($user['filename']) . '">' . htmlspecialchars($user['username']) . '</a></td>';
            echo '<td><a href="?delete_user=' . urlencode($user['filename']) . '" onclick="return confirm(\'Are you sure you want to delete this user?\')">Delete</a></td>';
            echo '<td><a href="?delete_user_videos=' . urlencode($user['filename']) . '" onclick="return confirm(\'Are you sure you want to delete this user and their videos?\')">Delete with Videos</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    // Handle delete actions
    if (isset($_GET['delete_video'])) {
        $file = 'tbvidsdat/' . basename($_GET['delete_video']);
        if (file_exists($file)) unlink($file);
        header('Location: ctrlpanel.php?view=videos');
        exit;
    }

    if (isset($_GET['delete_user'])) {
        $file = 'tbusersdat/' . basename($_GET['delete_user']);
        if (file_exists($file)) unlink($file);
        header('Location: ctrlpanel.php?view=users');
        exit;
    }

    if (isset($_GET['delete_user_videos'])) {
        $userFile = 'tbusersdat/' . basename($_GET['delete_user_videos']);
        if (file_exists($userFile)) {
            $user = json_decode(file_get_contents($userFile), true);
            $username = $user['username'];
            foreach (glob('tbvidsdat/*.json') as $file) {
                $video = json_decode(file_get_contents($file), true);
                if ($video['username'] === $username) {
                    unlink($file);
                }
            }
            unlink($userFile);
        }
        header('Location: ctrlpanel.php?view=users');
        exit;
    }
    ?>
</body>
</html>