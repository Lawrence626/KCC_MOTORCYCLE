<?php if(auth()->guard()->check()): ?>
<div class="mb-4 rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-4">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-white text-xl font-semibold overflow-hidden">
                <?php if(auth()->user()->avatar): ?>
                    <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-full h-full object-cover" />
                <?php else: ?>
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                <?php endif; ?>
            </span>
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-slate-900"><?php echo e(auth()->user()->name ?? 'Admin'); ?></span>
                <span class="text-xs text-slate-500"><?php echo e(auth()->user()->email ?? ''); ?></span>
            </div>
        </div>

        <div class="relative inline-flex items-center gap-3 rounded-[20px] bg-slate-950 px-4 py-2 text-white shadow-lg">
            <div>
                <button id="profileSectionButton" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 text-white transition hover:bg-slate-700 focus:outline-none" aria-label="Open profile menu">
                    <svg id="profileSectionArrow" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z" /></svg>
                </button>
            </div>

            <div id="profileSectionDropdown" class="absolute right-0 top-full mt-2 w-72 min-h-[120px] rounded-[17px] border border-slate-700/60 bg-[#0f0f0f] shadow-2xl shadow-black/40 z-50 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                <div class="px-4 py-4 border-b border-slate-800/60">
                    <div class="flex items-center gap-3">
                        <span class="w-12 h-12 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-lg font-semibold">
                            <?php if(auth()->user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-full h-full object-cover" />
                            <?php else: ?>
                                <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                            <?php endif; ?>
                        </span>
                        <div>
                            <div class="text-[13px] font-semibold text-white"><?php echo e(auth()->user()->name ?? 'Admin'); ?></div>
                            <div class="text-[12px] text-slate-400"><?php echo e(auth()->user()->email ?? ''); ?></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-cyan-300">
                            <?php echo e(ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user'))); ?>

                        </span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2">
                    <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span>View Profile</span>
                    </a>
                    <a href="<?php echo e(route('settings.general')); ?>" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span>Settings</span>
                    </a>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-rose-300 hover:bg-slate-800 transition">
                            <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-rose-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/components/profile-section.blade.php ENDPATH**/ ?>