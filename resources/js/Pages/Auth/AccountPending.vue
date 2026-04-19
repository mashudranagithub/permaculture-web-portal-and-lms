<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <GuestLayout>
        <Head :title="__('Account Pending')" />

        <div class="text-center p-4">
            <div class="display-1 text-warning mb-4">
                <i class="bi bi-clock-history"></i>
            </div>
            
            <h2 class="fw-bold text-dark mb-3">{{ __('Account Under Review') }}</h2>
            
            <p class="text-muted mb-4">
                {{ __('Thank you for registering! Your account has been created and your email is verified. However, for security reasons, all new accounts must be manually approved by our team before you can access the platform.') }}
            </p>

            <div class="bg-light p-3 rounded-3 border border-warning-subtle mb-4 text-start">
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> {{ __('Registration Complete') }}</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> {{ __('Email Verified') }}</li>
                    <li><i class="bi bi-circle text-warning me-2"></i> {{ __('Waiting for Admin Approval') }}</li>
                </ul>
            </div>

            <p class="small text-muted mb-4">
                {{ __('You will receive an email once your account is active.') }}
            </p>

            <div class="d-grid">
                <Link :href="route('logout')" method="post" as="button" class="btn btn-outline-danger rounded-pill fw-bold">
                    {{ __('Log Out') }}
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
