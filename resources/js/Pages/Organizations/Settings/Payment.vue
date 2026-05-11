<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    organization: Object,
    settings: Object,
});

const form = useForm({
    bkash: {
        app_key: props.settings.bkash?.app_key || '',
        app_secret: props.settings.bkash?.app_secret || '',
        username: props.settings.bkash?.username || '',
        password: props.settings.bkash?.password || '',
        sandbox: props.settings.bkash?.sandbox ?? true,
        active: props.settings.bkash?.active ?? false,
    },
    sslcommerz: {
        store_id: props.settings.sslcommerz?.store_id || '',
        store_password: props.settings.sslcommerz?.store_password || '',
        sandbox: props.settings.sslcommerz?.sandbox ?? true,
        active: props.settings.sslcommerz?.active ?? false,
    }
});

const submit = () => {
    form.post(route('admin.settings.payment.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="__('Payment Settings')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Payment Gateway Configuration') }}
        </template>

        <div class="py-4">
            <form @submit.prevent="submit">
                <div class="row g-4">
                    
                    <!-- bKash Configuration -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-pink-subtle p-2 rounded-3">
                                            <img src="/images/gateways/bkash.png" alt="bKash" height="30">
                                        </div>
                                        <div>
                                            <h5 class="mb-0 fw-bold text-dark">bKash Checkout</h5>
                                            <small class="text-muted">Tokenized API Integration</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input custom-switch" type="checkbox" v-model="form.bkash.active" id="bkashActive">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4" :class="{ 'opacity-50': !form.bkash.active }">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('App Key') }}</label>
                                    <input type="text" v-model="form.bkash.app_key" class="form-control rounded-3 border-light-subtle shadow-sm" :disabled="!form.bkash.active">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('App Secret') }}</label>
                                    <input type="password" v-model="form.bkash.app_secret" class="form-control rounded-3 border-light-subtle shadow-sm" :disabled="!form.bkash.active">
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">{{ __('Username') }}</label>
                                        <input type="text" v-model="form.bkash.username" class="form-control rounded-3 border-light-subtle shadow-sm" :disabled="!form.bkash.active">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">{{ __('Password') }}</label>
                                        <input type="password" v-model="form.bkash.password" class="form-control rounded-3 border-light-subtle shadow-sm" :disabled="!form.bkash.active">
                                    </div>
                                </div>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="checkbox" v-model="form.bkash.sandbox" id="bkashSandbox" :disabled="!form.bkash.active">
                                    <label class="form-check-label small text-muted" for="bkashSandbox">{{ __('Enable Sandbox Mode') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SSLCommerz Configuration -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary-subtle p-2 rounded-3 text-primary">
                                            <img src="/images/gateways/sslcommerz.png" alt="SSLCommerz" height="30">
                                        </div>
                                        <div>
                                            <h5 class="mb-0 fw-bold text-dark">SSLCommerz</h5>
                                            <small class="text-muted">Cards & Net Banking</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input custom-switch" type="checkbox" v-model="form.sslcommerz.active" id="sslActive">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4" :class="{ 'opacity-50': !form.sslcommerz.active }">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('Store ID') }}</label>
                                    <input type="text" v-model="form.sslcommerz.store_id" class="form-control rounded-3 border-light-subtle shadow-sm" :disabled="!form.sslcommerz.active">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('Store Password') }}</label>
                                    <input type="password" v-model="form.sslcommerz.store_password" class="form-control rounded-3 border-light-subtle shadow-sm" :disabled="!form.sslcommerz.active">
                                </div>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="checkbox" v-model="form.sslcommerz.sandbox" id="sslSandbox" :disabled="!form.sslcommerz.active">
                                    <label class="form-check-label small text-muted" for="sslSandbox">{{ __('Enable Sandbox Mode') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Action Button -->
                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-dark px-5 py-3 rounded-pill fw-bold shadow-lg" :disabled="form.processing">
                        <i v-if="form.processing" class="spinner-border spinner-border-sm me-2"></i>
                        <i v-else class="bi bi-cloud-check-fill me-2"></i>
                        {{ __('Save Configuration') }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.bg-pink-subtle { background-color: #fce4ec; }
.card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 1rem 3rem rgba(0,0,0,.075)!important; }
.custom-switch { width: 3rem; height: 1.5rem; cursor: pointer; }
.custom-switch:checked { background-color: #198754; border-color: #198754; }
.form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1); }
</style>
