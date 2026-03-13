<?php
/**
 * iPark Page Header
 * Included on all pages
 */

if (session_status() === PHP_SESSION_NONE) {
    require_once(__DIR__ . '/auth.php');
}
if (!defined('SITE_URL')) {
    require_once(__DIR__ . '/../config/db.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo ($page_title ?? 'iPark') . ' | ' . SITE_NAME; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <!-- Custom Styles -->
    <link href="<?php echo SITE_URL; ?>/style/<?php echo ($style_file ?? 'index') . '.css'; ?>" rel="stylesheet" />

    <style>
        /* Prevents flash of unstyled icon text */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
    </style>

    <!-- Tailwind Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#1152d4",
                        "secondary": "#10b981",
                        "background-light": "#f6f6f8",
                        "background-dark": "#101622",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>

</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen">

    <?php
    // Display success/error messages
    if (isset($_SESSION['flash_message'])) {
        $message = '';
        $messageType = 'info'; // Default type

        // Check if the flash message is an array (e.g., ['success' => '...']) or a string
        if (is_array($_SESSION['flash_message'])) {
            $messageType = key($_SESSION['flash_message']);
            $message = current($_SESSION['flash_message']);
        } else {
            $message = $_SESSION['flash_message'];
            $messageType = $_SESSION['flash_type'] ?? 'info';
        }

        // Safety check to prevent TypeError if data is still not a string
        $message = is_string($message) ? $message : 'An unknown notification was triggered.';

        $bgColor = $messageType === 'error' ? 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800' : 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800';
        $textColor = $messageType === 'error' ? 'text-red-800 dark:text-red-200' : 'text-green-800 dark:text-green-200';
    ?>
        <div class="fixed top-4 right-4 max-w-md p-3 border rounded-lg <?php echo $bgColor . ' ' . $textColor; ?> z-50">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
    ?>
