<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Profile & Settings')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Profile & Settings'))]); ?>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Profile & Settings</h1>
                <p class="text-xs text-slate-500 mt-1">Manage your account, security, and preferences</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-3 mb-4">
                    <button data-tab="security" class="settings-tab px-3 py-2 rounded-lg text-sm font-medium bg-cyan-600 text-white">Security</button>
                    <button data-tab="preferences" class="settings-tab px-3 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-50">Preferences</button>
                    <a href="<?php echo e(route('profile.show')); ?>" class="ml-auto text-sm text-cyan-600 font-medium">View Profile</a>
                </div>

                <div id="tabSecurity" class="settings-pane">
                    <h3 class="text-sm font-semibold text-slate-900 mb-2">Two-factor Authentication</h3>
                    <p class="text-xs text-slate-500 mb-3">Manage your two-factor authentication settings (TOTP, SMS).</p>
                    <div class="flex gap-2">
                        <button class="px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Enable</button>
                        <button class="px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Disable</button>
                    </div>
                </div>

                <div id="tabPreferences" class="settings-pane hidden">
                    <h3 class="text-sm font-semibold text-slate-900 mb-2">Theme</h3>
                    <p class="text-xs text-slate-500 mb-3">Choose your interface accent color.</p>
                    <div class="flex items-center gap-3">
                        <button class="theme-swatch w-8 h-8 rounded-full bg-cyan-500 border-2 border-white shadow-sm" data-color="cyan"></button>
                        <button class="theme-swatch w-8 h-8 rounded-full bg-emerald-500 border-2 border-white shadow-sm" data-color="emerald"></button>
                        <button class="theme-swatch w-8 h-8 rounded-full bg-violet-500 border-2 border-white shadow-sm" data-color="violet"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tabs
        document.querySelectorAll('.settings-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('bg-cyan-600','text-white'));
                document.querySelectorAll('.settings-tab').forEach(b => b.classList.add('bg-slate-50','text-slate-700'));
                btn.classList.add('bg-cyan-600','text-white');
                btn.classList.remove('bg-slate-50','text-slate-700');

                const tab = btn.dataset.tab;
                document.querySelectorAll('.settings-pane').forEach(p => p.classList.add('hidden'));
                document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('hidden');
            });
        });

        // Tabs
        document.querySelectorAll('.settings-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('bg-cyan-600','text-white'));
                document.querySelectorAll('.settings-tab').forEach(b => b.classList.add('bg-slate-50','text-slate-700'));
                btn.classList.add('bg-cyan-600','text-white');
                btn.classList.remove('bg-slate-50','text-slate-700');

                const tab = btn.dataset.tab;
                document.querySelectorAll('.settings-pane').forEach(p => p.classList.add('hidden'));
                document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('hidden');
            });
        });

        // Theme swatches (simple client-side placeholder)
        document.querySelectorAll('.theme-swatch').forEach(btn => {
            btn.addEventListener('click', () => {
                const color = btn.dataset.color;
                document.documentElement.style.setProperty('--theme-accent', color);
                alert('Theme set to ' + color + '. Refresh to persist (not yet implemented).');
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/settings/general.blade.php ENDPATH**/ ?>