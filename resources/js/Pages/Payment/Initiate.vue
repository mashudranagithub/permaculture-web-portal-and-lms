<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    enrollment: Object
});

const form = useForm({});

const submitMock = () => {
    form.post(route('payments.mock-success', props.enrollment.id));
};
</script>

<template>
    <Head :title="__('Complete Payment')" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-success-subtle">
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <div class="bg-success-subtle d-inline-block p-4 rounded-circle mb-4">
                                <i class="bi bi-shield-check fs-1 text-success"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-dark">{{ __('Secure Checkout') }}</h2>
                            <p class="text-muted">{{ __('Please complete your payment to start learning.') }}</p>
                        </div>

                        <div class="bg-light rounded-4 p-6 mb-8 border border-dashed border-success">
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-white">
                                <span class="text-muted fw-medium">{{ __('Enrollment No') }}</span>
                                <span class="fw-bold text-dark">{{ enrollment.no }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-white">
                                <span class="text-muted fw-medium">{{ __('Course') }}</span>
                                <span class="fw-bold text-dark text-end">{{ enrollment.course_title }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-white">
                                <span class="text-muted fw-medium">{{ __('Batch') }}</span>
                                <span class="fw-bold text-dark">{{ enrollment.batch_title }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2">
                                <span class="fs-5 fw-bold text-dark">{{ __('Total Amount') }}</span>
                                <span class="fs-4 fw-bold text-success">BDT {{ enrollment.amount }}</span>
                            </div>
                        </div>

                        <!-- Real Gateways would go here -->
                        <div class="mb-8">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-3">{{ __('Select Payment Method') }}</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-4 border-2 border-success rounded-4 text-center cursor-pointer bg-success-subtle bg-opacity-10">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/BKash_Logo.svg/2560px-BKash_Logo.svg.png" alt="bKash" class="img-fluid" style="height: 40px; filter: grayscale(1);">
                                        <div class="mt-3 small fw-bold text-muted opacity-50">{{ __('Coming Soon') }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-4 border-2 border-secondary border-opacity-10 rounded-4 text-center opacity-50">
                                        <i class="bi bi-credit-card fs-1 text-muted"></i>
                                        <div class="mt-2 small fw-bold">{{ __('Credit/Debit Card') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Development Mock Button -->
                        <div class="alert alert-info border-0 rounded-4 p-4 mb-8">
                            <div class="d-flex gap-3">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('Development Mode') }}</h6>
                                    <p class="mb-0 small opacity-75">{{ __('Click the button below to simulate a successful payment transaction.') }}</p>
                                </div>
                            </div>
                            <button @click="submitMock" class="btn btn-dark w-100 mt-4 py-3 rounded-pill fw-bold shadow-lg" :disabled="form.processing">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ __('Simulate Successful Payment') }}
                            </button>
                        </div>

                        <div class="text-center">
                            <Link :href="route('courses.index')" class="text-muted small text-decoration-none hover-text-dark transition-all">
                                <i class="bi bi-arrow-left me-1"></i> {{ __('Cancel and return to courses') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-pointer { cursor: pointer; }
.transition-all { transition: all 0.2s ease; }
.hover-text-dark:hover { color: #212529 !important; }
</style>
