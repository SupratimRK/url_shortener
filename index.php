<?php
// MySQL connection parameters
$servername = "localhost";
$username = "root"; // Update with your MySQL username
$password = ""; // Update with your MySQL password
$database = "url_shortener";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate random short code
function generateShortCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $shortCode = '';
    for ($i = 0; $i < $length; $i++) {
        $shortCode .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $shortCode;
}

// Function to check if custom short code is available
function isShortCodeAvailable($shortCode) {
    global $conn;
    $sql = "SELECT * FROM urls WHERE short_code = '$shortCode'";
    $result = $conn->query($sql);
    return ($result->num_rows === 0);
}

// Function to shorten URL
function shortenURL($url, $customCode = null) {
    global $conn;

    if ($customCode && isShortCodeAvailable($customCode)) {
        $shortCode = $customCode;
    } else {
        $shortCode = generateShortCode();
    }

    $sql = "INSERT INTO urls (original_url, short_code) VALUES ('$url', '$shortCode')";
    if ($conn->query($sql) === TRUE) {
        return $shortCode;
    } else {
        return null;
    }
}

// If form is submitted to shorten URL
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $customCode = isset($_POST['custom_code']) ? $_POST['custom_code'] : null;
    $shortened = shortenURL($url, $customCode);
    if ($shortened) {
        $shortened_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?code=$shortened";
    } else {
        $shortened_url = "Failed to shorten URL.";
    }
}

// If short code is provided to expand URL
if (isset($_GET['code'])) {
    $shortCode = $_GET['code'];
    $sql = "SELECT original_url FROM urls WHERE short_code = '$shortCode'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        header("Location: " . $row['original_url']);
        exit();
    } else {
        echo "URL not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>URL Shortener</h1>
        <form action="" method="post">
            <input type="text" name="url" placeholder="Enter URL to shorten" required>
            <br>
            <input type="text" name="custom_code" placeholder="Custom short code (optional)">
            <button class="ui-btn" type="submit">
                <span>Shorten</span>
            </button>
        </form>

        <?php if (isset($shortened_url)): ?>
        <div class="shortened-url">
            <p>Shortened URL:</p>
            <div class="xc"></div>
            <p><a href="<?php echo $shortened_url; ?>"><?php echo $shortened_url; ?></a>
            <button class="btn" onclick="copyToClipboard('<?php echo $shortened_url; ?>')">Copy</button></p>
        </div>
        <?php endif; ?>
        <p id="nm">Crafted with 💛 by Supratim</p>
    </div>

    <script>
    function copyToClipboard(text) {
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        alert("Copied to clipboard!");
    }
    </script>
</body>
</html>

<style>
:root {
    --primary-color: #FFD700;
    --secondary-color: #fafafa;
    --colc: #000;
    --cold: #000;
}

body {
    background-image: radial-gradient(circle at center center, transparent 0%,rgb(0,0,0) 99%),repeating-linear-gradient(0deg, rgba(163, 163, 163,0.2) 0px, rgba(163, 163, 163,0.2) 1px,transparent 1px, transparent 6px),repeating-linear-gradient(90deg, rgba(163, 163, 163,0.2) 0px, rgba(163, 163, 163,0.2) 1px,transparent 1px, transparent 6px),linear-gradient(90deg, rgb(0,0,0),rgb(0,0,0));
    color: var(--secondary-color);
    font-weight: 600;
    font-family: Menlo, Roboto Mono, monospace;
}

.container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
    color: var(--primary-color);
}

form {
    margin-bottom: 20px;
}

input[type="text"],
input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid var(--secondary-color);
    background-color: transparent;
    color: var(--secondary-color);
    border-radius: 5px;
    outline: none;
    box-sizing: border-box;
    align-items: center;
    font-weight: 600;
    font-family: Menlo, Roboto Mono, monospace;
}

input[type="text"]:focus,
input[type="submit"]:focus,
button:focus {
    border: 1px solid var(--secondary-color);
}

input[type="submit"],
button {
    cursor: pointer;
}

.shortened-url {
    padding: 10px;
    border-radius: 5px;
    align-items: center;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--secondary-color);
}

