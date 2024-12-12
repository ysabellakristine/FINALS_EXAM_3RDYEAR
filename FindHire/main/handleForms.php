<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}
// Registration Logic
if (isset($_POST['regBtn']) || isset($_POST['regHRBtn'])) {
    // Sanitize and retrieve input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);
    
    // Check if "regHRBtn" was clicked (for admin)
    $is_admin = isset($_POST['regHRBtn']) ? 1 : 0; // If regHRBtn is clicked, set is_admin as 1, else set as 0

    // Validate input
    if (empty($username) || empty($first_name) || empty($last_name) || empty($gender) || empty($email) || empty($password) || empty($age) || empty($date_of_birth) || empty($address) || empty($contact_no)) {
        $_SESSION['message'] = "All fields are required.";
        header('Location: ../register.php');
        exit;
    }

    // Password complexity check (optional)
    if (strlen($password) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        header('Location: ../register.php');
        exit;
    }

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Username or email already exists.";
        header('Location:../register.php');
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, gender, email, password, age, date_of_birth, address, contact_no, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $first_name, $last_name, $gender, $email, $hashed_password, $age, $date_of_birth, $address, $contact_no, $is_admin]);

        // Set success message and redirect
        $_SESSION['message'] = "Registration successful! You can log in now.";
        header('Location: ../login.php');
        exit;
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        header('Location: ../register.php');
        exit;
    }
}

// Login Logic
if (isset($_POST['loginBtn'])) {
    // Sanitize inputs
    $login_input = filter_input(INPUT_POST, 'login_input', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Check if inputs are valid (i.e., not empty)
    if (empty($login_input) || empty($password)) {
        $_SESSION['message'] = "Both fields are required.";
        header("Location: login.php");
        exit;
    }

    try {
        // Prepare SQL query to fetch user by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        
        // Execute with the same value for both email and username
        $stmt->execute(['email' => $login_input, 'username' => $login_input]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

        // Verify user and password
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name']; 
            $_SESSION['welcomeMessage'] = "Welcome, " . $_SESSION['username'] . "!";

            // Log user action (optional, adjust the logAction function accordingly)
            logAction($pdo, $_SESSION['user_id'], $user['is_admin'] == 1 ? 'Admin' : 'Applicant', 'LOG IN', "Logged in: " . $_SESSION['username']);

            // Redirect based on user role (admin or applicant)
            if ($user['is_admin'] == 1) {
                // Admin: Redirect to admin dashboard
                header("Location: hr_user/index.php");
            } else {
                // Applicant: Redirect to applicant dashboard
                header("Location: applicant/index.php");
            }
            exit;
        } else {
            // Invalid credentials
            $_SESSION['message'] = "Invalid login input or password. Please try again.";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        // Handle database error
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
}



// Application Search Logic
if (isset($_POST['searchUserBtn'])) {
    // Check if any fields are filled
    if (empty($_POST['first_name']) && empty($_POST['last_name']) && empty($_POST['email']) && empty($_POST['contact_no']) && empty($_POST['gender']) && empty($_POST['qualification']) && empty($_POST['application_status'])) {
        $_SESSION['searchResponse'] = $response;
        header("Location: ../search_users.php");
        exit();
    }

    try {
        $sql = "SELECT * FROM applicants WHERE 1=1";
        $params = [];

        // Check each input and add to the query if it's not empty
        if (!empty($_POST['first_name'])) {
            $sql .= " AND first_name LIKE :first_name";
            $params[':first_name'] = '%' . $_POST['first_name'] . '%';
        }
        if (!empty($_POST['last_name'])) {
            $sql .= " AND last_name LIKE :last_name";
            $params[':last_name'] = '%' . $_POST['last_name'] . '%';
        }
        if (!empty($_POST['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . $_POST['email'] . '%';
        }
        if (!empty($_POST['contact_no'])) {
            $sql .= " AND contact_no LIKE :contact_no";
            $params[':contact_no'] = '%' . $_POST['contact_no'] . '%';
        }
        if (!empty($_POST['gender'])) {
            $sql .= " AND gender = :gender";
            $params[':gender'] = $_POST['gender'];
        }
        if (!empty($_POST['qualification'])) {
            $sql .= " AND qualification LIKE :qualification";
            $params[':qualification'] = '%' . $_POST['qualification'] . '%';
        }
        if (!empty($_POST['application_status'])) {
            $sql .= " AND application_status = :application_status";
            $params[':application_status'] = $_POST['application_status'];
        }

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $querySet = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update response
        if ($querySet) {
            $response['message'] = "Search results found.";
            $response['querySet'] = $querySet;
        } else {
            $response['message'] = "No users found with the given criteria.";
        }

        // Log action if user is logged in
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $action_type = 'SEARCH';

            // Filter out empty fields and 'searchUserBtn' from $_POST
            $filteredCriteria = array_filter($_POST, function ($value, $key) {
                return $key !== 'searchUserBtn' && !empty($value);
            }, ARRAY_FILTER_USE_BOTH);

            // Manually format criteria for logging without quotation marks
            $criteriaStrings = [];
            foreach ($filteredCriteria as $key => $value) {
                $criteriaStrings[] = "$key: $value";
            }
            $action_details = "Searched with the criteria: [" . implode("], [", $criteriaStrings) . "]";

            logAction($pdo, $user_id, $action_type, $action_details);
        }

    } catch (PDOException $e) {
        $response['message'] = "Database error: " . $e->getMessage();
    }

    // Store response in session to retrieve in search_users.php
    $_SESSION['searchResponse'] = $response;

    // Redirect back to search_users.php to display the results
    header("Location: ../search_users.php");
    exit();
}

if (isset($_POST['insertJobPostBtn'])) {

    // Get the user_id from the session
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in and session is active
    
    // Check if necessary POST data is available
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $location = $_POST['location'] ?? null;
    $salary = $_POST['salary'] ?? null;
    $application_deadline = $_POST['application_deadline'] ?? null;
    $qualification = $_POST['qualification'] ?? null;
    $status = $_POST['status'] ?? null;

    // Set date_posted to current date if not provided
    $date_posted = date('Y-m-d H:i:s'); // Current timestamp

    // Call the insertJobPost function to insert the job post
    $query = insertJobPost($pdo, $title, $description, $location, $salary, $application_deadline, $user_id, $date_posted, $status);

    if ($query) {
        header("Location: ../hr_user/index.php"); // Redirect on success
        exit;
    } else {
        echo "Insertion failed"; // Error handling
    }
}

if (isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Handle file upload if there's an attachment
    $pdf_file_path = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['pdf_file']['name']);
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_file)) {
            $pdf_file_path = $target_file; // Save the file path in the database
        }
    }

    // Insert the new message into the database
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, pdf_file_path, date_sent) VALUES (?, ?, ?, ?, NOW())";
    logAction($pdo, $user_id, 'Admin', 'SENT MESSAGE', "Sent Message to: $receiver_id");

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $receiver_id, $message, $pdf_file_path]);

    // Redirect to the inbox page after sending the message
    header("Location: ../hr_user/message_list.php");
    exit;
}

