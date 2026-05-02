<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    org_name:        '',
    org_email:       '',
    org_phone:       '',
    org_address:     '',
    org_website:     '',
    org_description: '',
    org_logo:        null,
    admin_name:      '',
    admin_email:     '',
    password:        '',
    password_confirmation: '',
});

const handleLogo = (e) => {
    form.org_logo = e.target.files[0] || null;
};

const submit = () => {
    form.post(route('org.register.store'), {
        forceFormData: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :wide="true">
        <Head title="Register Your Organization" />

        <div class="text-center mb-4">
            <div class="org-icon-ring mb-3">
                <i class="bx bx-buildings text-success" style="font-size: 2rem;"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1">Register Your Organization</h2>
            <p class="text-muted small">
                Submit your details for review. Once verified, you'll get full platform access.
            </p>
        </div>

        <form @submit.prevent="submit" enctype="multipart/form-data">

            <!-- ── Section: Organization Info ─────────────────────────── -->
            <div class="section-label">
                <i class="bx bx-buildings me-2 text-success"></i>Organization Information
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Organization Name <span class="text-danger">*</span></label>
                    <input v-model="form.org_name" type="text" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.org_name }" required autofocus>
                    <div v-if="form.errors.org_name" class="invalid-feedback">{{ form.errors.org_name }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Official Email <span class="text-danger">*</span></label>
                    <input v-model="form.org_email" type="email" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.org_email }" required>
                    <div v-if="form.errors.org_email" class="invalid-feedback">{{ form.errors.org_email }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Phone</label>
                    <input v-model="form.org_phone" type="text" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.org_phone }">
                    <div v-if="form.errors.org_phone" class="invalid-feedback">{{ form.errors.org_phone }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Website</label>
                    <input v-model="form.org_website" type="url" class="form-control form-control-lg rounded-3"
                        placeholder="https://" :class="{ 'is-invalid': form.errors.org_website }">
                    <div v-if="form.errors.org_website" class="invalid-feedback">{{ form.errors.org_website }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Address</label>
                    <input v-model="form.org_address" type="text" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.org_address }">
                    <div v-if="form.errors.org_address" class="invalid-feedback">{{ form.errors.org_address }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">About Your Organization</label>
                    <textarea v-model="form.org_description" class="form-control rounded-3" rows="3"
                        placeholder="Briefly describe your organization..." :class="{ 'is-invalid': form.errors.org_description }"></textarea>
                    <div v-if="form.errors.org_description" class="invalid-feedback">{{ form.errors.org_description }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Logo <span class="text-muted fw-normal">(optional, max 2MB)</span></label>
                    <input @change="handleLogo" type="file" class="form-control form-control-lg rounded-3" accept="image/*"
                        :class="{ 'is-invalid': form.errors.org_logo }">
                    <div v-if="form.errors.org_logo" class="invalid-feedback">{{ form.errors.org_logo }}</div>
                </div>
            </div>

            <!-- ── Section: Admin Account ──────────────────────────────── -->
            <div class="section-label mt-4">
                <i class="bx bx-user me-2 text-success"></i>Your Admin Account
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Your Full Name <span class="text-danger">*</span></label>
                    <input v-model="form.admin_name" type="text" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.admin_name }" required>
                    <div v-if="form.errors.admin_name" class="invalid-feedback">{{ form.errors.admin_name }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Admin Email <span class="text-danger">*</span></label>
                    <input v-model="form.admin_email" type="email" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.admin_email }" required>
                    <div v-if="form.errors.admin_email" class="invalid-feedback">{{ form.errors.admin_email }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                    <input v-model="form.password" type="password" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.password }" required autocomplete="new-password">
                    <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Confirm Password <span class="text-danger">*</span></label>
                    <input v-model="form.password_confirmation" type="password" class="form-control form-control-lg rounded-3"
                        :class="{ 'is-invalid': form.errors.password_confirmation }" required>
                    <div v-if="form.errors.password_confirmation" class="invalid-feedback">{{ form.errors.password_confirmation }}</div>
                </div>
            </div>

            <!-- ── Info Notice ─────────────────────────────────────────── -->
            <div class="alert alert-success-subtle d-flex gap-2 align-items-start mb-4 rounded-3 border border-success-subtle">
                <i class="bx bx-info-circle text-success fs-5 mt-1"></i>
                <div class="small text-success-emphasis">
                    <strong>What happens next?</strong><br>
                    You will receive an email to verify your address. After verification, our team will review your
                    organization registration and notify you once it's approved.
                </div>
            </div>

            <!-- ── Submit ──────────────────────────────────────────────── -->
            <div class="d-grid">
                <button class="btn btn-success btn-lg rounded-pill fw-bold py-3 shadow-sm" :disabled="form.processing">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="bx bx-send me-2"></i>
                    Submit Registration
                </button>
            </div>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted small mb-1">Already have an account?
                    <Link :href="route('login')" class="text-success fw-bold text-decoration-none">Log In</Link>
                </p>
                <p class="text-muted small mb-0">Registering as a student?
                    <Link :href="route('register')" class="text-success fw-bold text-decoration-none">Sign Up Here</Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
}
.section-label {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}
.org-icon-ring {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #d1fae5;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.alert-success-subtle {
    background-color: #f0fdf4;
}
</style>
