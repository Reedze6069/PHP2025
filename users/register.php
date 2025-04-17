<?php
// register.php - User Registration script

session_start();              // Start session (not strictly needed here unless we want to track login state after registration)
require_once __DIR__ . '/../includes/db_connect.php';
     // Include the database connection

// Initialize variables for form data and messages
$name = $email = $phone = $address = "";
$errors = array();
$success = "";

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form input
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $phone    = trim($_POST["phone"]);
    $address  = trim($_POST["address"]);

    // 1. Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($address)) {
        $errors[] = "All fields are required.";
    }
    // 2. Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }
    // 3. (Optional) Validate phone number (digits only in this example)
    elseif (!ctype_digit($phone)) {
        $errors[] = "Phone number should contain only digits.";
    }

    // 4. If no validation errors so far, check if the email is already registered
    if (empty($errors)) {
        $sql = "SELECT id FROM customers WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "An account with this email already exists.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: Could not prepare query.";
        }
    }

    // 5. If still no errors, insert the new user into the database
    if (empty($errors)) {
        // Hash the password before storing (using a strong one-way hashing algorithm)&#8203;:contentReference[oaicite:9]{index=9}
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Prepare insert query to save the new user securely
        $sql = "INSERT INTO customers (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            // "sssss" - five string parameters (name, email, hashedPassword, phone, address)
            $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $address);
            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
            } else {
                // Handle execution error (e.g., database issue)
                $errors[] = "Error: Could not execute the registration query.";
            }
            $stmt->close();
        } else {
            // Handle preparation error
            $errors[] = "Error: Could not prepare the registration query.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Registration</title>
</head>
<body>
    <h2>Register</h2>

    <!-- Display success or error messages -->
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            // Use htmlspecialchars to avoid HTML injection in messages
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    }
    if (!empty($success)) {
        echo "<p style='color:green;'>" . htmlspecialchars($success) . "</p>";
    }
    ?>

    <!-- Registration Form -->
    <form action="" method="post">
        <label for="name">Name:</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <label for="phone">Phone:</label><br>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" required><br><br>

        <label for="address">Address:</label><br>
        <textarea name="address" id="address" required><?php echo htmlspecialchars($address); ?></textarea><br><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in here</a>.</p>
</body>
</html>