if (isset($_POST['send_message_applicant'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Handle file upload if there's an attachment
    $pdf_file_path = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['pdf_file']['name']);
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_file)) {
            $pdf_file_path = $target_file; // Save the file path in the database
        }
    }

    // Insert the new message into the database
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, pdf_file_path, date_sent) VALUES (?, ?, ?, ?, NOW())";
    logAction($pdo, $user_id, 'Admin', 'SENT MESSAGE', "Sent Message to: $receiver_id");

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $receiver_id, $message, $pdf_file_path]);

    // Redirect to the inbox page after sending the message
    header("Location: ../applicant/message_list.php");
    exit;
}

if (isset($_POST['reply_btn'])) {
    // The reply button was clicked
    // You can now handle the form submission logic
    $message_id = $_POST['message_id'];
    $receiver_id = $_POST['receiver_id'];
    $reply_message = $_POST['reply_message'];

    // Insert the reply into the database or perform any necessary actions
    $sql = "INSERT INTO replies (message_id, sender_id, receiver_id, reply_message, date_sent) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$message_id, $_SESSION['user_id'], $receiver_id, $reply_message]);

    // Redirect or provide feedback to the user
    echo "Reply sent successfully!";
    header("Location: ../hr_user/message_list.php");
}

if (isset($_POST['reply_btn_applicant'])) {
    // The reply button was clicked
    // You can now handle the form submission logic
    $message_id = $_POST['message_id'];
    $receiver_id = $_POST['receiver_id'];
    $reply_message = $_POST['reply_message'];

    // Insert the reply into the database or perform any necessary actions
    $sql = "INSERT INTO replies (message_id, sender_id, receiver_id, reply_message, date_sent) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$message_id, $_SESSION['user_id'], $receiver_id, $reply_message]);

    // Redirect or provide feedback to the user
    echo "Reply sent successfully!";
    header("Location: ../applicant/message_list.php");
}
?>
