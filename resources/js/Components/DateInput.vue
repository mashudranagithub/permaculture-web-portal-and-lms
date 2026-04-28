<script setup>
import { onMounted, onBeforeUnmount, ref, watch, computed } from 'vue';

/**
 * PREMIUM DATE/TIME INPUT COMPONENT (Flatpickr + Boxicons)
 */
const props = defineProps({
    modelValue: [String, Date, Object, Array],
    type: { type: String, default: 'date' }, // date, datetime, time
    placeholder: { type: String, default: '' },
    error: String,
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    minDate: { type: String, default: null },
    maxDate: { type: String, default: null },
});

const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);
let fp = null;

// Determine icon based on type
const iconClass = computed(() => {
    if (props.type === 'time') return 'bx bx-time-five';
    if (props.type === 'datetime') return 'bx bx-calendar-event';
    return 'bx bx-calendar';
});

// Configure Flatpickr based on type
const initFlatpickr = () => {
    if (fp) fp.destroy();

    const flatpickr = window.flatpickr;
    if (!flatpickr) {
        console.error('Flatpickr not found globally!');
        return;
    }

    const config = {
        dateFormat: props.type === 'time' ? 'H:i' : (props.type === 'datetime' ? 'Y-m-d H:i' : 'Y-m-d'),
        altInput: true,
        altFormat: props.type === 'time' ? 'h:i K' : (props.type === 'datetime' ? 'd/m/Y h:i K' : 'd/m/Y'),
        allowInput: true,
        disableMobile: true,
        enableTime: props.type === 'datetime' || props.type === 'time',
        noCalendar: props.type === 'time',
        time_24hr: false,
        minDate: props.minDate,
        maxDate: props.maxDate,
        onChange: (selectedDates, dateStr) => {
            emit('update:modelValue', dateStr);
        },
    };

    fp = flatpickr(inputRef.value, config);
};

onMounted(() => {
    initFlatpickr();
});

onBeforeUnmount(() => {
    if (fp) fp.destroy();
});

// Sync value changes from parent
watch(() => props.modelValue, (newVal) => {
    if (fp && newVal !== fp.input.value) {
        fp.setDate(newVal, false);
    }
});

// Sync min/max date changes
watch(() => props.minDate, (newVal) => {
    if (fp) fp.set('minDate', newVal);
});

watch(() => props.maxDate, (newVal) => {
    if (fp) fp.set('maxDate', newVal);
});
</script>

<template>
    <div class="premium-date-wrapper">
        <div class="input-group custom-input-group" :class="{ 'is-invalid': error, 'disabled': disabled }">
            <span class="input-group-text bg-white border-end-0">
                <i :class="iconClass"></i>
            </span>
            <input 
                ref="inputRef"
                type="text"
                class="form-control border-start-0 ps-1 shadow-none"
                :class="{ 'is-invalid': error }"
                :value="modelValue"
                :placeholder="placeholder || (type === 'time' ? '00:00' : 'DD/MM/YYYY')"
                :disabled="disabled"
                :required="required"
                :readonly="readonly"
            >
        </div>
        <div v-if="error" class="text-danger small mt-1 animate__animated animate__fadeIn">
            {{ error }}
        </div>
    </div>
</template>

<style>
/* Premium Flatpickr Styling */
.flatpickr-calendar {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    font-family: 'Inter', system-ui, sans-serif !important;
}

.flatpickr-day.selected, 
.flatpickr-day.startRange, 
.flatpickr-day.endRange, 
.flatpickr-day.selected.prevMonthDay, 
.flatpickr-day.selected.nextMonthDay, 
.flatpickr-day.startRange.prevMonthDay, 
.flatpickr-day.startRange.nextMonthDay, 
.flatpickr-day.endRange.prevMonthDay, 
.flatpickr-day.endRange.nextMonthDay {
    background: #198754 !important;
    border-color: #198754 !important;
}

.flatpickr-months .flatpickr-month {
    background: #198754 !important;
    color: #fff !important;
    fill: #fff !important;
    border-radius: 8px 8px 0 0;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
    font-weight: 700 !important;
}

.flatpickr-calendar.hasTime .flatpickr-time {
    border-top: 1px solid #e2e8f0 !important;
}

/* Input Group Enhancements */
.custom-input-group {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    border-radius: 6px;
    transition: all 0.2s ease;
}

.custom-input-group .input-group-text {
    color: #198754;
    font-size: 1.1rem;
    padding-left: 0.75rem;
    padding-right: 0.5rem;
    border-color: #dee2e6;
}

.custom-input-group .form-control {
    height: 42px;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    font-size: 0.95rem;
    border-color: #dee2e6;
    color: #334155;
}

.custom-input-group:focus-within {
    border-color: #198754;
    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
}

.custom-input-group:focus-within .input-group-text,
.custom-input-group:focus-within .form-control {
    border-color: #198754;
}

.custom-input-group.is-invalid {
    border-color: #dc3545;
}

.custom-input-group.disabled {
    background-color: #f8fafc;
    opacity: 0.7;
}

.premium-date-wrapper .is-invalid {
    border-color: #dc3545 !important;
}
</style>
