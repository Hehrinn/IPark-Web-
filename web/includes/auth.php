<?php
// iPark Authentication & Authorization System

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_destroy();
    $_SESSION = [];
}
$_SESSION['last_activity'] = time();

// --- CSRF & Password Security ---

// Generate CSRF Token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verifyCSRFToken($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Hash Password
function hashPassword($password) {
    return password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
}

// Verify Password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}


// --- Session & User State Checkers ---

function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function isUser() {
    return isset($_SESSION['user_id']);
}


// --- Getters for User/Admin Info ---

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentAdminId() {
    return $_SESSION['admin_id'] ?? null;
}

function getCurrentUserRole() {
    if (isset($_SESSION['admin_id'])) {
        return $_SESSION['admin_role'] ?? 'admin';
    }
    return isset($_SESSION['user_id']) ? 'user' : 'guest';
}


// --- Authentication & Role Enforcement ---

// Redirects to login if not authenticated as user
function requireUser() {
    if (!isUser()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

// Redirects to admin login if not authenticated as admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit();
    }
}

// Requires a specific admin role
function requireAdminRole($role) {
    requireAdmin();
    if ($_SESSION['admin_role'] !== $role && $_SESSION['admin_role'] !== 'super_admin') {
        http_response_code(403);
        die('Access Denied: Insufficient permissions');
    }
}


// --- Auditing ---

// Log user activity
function logActivity($action, $table_name, $record_id, $old_value = null, $new_value = null) {
    global $conn;
    
    $user_id = getCurrentUserId();
    $admin_id = getCurrentAdminId();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("
        INSERT INTO ipark_audit_logs 
        (user_id, admin_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $old_json = $old_value ? json_encode($old_value) : null;
    $new_json = $new_value ? json_encode($new_value) : null;
    
    $stmt->bind_param("iissiisss", 
        $user_id, $admin_id, $action, $table_name, $record_id,
        $old_json, $new_json, $ip_address, $user_agent
    );
    
    $stmt->execute();
    $stmt->close();
}


// --- User Authentication ---

// Login a user
function loginUser($email, $password) {
    global $conn;

    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required'];
    }

    // Specific Admin Check
    if ($email === 'admin@gmail.com') {
        $stmt = $conn->prepare("SELECT * FROM ipark_admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && password_verify($password, $row['password_hash'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['admin_role'] = $row['role'];
            $_SESSION['admin_name'] = $row['full_name'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            return ['success' => true, 'message' => 'Admin login successful'];
        }
    }

    // Standard User Check
    $stmt = $conn->prepare("SELECT id, password_hash, full_name, is_active FROM ipark_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return ['success' => true, 'message' => 'Login successful'];
    }

    // Login Failed
    return ['success' => false, 'message' => 'Invalid email or password'];
}

// Register a new user
function registerUser($email, $password, $full_name, $phone = null) {
    global $conn;
    
    if (empty($email) || empty($password) || empty($full_name)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM ipark_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Email already registered'];
    }
    $stmt->close();
    
    // Generate username from email
    $username = explode('@', $email)[0] . '_' . substr(bin2hex(random_bytes(4)), 0, 8);
    
    // Hash password
    $password_hash = hashPassword($password);
    
    // Generate verification token
    $verification_token = bin2hex(random_bytes(32));
    
    // Insert user and auto-verify for testing
    $stmt = $conn->prepare("
        INSERT INTO ipark_users (username, email, password_hash, full_name, phone, verification_token, is_active, email_verified_at)
        VALUES (?, ?, ?, ?, ?, ?, TRUE, NOW())
    ");
    
    $stmt->bind_param("ssssss", $username, $email, $password_hash, $full_name, $phone, $verification_token);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $stmt->close();
        
        logActivity('user_registration', 'ipark_users', $user_id);
        
        // TODO: Send verification email with $verification_token
        return [
            'success' => true,
            'message' => 'Registration successful! You can now login.',
            'verification_token' => $verification_token
        ];
    } else {
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

// Logout the current user or admin
function logout() {
    if (isUser()) {
        logActivity('user_logout', 'ipark_users', getCurrentUserId());
    } elseif (isAdmin()) {
        logActivity('admin_logout', 'ipark_admins', getCurrentAdminId());
    }
    
    session_destroy();
    $_SESSION = [];
    setcookie(session_name(), '', time() - 3600, '/');
}

?>
