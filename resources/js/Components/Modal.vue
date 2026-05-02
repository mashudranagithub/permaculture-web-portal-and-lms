<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;

            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';

            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();

        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);

    document.body.style.overflow = '';
});

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
    }[props.maxWidth];
});

const customStyle = computed(() => {
    if (!props.maxWidth) return {};
    if (props.maxWidth.includes('%') || props.maxWidth.includes('px') || props.maxWidth.includes('vw')) {
        return { 
            width: props.maxWidth,
            maxWidth: props.maxWidth,
            minWidth: props.maxWidth
        };
    }
    return { width: '100%' };
});
</script>

<template>
    <dialog
        class="z-50 min-h-full min-w-full overflow-y-auto bg-transparent border-0 outline-none p-0"
        ref="dialog"
    >
        <div
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 d-flex align-items-center justify-content-center"
            scroll-region
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform"
                    style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
                    @click="close"
                >
                    <div
                        class="absolute inset-0 bg-dark opacity-50"
                    />
                </div>
            </Transition>

            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl"
                    :class="maxWidthClass"
                    :style="customStyle"
                >
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>

<style scoped>
/* Standard Max Widths in Vanilla CSS */
.sm\:max-w-sm { max-width: 384px !important; }
.sm\:max-w-md { max-width: 448px !important; }
.sm\:max-w-lg { max-width: 512px !important; }
.sm\:max-w-xl { max-width: 576px !important; }
.sm\:max-w-2xl { max-width: 672px !important; }
.sm\:max-w-3xl { max-width: 768px !important; }
.sm\:max-w-4xl { max-width: 896px !important; }
.sm\:max-w-5xl { max-width: 1024px !important; }

@media (max-width: 640px) {
    div[style*="max-width"] {
        max-width: 95% !important;
    }
}
</style>
