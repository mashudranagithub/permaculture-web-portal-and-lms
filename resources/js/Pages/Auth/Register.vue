<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="__('Sign Up')" />

        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">{{ __('Sign Up') }}</h2>
            <p class="text-muted small">{{ __('Join the Permaculture Methods community') }}</p>
        </div>

        <form @submit.prevent="submit">
            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label fw-bold small text-uppercase tracking-wide text-muted">{{ __('Full Name') }}</label>
                <input
                    id="name"
                    type="text"
                    class="form-control form-control-lg rounded-3"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    :class="{ 'is-invalid': form.errors.name }"
                />
                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-bold small text-uppercase tracking-wide text-muted">{{ __('Email Address') }}</label>
                <input
                    id="email"
                    type="email"
                    class="form-control form-control-lg rounded-3"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                    :class="{ 'is-invalid': form.errors.password }"
                />
                <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-bold small text-uppercase tracking-wide text-muted">{{ __('Confirm Password') }}</label>
                <input
                    id="password_confirmation"
                    type="password"
                    class="form-control form-control-lg rounded-3"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    :class="{ 'is-invalid': form.errors.password_confirmation }"
                />
                <div v-if="form.errors.password_confirmation" class="invalid-feedback">{{ form.errors.password_confirmation }}</div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <button
                    class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm py-3"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ __('Sign Up') }}
                </button>
            </div>

            <div class="mt-4 pt-2 text-center border-top">
                <div class="mt-2 text-muted small">
                    {{ __('Already have an account?') }} 
                    <Link :href="route('login')" class="text-success fw-bold text-decoration-none ms-1">{{ __('Log in') }}</Link>
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
