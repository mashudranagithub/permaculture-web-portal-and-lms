<script setup>
import { computed } from 'vue';

/**
 * STANDARD STABLE DATE INPUT
 * Replaces the unstable VueDatePicker with a bulletproof native implementation
 * to ensure the application's Inertia routing and event loop remain stable.
 */
const props = defineProps({
    modelValue: [String, Date, Object, Array],
    type: { type: String, default: 'date' }, // date, datetime, time
    placeholder: { type: String, default: '' },
    error: String,
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

// Map my types to native input types
const inputType = computed(() => {
    if (props.type === 'datetime') return 'datetime-local';
    return props.type; // date, time, month, year
});

const value = computed({
    get: () => {
        if (!props.modelValue) return '';
        // Handle ISO strings (2026-04-21T00:00:00Z) by stripping the time for 'date' inputs
        if (typeof props.modelValue === 'string' && props.type === 'date') {
            return props.modelValue.split(' ')[0].split('T')[0];
        }
        return props.modelValue;
    },
    set: (val) => emit('update:modelValue', val),
});
</script>

<template>
    <div class="custom-date-container">
        <div class="input-group has-validation shadow-sm rounded-1 overflow-hidden">
            <span class="input-group-text bg-light border-end-0 text-success">
                <i v-if="type === 'time'" class="bi bi-clock"></i>
                <i v-else class="bi bi-calendar-date"></i>
            </span>
            <input 
                :type="inputType" 
                v-model="value"
                class="form-control border-start-0 ps-1"
                :class="{ 'is-invalid': error }"
                :required="required"
                :disabled="disabled"
                :readonly="readonly"
                :placeholder="placeholder"
            >
        </div>
        <div v-if="error" class="text-danger small mt-1 animate__animated animate__fadeIn">
            {{ error }}
        </div>
    </div>
</template>

<style scoped>
.custom-date-container {
    width: 100%;
}
.form-control {
    border-color: #dee2e6;
    padding-top: 0.6rem;
    padding-bottom: 0.6rem;
    font-size: 0.9rem;
}
.input-group-text {
    border-color: #dee2e6;
    font-size: 1rem;
    padding-right: 0.5rem;
}
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
    z-index: 3;
}
.input-group:focus-within .input-group-text {
    border-color: #198754;
    color: #198754 !important;
}

/* Success Green Calendar Icon Styling */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23198754" class="bi bi-calendar" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>');
}
</style>
