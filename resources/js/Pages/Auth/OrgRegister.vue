<script setup>
import { ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    org_name: '',
    org_slug: '',
    org_email: '',
    org_phone: '',
    org_logo: null,
    admin_name: '',
    admin_email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const logoPreview = ref(null);

// Auto-generate slug from organization name
watch(() => form.org_name, (value) => {
    form.org_slug = value
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
});

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.org_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('org.register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="__('Partner Registration')" />

    <div class="min-vh-100 bg-light py-5 px-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
        <div class="mx-auto" style="max-width: 1200px;">
            <!-- Logo & Brand Header -->
            <div class="text-center mb-5 animate-up">
                <Link href="/" class="text-decoration-none">
                    <img src="/favicon.png" alt="Logo" class="rounded-circle shadow-sm mb-3" style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #fff;">
                    <h2 class="fw-bold text-success text-uppercase tracking-wider fs-4">{{ __('Regenerative Systems') }}</h2>
                </Link>
                <h1 class="display-5 fw-bold text-dark mt-4 mb-2">{{ __('Partner Registration') }}</h1>
                <p class="text-muted lead">{{ __('Complete the form below to register your academy on our professional platform.') }}</p>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate-up">
                <!-- Top Accent Bar -->
                <div class="bg-success py-3 px-4 d-flex justify-content-between align-items-center text-white">
                    <span class="fw-bold text-uppercase tracking-widest small">{{ __('Registration Application') }}</span>
                    <span class="small opacity-75">{{ __('Step 1 of 1') }}</span>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <form @submit.prevent="submit">
                        <div class="row g-5">
                            <!-- Left Column: Organization -->
                            <div class="col-lg-6 border-end-lg">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="bg-success text-white rounded-3 p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-buildings"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-0 fs-5">{{ __('Organization Details') }}</h4>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Organization Name') }} <span class="text-danger">*</span></label>
                                    <input v-model="form.org_name" type="text" class="form-control form-control-lg rounded-3 border-light-subtle bg-light shadow-none" placeholder="e.g. Eco-Village Academy" required />
                                    <div v-if="form.errors.org_name" class="text-danger small mt-1">{{ form.errors.org_name }}</div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Organization Business Email') }} <span class="text-danger">*</span></label>
                                        <input v-model="form.org_email" type="email" class="form-control rounded-3 border-light-subtle bg-light shadow-none" placeholder="contact@org.com" required />
                                        <div v-if="form.errors.org_email" class="text-danger small mt-1">{{ form.errors.org_email }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Contact Phone Number') }} <span class="text-danger">*</span></label>
                                        <input v-model="form.org_phone" type="text" class="form-control rounded-3 border-light-subtle bg-light shadow-none" placeholder="+880..." required />
                                        <div v-if="form.errors.org_phone" class="text-danger small mt-1">{{ form.errors.org_phone }}</div>
                                    </div>
                                </div>

                                <div class="p-3 bg-light rounded-4 border border-dashed border-secondary border-opacity-25 mt-5">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="flex-grow-1">
                                            <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Organization Logo') }} <span class="text-danger">*</span></label>
                                            <input type="file" @change="handleLogoChange" class="form-control form-control-sm border-light-subtle shadow-none" accept="image/*" required />
                                        </div>
                                        <div class="logo-preview-area">
                                            <div v-if="logoPreview" class="border-2 border-white shadow-sm rounded-3 overflow-hidden bg-white" style="width: 100px; height: 75px; display: flex; align-items: center; justify-content: center;">
                                                <img :src="logoPreview" class="img-fluid" alt="Preview" />
                                            </div>
                                            <div v-else class="border border-secondary border-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-muted bg-white bg-opacity-50" style="width: 100px; height: 75px;">
                                                <i class="bi bi-image fs-3"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Admin -->
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="bg-primary text-white rounded-3 p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-0 fs-5">{{ __('Primary Administrator Account') }}</h4>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Administrator Name') }} <span class="text-danger">*</span></label>
                                    <input v-model="form.admin_name" type="text" class="form-control form-control-lg rounded-3 border-light-subtle bg-light shadow-none" placeholder="Full name of admin" required />
                                    <div v-if="form.errors.admin_name" class="text-danger small mt-1">{{ form.errors.admin_name }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Login Email Address') }} <span class="text-danger">*</span></label>
                                    <input v-model="form.admin_email" type="email" class="form-control form-control-lg rounded-3 border-light-subtle bg-light shadow-none" placeholder="admin@email.com" required />
                                    <div v-if="form.errors.admin_email" class="text-danger small mt-1">{{ form.errors.admin_email }}</div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Create Password') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" class="form-control border-light-subtle bg-light shadow-none" required />
                                            <button @click="showPassword = !showPassword" class="btn btn-outline-secondary border-light-subtle px-3" type="button">
                                                <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase tracking-wide">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                        <input v-model="form.password_confirmation" type="password" class="form-control border-light-subtle bg-light shadow-none" required />
                                    </div>
                                    <div v-if="form.errors.password" class="col-12 text-danger small mt-1">{{ form.errors.password }}</div>
                                </div>

                                <div class="mt-auto pt-4">
                                    <div class="p-3 bg-light rounded-4 border border-light-subtle text-muted small">
                                        <i class="bi bi-shield-check text-success me-2 fs-5"></i>
                                        {{ __('Your administrator account will have full control over the academy dashboard upon approval.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="mt-5 pt-4 border-top text-center">
                            <div class="mx-auto" style="max-width: 500px;">
                                <button class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-lg py-3 transition-hover mb-4" :disabled="form.processing">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ __('Submit Partner Application') }}
                                </button>
                                
                                <p class="text-muted small mb-0">{{ __('Already have an account?') }} <Link :href="route('login')" class="text-success fw-bold text-decoration-none">{{ __('Login here') }}</Link></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <p class="text-muted small">&copy; 2026 Regenerative Systems. {{ __('All Rights Reserved.') }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

.transition-hover { transition: all 0.2s ease; }
.transition-hover:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(25, 135, 84, 0.25) !important; }

@media (min-width: 992px) {
    .border-end-lg {
        border-right: 1px solid #dee2e6 !important;
    }
}

.form-control-lg {
    padding: 0.8rem 1rem;
    font-size: 1rem;
}

.tracking-wider { letter-spacing: 0.1em; }
.tracking-widest { letter-spacing: 0.2em; }
</style>
