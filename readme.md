Certainly! Here is a detailed `README.md` file for your URL shortener project:

```markdown
# URL Shortener

A simple URL shortener application built with PHP and MySQL. This application allows users to shorten URLs and retrieve the original URLs using short codes.

## Features

- Shorten long URLs into short codes
- Custom short codes
- Redirect short codes to original URLs
- Check if a URL has already been shortened

## Prerequisites

- PHP (>= 7.0)
- MySQL or MariaDB
- Web server (e.g., Apache)
- Composer (for dependency management)

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-username/url-shortener.git
cd url-shortener
```

### Step 2: Set Up the Database

1. **Create the database:**

    ```sql
    CREATE DATABASE url_shortener;
    ```

2. **Create the table:**

    ```sql
    USE url_shortener;

    CREATE TABLE urls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_url VARCHAR(255) NOT NULL,
        short_code VARCHAR(10) NOT NULL UNIQUE
    );
    ```

3. **(Optional) Insert sample data:**

    ```sql
    INSERT INTO urls (original_url, short_code) VALUES
    ('https://www.example.com', 'exmpl'),
    ('https://www.anotherexample.com', 'anothr');
    ```

### Step 3: Configure the Application

1. **Create a `config.php` file in the root directory:**

    ```php
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
    ?>
    ```

2. **Include `config.php` in your `index.php` file:**

    ```php
    <?php
    include 'config.php';
    // ... rest of your code
    ?>
    ```

### Step 4: Set Up the Web Server

1. **Apache Configuration:**

    Ensure your Apache server is configured to serve the project directory. You can create a virtual host for the project in your Apache configuration:

    ```apache
    <VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot "path_to_your_project_directory"
        ServerName url-shortener.local

        <Directory "path_to_your_project_directory">
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>
    ```

    Don't forget to add the `url-shortener.local` entry in your hosts file:

    ```plaintext
    127.0.0.1 url-shortener.local
    ```

2. **Restart Apache:**

    ```bash
    sudo service apache2 restart
    ```

### Step 5: Access the Application

Open your web browser and navigate to `http://url-shortener.local` to start using the URL shortener.

## Usage

1. **Shorten a URL:**

    - Enter the URL you want to shorten in the input field.
    - (Optional) Enter a custom short code.
    - Click the "Shorten" button.

2. **Retrieve the Original URL:**

    - Enter the short code in the browser URL as a query parameter (e.g., `http://url-shortener.local?x=shortcode`).
    - The application will redirect to the original URL if the short code exists.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an issue on GitHub.

## Contact

For any inquiries or issues, please contact [your-email@example.com](mailto:your-email@example.com).

```

Make sure to replace placeholders like `your-username`, `path_to_your_project_directory`, and `your-email@example.com` with the actual values relevant to your project. This `README.md` provides detailed installation instructions, from setting up the database to configuring the web server and using the application.