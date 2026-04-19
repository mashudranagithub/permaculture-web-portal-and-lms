<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="__('Log in')" />

        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">{{ __('Log in') }}</h2>
            <p class="text-muted small">{{ __('Welcome back to Permaculture Methods') }}</p>
        </div>

        <div v-if="status" class="alert alert-success mb-4 text-sm fw-medium">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-bold small text-uppercase tracking-wide text-muted">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="email"
                    class="form-control form-control-lg rounded-3"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    :class="{ 'is-invalid': form.errors.email }"
                />
                <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-bold small text-uppercase tracking-wide text-muted">{{ __('Password') }}</label>
                <input
                    id="password"
                    type="password"
                    class="form-control form-control-lg rounded-3"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    :class="{ 'is-invalid': form.errors.password }"
                />
                <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
            </div>

            <!-- Remember Me -->
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" v-model="form.remember">
                    <label class="form-check-label small text-muted" for="remember">
                        {{ __('Remember me') }}
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <button
                    class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm py-3"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ __('Log in') }}
                </button>
            </div>

            <div class="mt-4 pt-2 text-center border-top">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-decoration-none small text-success fw-medium"
                >
                    {{ __('Forgot your password?') }}
                </Link>
                <div class="mt-2 text-muted small">
                    {{ __("Don't have an account?") }} 
                    <Link :href="route('register')" class="text-success fw-bold text-decoration-none ms-1">{{ __('Sign Up') }}</Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.form-control:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.1);
}
</style>
