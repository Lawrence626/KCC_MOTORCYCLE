<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Select an option',
    'submitForm' => false,
    'class' => '',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name',
    'label' => '',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Select an option',
    'submitForm' => false,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="relative w-full" x-data="dropdownComponent('<?php echo e($name); ?>', <?php echo e(json_encode($submitForm)); ?>)" @click.outside="open = false">
    <!-- Hidden input for form submission -->
    <input type="hidden" name="<?php echo e($name); ?>" :value="selectedValue" x-ref="hiddenInput" />
    
    <!-- Dropdown trigger button -->
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:border-[#105f68] hover:text-slate-900 transition <?php echo e($class); ?>"
    >
        <span x-text="selectedLabel || '<?php echo e($placeholder); ?>'"></span>
        <svg class="h-4 w-4 transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </button>

    <!-- Dropdown card menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute top-full left-0 mt-2 w-56 rounded-[15px] border border-slate-200 bg-white shadow-lg z-50"
    >
        <div class="p-3 space-y-1 max-h-60 overflow-y-auto">
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button
                    type="button"
                    @click="selectOption('<?php echo e($value); ?>', '<?php echo e($label); ?>')"
                    class="w-full text-left px-3 py-2.5 rounded-[10px] text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition"
                    :class="{ 'bg-[#105f68]/10 text-[#105f68] font-semibold': selectedValue === '<?php echo e($value); ?>' }"
                >
                    <?php echo e($label); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<script>
    function dropdownComponent(fieldName, shouldSubmit) {
        return {
            open: false,
            selectedValue: <?php echo json_encode($selected ?? '', 15, 512) ?>,
            selectedLabel: <?php echo json_encode($options[$selected] ?? '', 15, 512) ?>,
            fieldName: fieldName,
            shouldSubmit: shouldSubmit,
            
            selectOption(value, label) {
                this.selectedValue = value;
                this.selectedLabel = label;
                this.open = false;
                
                if (this.shouldSubmit) {
                    setTimeout(() => {
                        const form = this.$el.closest('form');
                        if (form) form.submit();
                    }, 50);
                }
            }
        };
    }
</script>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/components/dropdown-select.blade.php ENDPATH**/ ?>