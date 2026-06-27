<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head :title="__('Email Verification')" />

        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">{{ __('Verify Email') }}</h2>
            <p class="text-muted small">
                {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
            </p>
        </div>

        <div
            class="alert alert-success mb-4 text-sm fw-medium"
            v-if="verificationLinkSent"
        >
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>

        <form @submit.prevent="submit">
            <div class="d-grid gap-2">
                <button
                    class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm py-3"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ __('Resend Verification Email') }}
                </button>
            </div>

            <div class="mt-4 pt-2 text-center border-top">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="btn btn-link text-decoration-none small text-danger fw-bold"
                >
                    {{ __('Log Out') }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.25);
}
</style>
