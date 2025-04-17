<?php
// login.php - User Login script

session_start();              // Start a session to allow login state tracking via $_SESSION
require_once __DIR__ . '/../includes/db_connect.php';
     // Include database connection

$errors = array();
$success = "";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    // 1. Validate required fields
    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in both email and password.";
    }
    // 2. Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // 3. If no errors, check credentials against the database
    if (empty($errors)) {
        $sql = "SELECT id, name, email, password FROM customers WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();  // Get the result set from the prepared statement
            if ($result && $result->num_rows > 0) {
                // Email found, now verify password
                $user = $result->fetch_assoc();  // Fetch associative array of user data
                if (password_verify($password, $user['password'])) {
                    // 4. Password is correct -> set session variables for the logged-in user
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    // You can store additional info in session as needed

                    $success = "Login successful! Welcome, " . htmlspecialchars($user['name']) . ".";
                    // Optionally, redirect to a protected page or homepage:
                    // header("Location: index.php");
                    // exit;
                } else {
                    // Password mismatch
                    $errors[] = "Invalid email or password.";
                }
            } else {
                // No user found with that email
                $errors[] = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: Could not prepare login query.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Login</title>
</head>
<body>
    <h2>Login</h2>

    <!-- Display error or success messages -->
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    }
    if (!empty($success)) {
        echo "<p style='color:green;'>" . htmlspecialchars($success) . "</p>";
        // Provide a logout link when logged in
        echo "<p><a href='logout.php'>Log out</a></p>";
    }
    ?>

    <!-- Show login form only if not logged in yet -->
    <?php if (empty($success)): ?>
    <form action="" method="post">
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    <?php endif; ?>
</body>
</html>
