<?php
/**
 * iPark Page Footer
 * Included on all pages
 */
?>

    <!-- Footer -->
    <footer class="bg-white dark:bg-background-dark border-t border-slate-200 dark:border-slate-800 px-4 md:px-10 lg:px-40 py-10 mt-auto">
        <div class="max-w-[1200px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">

            <!-- Brand -->
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 text-primary mb-4">
                    <span class="material-symbols-outlined text-2xl font-bold">local_parking</span>
                    <h2 class="text-slate-900 dark:text-white text-lg font-bold tracking-tight"><?php echo SITE_NAME; ?></h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Simplifying urban parking through smart digital reservations. Fast, secure, and always available.
                </p>
            </div>

            <!-- Company Links -->
            <div>
                <h4 class="text-sm font-bold mb-4 text-slate-900 dark:text-white uppercase tracking-wider">Company</h4>
                <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-2">
                    <li><a class="hover:text-primary transition-colors" href="<?php echo SITE_URL; ?>/">About Us</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?php echo SITE_URL; ?>/">Careers</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?php echo SITE_URL; ?>/">Press</a></li>
                </ul>
            </div>

            <!-- Legal Links -->
            <div>
                <h4 class="text-sm font-bold mb-4 text-slate-900 dark:text-white uppercase tracking-wider">Legal</h4>
                <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-2">
                    <li><a class="hover:text-primary transition-colors" href="<?php echo SITE_URL; ?>/">Privacy Policy</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?php echo SITE_URL; ?>/">Terms of Service</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?php echo SITE_URL; ?>/">Cookie Policy</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-sm font-bold mb-4 text-slate-900 dark:text-white uppercase tracking-wider">Contact</h4>
                <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">mail</span>
                        <?php echo ADMIN_EMAIL; ?>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">phone</span>
                        1-800-IPARK
                    </li>
                </ul>
            </div>

        </div>

        <!-- Footer Bottom -->
        <div class="max-w-[1200px] mx-auto mt-10 pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">© 2026 <?php echo SITE_NAME; ?> Inc. All rights reserved.</p>
            <div class="flex gap-4">
                <a class="text-slate-400 hover:text-primary transition-colors" href="#">
                    <span class="material-symbols-outlined">public</span>
                </a>
                <a class="text-slate-400 hover:text-primary transition-colors" href="#">
                    <span class="material-symbols-outlined">alternate_email</span>
                </a>
                <a class="text-slate-400 hover:text-primary transition-colors" href="#">
                    <span class="material-symbols-outlined">hub</span>
                </a>
            </div>
        </div>

    </footer>

    </body>

</html>
