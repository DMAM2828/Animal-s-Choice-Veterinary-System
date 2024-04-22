<?php
$host = "localhost";
$dbname = "ovas_db";
$username = "root";
$password = "";

try {
    // Establish a database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Check if the keys exist in the $_POST array and if values are not empty
        if (isset($_POST['firstname'], $_POST['lastname'], $_POST['username'], $_POST['password']) &&
            !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['username']) && !empty($_POST['password'])) {
            
            // Retrieve user input from the form
            $inputFirstname = $_POST["firstname"];
            $inputLastname = $_POST["lastname"];
            $inputUsername = $_POST["username"];
            $inputPassword = password_hash($_POST["password"], PASSWORD_BCRYPT);

            // Insert user information into the database
            $stmt = $pdo->prepare("INSERT INTO doctors (firstname, lastname, username, password) VALUES (:firstname, :lastname, :username, :password)");
            $stmt->bindParam(':firstname', $inputFirstname);
            $stmt->bindParam(':lastname', $inputLastname);
            $stmt->bindParam(':username', $inputUsername);
            $stmt->bindParam(':password', $inputPassword);
            $stmt->execute();

            header("Location: doctorlogin.php");
        } else {
            echo "Firstname, Lastname, Username, and Password cannot be null or empty";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
