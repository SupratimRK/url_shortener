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
            <input type="submit" value="Shorten">
        </form>

        <?php if (isset($_POST['url'])): ?>
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

            $url = $_POST['url'];
            $customCode = isset($_POST['custom_code']) ? $_POST['custom_code'] : null;
            $shortened = shortenURL($url, $customCode);
            if ($shortened) {
                $shortened_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?code=$shortened";
            } else {
                $shortened_url = "Failed to shorten URL.";
            }
            ?>
            <div class="shortened-url">
                <p>Shortened URL</p><div class="xc"></div><p> <a href="<?php echo $shortened_url; ?>"><?php echo $shortened_url; ?></a> 
                <button class="btn" onclick="copyToClipboard('<?php echo $shortened_url; ?>')">Copy</button></p>
            </div>
            <p id="nm">Crafted with 💛 by Supratim</p>
        <?php endif; ?>
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
