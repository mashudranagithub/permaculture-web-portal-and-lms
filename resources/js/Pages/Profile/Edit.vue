<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head :title="__('Profile')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Profile') }}
        </template>

        <div class="row g-4">
            <div class="col-xl-8 mx-auto">
                <!-- Profile Information Card -->
                <div class="card border-0 shadow-sm rounded-1 mb-4 animate__animated animate__fadeInUp">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-person-badge text-success me-2"></i>{{ __('Profile Information') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <!-- Password Update Card -->
                <div class="card border-0 shadow-sm rounded-1 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-shield-lock text-success me-2"></i>{{ __('Update Password') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <!-- Account Security/Danger Zone Card -->
                <div class="card border-0 shadow-sm rounded-1 mb-4 border-start border-4 border-danger animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Danger Zone') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
