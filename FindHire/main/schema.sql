CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender VARCHAR(50) NOT NULL,
    password VARCHAR(250) NOT NULL,
    age INT,
    date_of_birth DATE,
    email VARCHAR(100) NOT NULL UNIQUE,
    address TEXT NULL,
    contact_no VARCHAR(20) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,  -- 0 for applicant, 1 for admin
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role VARCHAR(50) NOT NULL,                       -- Role of the user: 'admin' or 'applicant'
    action_type VARCHAR(50) NOT NULL,                             -- Possible values: CREATE, UPDATE, OR DELETE
    action_details TEXT,                                          -- Comments from the database
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)   -- Define what happens if user is deleted
);

CREATE TABLE IF NOT EXISTS job_posts (
    job_post_id INT AUTO_INCREMENT PRIMARY KEY,  -- Unique ID for each job post
    title VARCHAR(255) NOT NULL,                 -- Job title (e.g., Software Engineer)
    description TEXT NOT NULL,                   -- Job description
    location VARCHAR(255),                       -- Job location (can be optional)
    salary DECIMAL(10, 2),                       -- Salary for the job, can be NULL if not specified
    application_deadline DATE,                   -- Deadline for applying to the job
    hr_user_id INT NOT NULL,                     -- The user (HR) who created the job post
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Timestamp of when the job post was created
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('Accepting Applicants', 'No longer available') DEFAULT 'Accepting Applicants', -- Status of the job post (open or closed)
    FOREIGN KEY (hr_user_id) REFERENCES users(user_id)   -- Link to HR user table, set HR to NULL if deleted
);

CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,                       -- User ID (sender)
    receiver_id INT NOT NULL,                     -- HR User ID (receiver)
    message TEXT NOT NULL,                        -- The message sent by the user
    pdf_file_path VARCHAR(255),                   -- Path to the uploaded PDF file
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Timestamp for when the message is sent
    job_post_id INT,                              -- Job Post ID (newly added)
    FOREIGN KEY (sender_id) REFERENCES users(user_id),  -- User who sent the message, set sender to NULL if deleted
    FOREIGN KEY (receiver_id) REFERENCES users(user_id), -- HR User who receives the message, set receiver to NULL if deleted
    FOREIGN KEY (job_post_id) REFERENCES job_posts(job_post_id) -- Link to the job post, delete message if the job post is deleted
);

CREATE TABLE IF NOT EXISTS replies (
    reply_id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL, -- The ID of the original message being replied to
    sender_id INT NOT NULL,  -- The user who sent the reply
    receiver_id INT NOT NULL, -- The user who receives the reply (usually the sender of the original message)
    reply_message TEXT NOT NULL, -- The reply content
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(message_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS file_uploads (
    upload_id INT AUTO_INCREMENT PRIMARY KEY,
    job_post_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    message TEXT,
    uploaded_by INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_post_id) REFERENCES job_posts(job_post_id),
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id)
);
