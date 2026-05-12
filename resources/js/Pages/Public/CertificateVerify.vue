<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    certificate: Object
});
</script>

<template>
    <Head :title="__('Verify Certificate')" />

    <GuestLayout>
        <div class="text-center p-4">
            <div class="mb-4">
                <div class="bg-success-subtle d-inline-block p-3 rounded-circle mb-3">
                    <i class="bi bi-patch-check-fill text-success fs-1"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ __('Certificate Verified') }}</h3>
                <p class="text-muted small">{{ __('This document is an authentic certificate issued by Permaculture LMS.') }}</p>
            </div>

            <div class="card border-0 bg-light rounded-4 overflow-hidden mb-4 text-start">
                <div class="card-body p-4">
                    <div class="space-y-4">
                        <div class="border-bottom pb-3">
                            <label class="text-muted small d-block mb-1">{{ __('Certificate Number') }}</label>
                            <span class="fw-bold text-dark fs-5">{{ certificate.no }}</span>
                        </div>
                        
                        <div class="border-bottom pb-3 mt-3">
                            <label class="text-muted small d-block mb-1">{{ __('Student Name') }}</label>
                            <span class="fw-bold text-dark fs-5">{{ certificate.student }}</span>
                        </div>

                        <div class="border-bottom pb-3 mt-3">
                            <label class="text-muted small d-block mb-1">{{ __('Course Title') }}</label>
                            <span class="fw-bold text-dark fs-5">{{ certificate.course }}</span>
                        </div>

                        <div class="border-bottom pb-3 mt-3">
                            <label class="text-muted small d-block mb-1">{{ __('Issuing Organization') }}</label>
                            <span class="fw-bold text-dark fs-5">{{ certificate.organization }}</span>
                        </div>

                        <div class="mt-3">
                            <label class="text-muted small d-block mb-1">{{ __('Date of Issue') }}</label>
                            <span class="fw-bold text-dark fs-5">{{ certificate.issue_date }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-success border-0 rounded-4 x-small text-start d-flex align-items-center gap-3">
                <i class="bi bi-shield-lock-fill fs-4"></i>
                <div>
                    <span class="fw-bold d-block">{{ __('Secure Verification') }}</span>
                    {{ __('This certificate is digitally signed and stored in our secure database.') }}
                </div>
            </div>

            <div class="mt-5">
                <a href="/" class="btn btn-dark rounded-pill px-4 btn-sm fw-bold shadow-sm">
                    {{ __('Back to Homepage') }}
                </a>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.x-small { font-size: 0.8rem; }
.bg-success-subtle { background-color: #d1e7dd; }
</style>
