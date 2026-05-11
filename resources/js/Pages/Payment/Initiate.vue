<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    enrollment: Object,
    gateways: Object,
});

const form = useForm({});

const submitMock = () => {
    form.post(route('payments.mock-success', props.enrollment.id));
};

const submitBkash = () => {
    form.post(route('bkash.pay', props.enrollment.id));
};
</script>

<template>
    <Head :title="__('Secure Checkout')" />

    <AuthenticatedLayout>
        <div class="py-5 bg-light-subtle min-vh-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        
                        <!-- Checkout Progress Header -->
                        <div class="d-flex justify-content-between align-items-center mb-5 px-4">
                            <div class="text-center">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-2 shadow-sm mx-auto" style="width: 32px; height: 32px;">1</div>
                                <span class="small fw-bold text-dark">{{ __('Enrollment') }}</span>
                            </div>
                            <div class="flex-grow-1 mx-3 border-bottom border-2 border-success-subtle mb-4"></div>
                            <div class="text-center">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-2 shadow-lg mx-auto border border-4 border-white" style="width: 40px; height: 40px; margin-top: -4px;">2</div>
                                <span class="small fw-bold text-success">{{ __('Payment') }}</span>
                            </div>
                            <div class="flex-grow-1 mx-3 border-bottom border-2 border-light mb-4"></div>
                            <div class="text-center opacity-50">
                                <div class="rounded-circle bg-white text-muted d-flex align-items-center justify-content-center mb-2 shadow-sm mx-auto border" style="width: 32px; height: 32px;">3</div>
                                <span class="small fw-bold text-muted">{{ __('Success') }}</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Left Column: Payment Methods -->
                            <div class="col-md-7">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                    <div class="card-body p-5">
                                        <h4 class="fw-bold text-dark mb-1">{{ __('Select Payment Method') }}</h4>
                                        <p class="text-muted small mb-5">{{ __('Choose your preferred way to pay securely.') }}</p>

                                        <!-- Real Gateways -->
                                        <div v-if="gateways.bkash || gateways.sslcommerz" class="space-y-4">
                                            
                                            <!-- bKash Button -->
                                            <div v-if="gateways.bkash" 
                                                @click="submitBkash"
                                                class="payment-card p-4 rounded-4 border-2 transition-all d-flex align-items-center gap-4 mb-3 position-relative"
                                                :class="[form.processing ? 'opacity-50 pointer-events-none' : 'border-pink-subtle bg-white hover-scale shadow-sm']"
                                            >
                                                <div class="bg-pink-subtle p-3 rounded-3">
                                                    <img src="/images/gateways/bkash.png" alt="bKash" height="35">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-0 text-dark">{{ __('bKash Checkout') }}</h6>
                                                    <small class="text-muted">{{ __('Pay securely with your bKash account') }}</small>
                                                </div>
                                                <i class="bi bi-chevron-right text-muted"></i>
                                                <div class="position-absolute top-0 end-0 p-2 me-2">
                                                    <span class="badge bg-success-subtle text-success rounded-pill x-small px-2">Instant</span>
                                                </div>
                                            </div>

                                            <!-- SSLCommerz/Cards Button -->
                                            <div v-if="gateways.sslcommerz"
                                                class="payment-card p-4 rounded-4 border-2 border-light bg-light opacity-75 d-flex align-items-center gap-4 mb-3"
                                            >
                                                <div class="bg-white p-3 rounded-3 shadow-sm text-primary">
                                                    <img src="/images/gateways/sslcommerz.png" alt="SSLCommerz" height="25">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-0 text-muted">{{ __('Cards & Net Banking') }}</h6>
                                                    <small class="text-muted">{{ __('Visa, MasterCard, and Local Banks') }}</small>
                                                </div>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill x-small px-2">Soon</span>
                                            </div>
                                        </div>

                                        <!-- No Gateways Empty State -->
                                        <div v-else class="text-center py-5 bg-light rounded-4">
                                            <div class="bg-warning-subtle d-inline-block p-3 rounded-circle mb-3">
                                                <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                                            </div>
                                            <h5 class="fw-bold">{{ __('Payment Currently Unavailable') }}</h5>
                                            <p class="text-muted small px-4">{{ __('This organization has not enabled any payment gateways. Please contact their support for assistance.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Developer Mock (Admin Only) -->
                                <div v-if="$page.props.auth.user.roles.includes('super-admin')" class="card border-0 bg-info-subtle shadow-sm rounded-4 overflow-hidden border-start border-4 border-info">
                                    <div class="card-body p-4">
                                        <div class="d-flex gap-3">
                                            <i class="bi bi-bug-fill text-info fs-4"></i>
                                            <div>
                                                <h6 class="fw-bold text-info mb-1">{{ __('Dev Sandbox') }}</h6>
                                                <p class="small text-info-emphasis mb-3 opacity-75">{{ __('As a Super Admin, you can bypass the payment gateway for testing.') }}</p>
                                                <button @click="submitMock" class="btn btn-info text-white btn-sm px-4 rounded-pill fw-bold" :disabled="form.processing">
                                                    {{ __('Simulate Success') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Order Summary -->
                            <div class="col-md-5">
                                <div class="card border-0 shadow-lg rounded-4 overflow-hidden sticky-top" style="top: 2rem;">
                                    <div class="bg-dark p-4 text-white">
                                        <h5 class="fw-bold mb-0">{{ __('Order Summary') }}</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <!-- Course Preview -->
                                        <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                                            <div class="bg-success-subtle rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; min-width: 60px;">
                                                <i class="bi bi-book text-success fs-3"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold text-dark text-truncate mb-1">{{ enrollment.course_title }}</h6>
                                                <span class="badge bg-light text-dark border rounded-pill x-small">{{ enrollment.batch_title }}</span>
                                            </div>
                                        </div>

                                        <!-- Pricing Details -->
                                        <div class="space-y-3 mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">{{ __('Enrollment No') }}</span>
                                                <span class="fw-bold small text-dark">{{ enrollment.no }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">{{ __('Subtotal') }}</span>
                                                <span class="fw-bold text-dark">BDT {{ enrollment.amount }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">{{ __('Platform Fee') }}</span>
                                                <span class="text-success small fw-bold">{{ __('Free') }}</span>
                                            </div>
                                        </div>

                                        <!-- Total -->
                                        <div class="bg-light rounded-4 p-4 mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="fw-bold text-dark mb-0">{{ __('Total Amount') }}</h5>
                                                <div class="text-end">
                                                    <h4 class="fw-bold text-success mb-0">BDT {{ enrollment.amount }}</h4>
                                                    <small class="text-muted x-small">{{ __('Inclusive of all taxes') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <Link :href="route('enrollments.my-courses')" class="text-muted small text-decoration-none hover-text-dark transition-all d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-arrow-left"></i> {{ __('Cancel and Return') }}
                                            </Link>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light border-0 py-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2 text-muted x-small">
                                            <i class="bi bi-shield-lock-fill text-success"></i>
                                            {{ __('Secure 256-bit SSL Encrypted Payment') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.x-small { font-size: 0.7rem; }
.hover-scale { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
.hover-scale:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1) !important; border-color: #f06292 !important; }
.payment-card { border: 2px solid transparent; }
.bg-pink-subtle { background-color: #fce4ec; }
.bg-light-subtle { background-color: #f8f9fa; }
.transition-all { transition: all 0.2s ease; }
.hover-text-dark:hover { color: #212529 !important; }
.grayscale { filter: grayscale(1); }
.space-y-4 > * + * { margin-top: 1rem; }
.space-y-3 > * + * { margin-top: 0.75rem; }

/* Custom Progress Pulse */
.border-success-subtle { border-color: #d1e7dd !important; }
</style>
