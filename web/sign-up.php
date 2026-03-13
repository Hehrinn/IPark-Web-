<?php
/**
 * iPark - User Registration Page
 */

require_once(__DIR__ . '/config/db.php');
require_once(__DIR__ . '/includes/auth.php');

// If already logged in, redirect
if (isUser()) {
    header('Location: ' . SITE_URL . '/dashboard/user_home.php');
    exit();
}

$page_title = 'Sign Up';
$style_file = 'sign-up';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        $result = registerUser($email, $password, $full_name, $phone);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . SITE_URL . '/index.php');
            exit();
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

        <!-- Registration Container -->
        <div class="w-full max-w-md space-y-8">

            <!-- Logo Section -->
            <div class="text-center">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="flex items-center justify-center size-12 rounded-xl bg-primary text-white">
                        <span class="material-symbols-outlined text-3xl">local_parking</span>
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white"><?php echo SITE_NAME; ?></h1>
                </div>
                <p class="text-slate-600 dark:text-slate-400">Create your account</p>
            </div>

            <!-- Registration Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8">

                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Get Started</h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm mb-6">Join <?php echo SITE_NAME; ?> today for easy parking</p>

                <!-- Error Alert -->
                <?php if (isset($error)): ?>
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-200"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">person</span>
                            <input
                                type="text"
                                name="full_name"
                                required
                                placeholder="John Doe"
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">mail</span>
                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="your@email.com"
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Phone Number</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">phone</span>
                            <input
                                type="tel"
                                name="phone"
                                placeholder="+1 (555) 000-0000"
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="••••••••"
                                minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            />
                        </div>
                        <p class="text-xs text-slate-500 mt-1">At least <?php echo PASSWORD_MIN_LENGTH; ?> characters required</p>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            id="terms"
                            required
                            class="mt-1 w-4 h-4 rounded border-slate-300 text-primary accent-primary"
                        />
                        <label for="terms" class="text-sm text-slate-600 dark:text-slate-400">
                            I agree to the <a href="#" class="text-primary hover:underline">Terms of Service</a> and <a href="#" class="text-primary hover:underline">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Sign Up Button -->
                    <button
                        type="submit"
                        class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-2.5 rounded-lg flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/25 mt-6"
                    >
                        <span>Create Account</span>
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>

                </form>

            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-slate-600 dark:text-slate-400">
                    Already have an account?
                    <a href="<?php echo SITE_URL; ?>/index.php" class="text-primary font-semibold hover:underline">Log in here</a>
                </p>
            </div>

        </div>

    </div>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
