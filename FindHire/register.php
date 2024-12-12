<?php 
require_once 'main/handleForms.php';
require_once 'main/models.php'; 
?>
<!DOCTYPE html>
<div lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class = 'container'>
    <h1>Register Here!</h1>
    <?php if (isset($_SESSION['message'])) { ?>
        <h1 style="color: red;"><?php echo $_SESSION['message']; ?></h1>
    <?php unset($_SESSION['message']); } ?>

    <form action="main/handleForms.php" method="POST">
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" required>
        </p>
        <p>
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" required>
        </p>
        <p>
            <label for="gender">Gender</label>
            <select name="gender" required>
                <option value="">--Select Gender--</option>
                <?php
                $genders = ["Male", "Female", "Nonbinary", "Secret", "Helicopter", "ML Player"];
                foreach ($genders as $gender) {
                    echo "<option value='$gender'>$gender</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" required>
        </p>
        <p>
            <label for="age">Age</label>
            <input type="number" name="age" min="0">
        </p>
        <p>
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" name="date_of_birth">
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" required>
        </p>
        <p>
            <label for="address">Address</label>
            <textarea name="address"></textarea>
        </p>
        <p>
            <label for="contact_no">Contact Number</label>
            <input type="text" name="contact_no" required>
        </p>
        <p>
            <input type="submit" name="regBtn" value="Register">
        </p>
    </form>
    <p>Already have an account? Log in <a href="login.php">here</a>.</p><br>
    <p>Not an applicant? Register as HR: <a href="register_hr.php">here</a>.</p> <br>

</body>
            </div>
</html>
