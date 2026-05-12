<script setup>
import { ref, computed, watch } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    org_name: '',
    org_slug: '',
    org_email: '',
    admin_name: '',
    admin_email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);

// Auto-generate slug from organization name
watch(() => form.org_name, (value) => {
    form.org_slug = value
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
});

const submit = () => {
    form.post(route('organization.register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="__('Partner with Us')" />

        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">{{ __('Organization Registration') }}</h2>
            <p class="text-muted small">{{ __('Apply to host your courses on our platform') }}</p>
        </div>

        <form @submit.prevent="submit">
            <!-- Organization Info Header -->
            <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                <div class="bg-success-subtle text-success rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-buildings"></i>
                </div>
                <h5 class="mb-0 fw-bold text-dark fs-6">{{ __('Organization Details') }}</h5>
            </div>

            <div class="mb-3">
                <label for="org_name" class="form-label fw-bold small text-muted text-uppercase">{{ __('Organization Name') }}</label>
                <input id="org_name" v-model="form.org_name" type="text" class="form-control rounded-3" :class="{ 'is-invalid': form.errors.org_name }" required />
                <div v-if="form.errors.org_name" class="invalid-feedback">{{ form.errors.org_name }}</div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="org_slug" class="form-label fw-bold small text-muted text-uppercase">{{ __('Custom URL Slug') }}</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted small">/org/</span>
                        <input id="org_slug" v-model="form.org_slug" type="text" class="form-control" :class="{ 'is-invalid': form.errors.org_slug }" required />
                    </div>
                    <div v-if="form.errors.org_slug" class="text-danger small mt-1">{{ form.errors.org_slug }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="org_email" class="form-label fw-bold small text-muted text-uppercase">{{ __('Business Email') }}</label>
                    <input id="org_email" v-model="form.org_email" type="email" class="form-control rounded-3" :class="{ 'is-invalid': form.errors.org_email }" required />
                    <div v-if="form.errors.org_email" class="invalid-feedback">{{ form.errors.org_email }}</div>
                </div>
            </div>

            <!-- Admin Info Header -->
            <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                <div class="bg-primary-subtle text-primary rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h5 class="mb-0 fw-bold text-dark fs-6">{{ __('Administrator Account') }}</h5>
            </div>

            <div class="mb-3">
                <label for="admin_name" class="form-label fw-bold small text-muted text-uppercase">{{ __('Admin Full Name') }}</label>
                <input id="admin_name" v-model="form.admin_name" type="text" class="form-control rounded-3" :class="{ 'is-invalid': form.errors.admin_name }" required />
                <div v-if="form.errors.admin_name" class="invalid-feedback">{{ form.errors.admin_name }}</div>
            </div>

            <div class="mb-3">
                <label for="admin_email" class="form-label fw-bold small text-muted text-uppercase">{{ __('Login Email') }}</label>
                <input id="admin_email" v-model="form.admin_email" type="email" class="form-control rounded-3" :class="{ 'is-invalid': form.errors.admin_email }" required />
                <div v-if="form.errors.admin_email" class="invalid-feedback">{{ form.errors.admin_email }}</div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="password" class="form-label fw-bold small text-muted text-uppercase">{{ __('Password') }}</label>
                    <div class="input-group">
                        <input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password" class="form-control rounded-start-3" :class="{ 'is-invalid': form.errors.password }" required />
                        <button @click="showPassword = !showPassword" class="btn btn-outline-secondary border-light-subtle px-3 rounded-end-3" type="button">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    <div v-if="form.errors.password" class="text-danger small mt-1">{{ form.errors.password }}</div>
                </div>
                <div class="col-md-12 mb-4">
                    <label for="password_confirmation" class="form-label fw-bold small text-muted text-uppercase">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="form-control rounded-3" required />
                </div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm py-3" :disabled="form.processing">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ __('Submit Application') }}
                </button>
            </div>

            <div class="mt-4 pt-2 text-center border-top">
                <p class="small text-muted">{{ __('By applying, you agree to our terms of partnership and content guidelines.') }}</p>
                <Link :href="route('login')" class="text-success fw-bold text-decoration-none small">{{ __('Back to Login') }}</Link>
            </div>
        </form>
    </GuestLayout>
</template>
