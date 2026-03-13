<?php
/**
 * iPark - Login Page
 */

require_once(__DIR__ . '/config/db.php');
require_once(__DIR__ . '/includes/auth.php');

// DEBUG: Enable Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If already logged in, redirect to appropriate dashboard
if (isAdmin()) {
    header('Location: ' . SITE_URL . '/admin.php');
    exit();
} elseif (isUser()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit();
}

$page_title = 'Login';
$style_file = 'index';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid. Please try again.';
    } else {
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        
        $result = loginUser($email, $password);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
            
            // Clean Redirect: Check session variables directly after login
            if (isset($_SESSION['admin_id'])) {
                header('Location: admin.php');
                exit();
            } else {
                header('Location: ' . SITE_URL . '/dashboard.php');
                exit();
            }
        } else {
            $error = $result['message'];
        }
    }
}

$csrf_token = generateCSRFToken();
?>

<?php require_once(__DIR__ . '/includes/header.php'); ?>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center px-4 py-12">

        <!-- Login Container -->
        <div class="w-full max-w-md space-y-8">

            <!-- Logo Section -->
            <div class="text-center">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="flex items-center justify-center size-12 rounded-xl bg-primary text-white">
                        <span class="material-symbols-outlined text-3xl">local_parking</span>
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white"><?php echo SITE_NAME; ?></h1>
                </div>
                <p class="text-slate-600 dark:text-slate-400">Smart Parking Reservations</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8">

                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Welcome Back</h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm mb-6">Sign in to your account to continue</p>

                <!-- Error Alert -->
                <?php if (isset($error)): ?>
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-200"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <!-- Email Field -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">mail</span>
                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="your@email.com"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Password</label>
                            <a href="#" class="text-xs text-primary hover:underline">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Sign In Button -->
                    <button
                        type="submit"
                        class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/25 mt-6"
                    >
                        <span>Sign In</span>
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>

                </form>

            </div>

            <!-- Sign Up Link -->
            <div class="text-center">
                <p class="text-slate-600 dark:text-slate-400">
                    Don't have an account?
                    <a href="<?php echo SITE_URL; ?>/sign-up.php" class="text-primary font-semibold hover:underline">Sign up here</a>
                </p>
            </div>

        </div>

    </div>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