.shortened-url p {
    margin-bottom: 10px;
    text-align: center;
    letter-spacing: 0.1rem;
}

.xc {
    border: 1px dashed var(--secondary-color);
    padding: 0 50% 0 50%;
}

.shortened-url a {
    color: var(--primary-color);
    text-decoration: none;
    margin: 10px;
}

.shortened-url button {
    align-items: center;
    background-color: var(--secondary-color);
    color: #000;
    border: none;
    padding: 10px 10px;
    margin: 10px;
    border-radius: 5px;
    cursor: pointer;
    border: 1px solid var(--secondary-color);
}

.btn:hover {
    color: var(--primary-color);
    background-color: transparent;
    border: 1px solid var(--secondary-color);
    transition: 0.7s;
    letter-spacing: .1rem;
}

.btn {
    width: 80%;
    padding: 10px;
    margin-bottom: 10px;
    margin-top: 10;
    border: 1px solid var(--secondary-color);
    border-radius: 5px;
    outline: none;
    box-sizing: border-box;
    align-items: center;
    font-weight: 800;
    font-family: Menlo, Roboto Mono, monospace;
    font-size: 16px;
    letter-spacing: 0.2rem;
}

input[type="submit"]:hover {
    color: var(--primary-color);
    background-color: transparent;
    transition: 0.7s;
}

#nm {
    text-align: center;
}

.ui-btn {
    --btn-default-bg: transparent;
    --btn-padding: 10px;
    --btn-hover-bg: transparent;
    --btn-transition: 1s;
    --btn-letter-spacing: .1rem;
    --btn-animation-duration: 1.2s;
    --btn-shadow-color: rgba(0, 0, 0, 0.137);
    --btn-shadow: 0 2px 10px 0 var(--btn-shadow-color);
    --hover-btn-color: #FFD700;
    --default-btn-color: #fafafa;
    --font-size: 16px;
    --font-weight: 600;
    --font-family: Menlo, Roboto Mono, monospace;
}

.ui-btn {
    width: 100%;
    box-sizing: border-box;
    padding: var(--btn-padding);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--default-btn-color);
    font: var(--font-weight) var(--font-size) var(--font-family);
    background: var(--btn-default-bg);
    border: none;
    cursor: pointer;
    transition: var(--btn-transition);
    overflow: hidden;
    box-shadow: var(--btn-shadow);
    border: 1px solid var(--secondary-color);
    border-radius: 5px;
}

.ui-btn span {
    letter-spacing: var(--btn-letter-spacing);
    transition: var(--btn-transition);
    box-sizing: border-box;
    position: relative;
    background: inherit;
}

.ui-btn span::before {
    box-sizing: border-box;
    position: absolute;
    content: "";
    background: inherit;
}

.ui-btn:hover, .ui-btn:focus {
    background: var(--btn-hover-bg);
}

.ui-btn:hover span, .ui-btn:focus span {
    color: var(--hover-btn-color);
}

.ui-btn:hover span::before, .ui-btn:focus span::before {
    animation: chitchat linear both var(--btn-animation-duration);
}

@keyframes chitchat {
    0% {
        content: "#";
    }
    5% {
        content: ".";
    }
    10% {
        content: "^{";
    }
    15% {
        content: "-!";
    }
    20% {
        content: "#$_";
    }
    25% {
        content: "№:0";
    }
    30% {
        content: "#{+.";
    }
    35% {
        content: "@}-?";
    }
    40% {
        content: "?{4@%";
    }
    45% {
        content: "=.,^!";
    }
    50% {
        content: "?2@%";
    }
    55% {
        content: "\;1}]";
    }
    60% {
        content: "?{%:%";
        right: 0;
    }
    65% {
        content: "|{f[4";
        right: 0;
    }
    70% {
        content: "{4%0%";
        right: 0;
    }
    75% {
        content: "'1_0<";
        right: 0;
    }
    80% {
        content: "{0%";
        right: 0;
    }
    85% {
        content: "]>'";
        right: 0;
    }
    90% {
        content: "4";
        right: 0;
    }
    95% {
        content: "2";
        right: 0;
    }
    100% {
        content: "";
        right: 0;
    }
}
</style>
