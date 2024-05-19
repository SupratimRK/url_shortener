<?php
// MySQL connection parameters
$servername = "localhost";
$username = "root";
$password = "";
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

    // Add "https://" prefix if missing
    if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
        $url = 'https://' . $url;
    }

    // Remove protocol (http:// or https://) from the URL
    $urlWithoutProtocol = preg_replace('#^https?://#', '', $url);

    // Check if the URL without protocol already exists in the database
    $sql = "SELECT short_code FROM urls WHERE REPLACE(REPLACE(original_url, 'https://', ''), 'http://', '') = '$urlWithoutProtocol'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // If URL already exists, return the existing short code
        $row = $result->fetch_assoc();
        return $row['short_code'];
    } else {
        // If URL doesn't exist, generate a new short code
        do {
            $shortCode = $customCode ? $customCode : generateShortCode();
        } while (!$customCode && !isShortCodeAvailable($shortCode));

        // Insert the new URL and short code into the database
        $sql = "INSERT INTO urls (original_url, short_code) VALUES ('$url', '$shortCode')";
        if ($conn->query($sql) === TRUE) {
            return $shortCode;
        } else {
            return null;
        }
    }
}


// If form is submitted to shorten URL
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $customCode = isset($_POST['custom_code']) ? $_POST['custom_code'] : null;
    $shortened = shortenURL($url, $customCode);
    if ($shortened) {
        $shortened_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $shortened_url = preg_replace('/\?.*/', '', $shortened_url); // Remove existing query parameters
        $shortened_url .= "?x=$shortened";
    } else {
        $shortened_url = "Failed to shorten URL.";
    }
}

// If short code is provided to expand URL
if (isset($_GET['x'])) {
    $shortCode = $_GET['x'];
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
            <p class="xrl">Shortened URL</p>
            <div class="xc"></div>
            <p class="url"><a href="<?php echo $shortened_url; ?>"><?php echo str_replace('http://', '', $shortened_url); ?></a>
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
