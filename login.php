<h2> Login or Register to KazVooru </h2>

<?php
session_start();

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Define the file path for the user's JSON data
    $userFile = 'tbusersdat/' . $username . '.json';

    // Check if the user file exists
    if (file_exists($userFile)) {
        // Get the user data from the file
        $userData = json_decode(file_get_contents($userFile), true);

        // Check if the password matches
        if ($userData['password'] == $password) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            echo "Invalid login credentials!";
        }
    } else {
        echo "User does not exist!";
    }
}

// Handle registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Define the file path for the user's JSON data
    $userFile = 'tbusersdat/' . $username . '.json';

    // Check if the user file already exists
    if (file_exists($userFile)) {
        echo "Username already exists!";
        exit;
    }

    // Create user data array
    $userData = [
        'username' => $username,
        'password' => $password,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Save the user data to the file
    if (file_put_contents($userFile, json_encode($userData, JSON_PRETTY_PRINT))) {
        echo "User registered successfully! You can now log in.";
    } else {
        echo "Error registering user.";
    }
}
?>


<!-- HTML for Login/Registration -->

<form method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br>

    <button type="submit" name="login">Log In</button>
    <button type="submit" name="register">Register</button>
</form>

<style>
    /* General body styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #12151a; /* Deep dark background */
        color: #d9e6f2; /* Softer light text */
        margin: 0;
        padding: 0;
    }

    /* Header */
    header {
        background-color: #0e1624; /* Darker header background */
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
        background-color: #0e1624; /* Dark footer */
        color: #d9e6f2;
        padding: 10px;
        text-align: center;
        position: fixed;
        bottom: 0;
        width: 100%;
        z-index: 999;
    }

    /* Button Styling */
    button {
        background-color: #32a4ff; /* Mild blue for buttons */
        color: #fff;
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    button:hover {
        background-color: #2486cc; /* Darker blue when hovered */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Subtle shadow on hover */
    }

    /* Video content styling */
    .video-content {
        background-color: #1b2837; /* Slightly lighter grey */
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
        color: #96a9bc; /* Muted text color for descriptions */
        font-size: 14px;
    }

    /* Form input fields */
    input[type="text"], input[type="password"], textarea {
        background-color: #1e252e;
        color: #d9e6f2;
        border: 1px solid #32a4ff; /* Blue border */
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
        background-color: #1b2837;
        padding: 20px;
        border-radius: 10px;
    }

    .video-detail video {
        width: 80%;
        max-width: 700px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Subtle shadow */
    }

    .video-detail h1 {
        color: #ffffff;
    }

    .video-detail p {
        color: #96a9bc;
    }

    /* Navbar styles */
    nav {
        background-color: #0d1218;
        padding: 10px 0;
        text-align: center;
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
        color: #d9e6f2;
        font-size: 18px;
        font-weight: bold;
        padding: 5px 10px;
        transition: color 0.3s ease, background-color 0.3s ease;
    }

    nav ul li a:hover {
        color: #32a4ff; /* Highlight link color */
        background-color: #1b2837; /* Subtle hover background */
        border-radius: 5px;
    }

    /* Center the form elements */
    form {
        max-width: 500px;
        margin: 30px auto;
        padding: 20px;
        background-color: #1b2837;
        border-radius: 10px;
    }

    form label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #d9e6f2;
    }

    form input[type="text"], form input[type="password"], form textarea {
        margin-bottom: 15px;
    }
</style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    	