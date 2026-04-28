<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: [String, Number, Array],
    placeholder: {
        type: String,
        default: ''
    },
    allowClear: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectRef = ref(null);

onMounted(() => {
    const jQuery = window.$;
    if (!jQuery || !jQuery.fn.select2) {
        console.error('Select2 or jQuery not found globally!');
        return;
    }

    const $select = jQuery(selectRef.value);
    
    $select.select2({
        theme: 'bootstrap-5',
        placeholder: props.placeholder,
        allowClear: props.allowClear,
        width: '100%',
        dropdownParent: $select.parent()
    })
    .on('change', (e) => {
        const value = jQuery(e.target).val();
        emit('update:modelValue', value);
        emit('change', value);
    });

    if (props.modelValue !== undefined && props.modelValue !== null) {
        $select.val(props.modelValue).trigger('change.select2');
    }
});

watch(() => props.modelValue, (newVal) => {
    const jQuery = window.$;
    if (jQuery && jQuery.fn.select2) {
        const $select = jQuery(selectRef.value);
        if ($select.val() !== newVal) {
            $select.val(newVal).trigger('change.select2');
        }
    }
});

watch(() => props.disabled, (newVal) => {
    const jQuery = window.$;
    if (jQuery && jQuery.fn.select2) {
        jQuery(selectRef.value).prop('disabled', newVal).trigger('change.select2');
    }
});

onUnmounted(() => {
    const jQuery = window.$;
    if (jQuery && jQuery.fn.select2 && selectRef.value) {
        jQuery(selectRef.value).select2('destroy');
    }
});
</script>

<template>
    <select ref="selectRef" :disabled="disabled" class="form-select shadow-none">
        <slot></slot>
    </select>
</template>

<style>
/* Ensure Select2 container matches Bootstrap 5 styling */
/* Global Select2 Clear Button Force */
.select2-selection__clear {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: absolute !important;
    right: 2.5rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #dc3545 !important;
    cursor: pointer !important;
    z-index: 9999 !important;
    background: transparent !important;
    width: 12px !important;
    height: 12px !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;
    border: 1px solid #dc3545 !important;
    padding: 0 !important;
}

.select2-selection__clear::before,
.select2-selection__clear::after {
    content: '' !important;
    position: absolute !important;
    height: 6px !important;
    width: 1.5px !important;
    background-color: #dc3545 !important;
    top: 50% !important;
    left: 50% !important;
}

.select2-selection__clear::before {
    transform: translate(-50%, -50%) rotate(45deg) !important;
}

.select2-selection__clear::after {
    transform: translate(-50%, -50%) rotate(-45deg) !important;
}

.select2-container--bootstrap-5 .select2-selection {
    position: relative !important;
    overflow: visible !important;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    height: 42px;
    display: flex;
    align-items: center;
}

.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    color: #212529;
    padding-left: 0.75rem;
    font-size: 0.95rem;
    width: 100%;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
    color: #6c757d;
}
</style>
