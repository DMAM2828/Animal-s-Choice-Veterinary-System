<?php
require_once('../config.php');

$firstname = $lastname = $username = $password = $type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $type = 2;
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user data into the database
    $sql = "INSERT INTO users (firstname, lastname, username, password, type, date_added) VALUES ('$firstname', '$lastname', '$username', '$hashed_password', '$type', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect to login page after successful signup
        header("Location: login.php");
        exit();
    } else {
        // Display error message if signup fails
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        /* Your existing CSS styles */
        body {
            font-size: 16px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .signup-container {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        input[type="submit"]:active {
            background-color: #3e8e41;
        }

        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Signup Form</h2>
        <form id="signupForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <label for="firstname">First Name:</label><br>
            <input type="text" id="firstname" name="firstname" required><span id="firstnameError" class="error"></span><br>

            <label for="lastname">Last Name:</label><br>
            <input type="text" id="lastname" name="lastname" required><span id="lastnameError" class="error"></span><br>

            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><span id="usernameError" class="error"></span><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><span id="passwordError" class="error"></span><br>

            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><span id="confirmPasswordError" class="error"></span><br><br>

            <input type="submit" value="Signup">
        </form>
    </div>

    <script>
        function validateForm() {
            var firstName = document.getElementById("firstname").value;
            var lastName = document.getElementById("lastname").value;
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var firstNameRegex = /^[a-zA-Z]{2,}$/;
            var lastNameRegex = /^[a-zA-Z]{2,}$/;
            var usernameRegex = /^[a-zA-Z]{5,}$/;
            var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

            var isValid = true;

            if (!firstNameRegex.test(firstName)) { 
                document.getElementById("firstnameError").innerText = "First name must contain only letters and have a minimum length of 2.";
                isValid = false;
            } else { 
                document.getElementById("firstnameError").innerText = "";
            }

            if (!lastNameRegex.test(lastName)) {
                document.getElementById("lastnameError").innerText = "Last name must contain only letters and have a minimum length of 2.";
                isValid = false;
            } else {
                document.getElementById("lastnameError").innerText = "";
            }

            if (!usernameRegex.test(username)) {
                document.getElementById("usernameError").innerText = "Username must contain only letters and have a minimum length of 5.";
                isValid = false;
            } else {
                document.getElementById("usernameError").innerText = "";
            }

            if (!passwordRegex.test(password)) {
                document.getElementById("passwordError").innerText = "Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 digit, and have a minimum length of 8.";
                isValid = false;
            } else {
                document.getElementById("passwordError").innerText = "";
            }

            if (password !== confirmPassword) {
                document.getElementById("confirmPasswordError").innerText = "Passwords do not match.";
                isValid = false;
            } else {
                document.getElementById("confirmPasswordError").innerText = "";
            }

            return isValid;
        }
    </script>
</body>
</html>
