<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    teacher: Object
});
</script>

<template>
    <Head :title="__('Teacher Profile') + ' - ' + teacher.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center gap-3">
                <Link :href="route('admin.teachers.index')" class="btn btn-outline-success btn-sm rounded-circle p-1" style="width: 32px; height: 32px;">
                    <i class="bi bi-arrow-left"></i>
                </Link>
                {{ __('Teacher Profile') }}
            </div>
        </template>

        <div class="row g-4 justify-content-center">
            <!-- Profile Card -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-5 text-center">
                        <img :src="teacher.avatar_url" class="rounded-circle shadow mb-4 border border-3 border-success-subtle object-fit-cover" style="width: 140px; height: 140px;" alt="Avatar">
                        <h3 class="fw-bold mb-1 text-dark">{{ teacher.name }}</h3>
                        <p class="text-muted small mb-4">{{ teacher.email }}</p>
                        
                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                            <span v-for="role in teacher.roles" :key="role.id" class="badge bg-success text-white px-3 py-2 rounded-pill small">
                                {{ role.name }}
                            </span>
                        </div>
                        
                        <hr class="opacity-10 my-4">
                        
                        <div class="text-start mt-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="text-muted x-small text-uppercase fw-bold d-block mb-1">{{ __('Organization') }}</label>
                                    <span class="fw-bold text-dark">{{ teacher.organization?.name || __('System / LMS') }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted x-small text-uppercase fw-bold d-block mb-1">{{ __('Registered At') }}</label>
                                    <span class="fw-bold text-dark">{{ new Date(teacher.created_at).toLocaleDateString() }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted x-small text-uppercase fw-bold d-block mb-1">{{ __('Verification Status') }}</label>
                                    <span v-if="teacher.email_verified_at" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>{{ __('Verified') }}
                                    </span>
                                    <span v-else class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>{{ __('Unverified') }}
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted x-small text-uppercase fw-bold d-block mb-1">{{ __('Account Status') }}</label>
                                    <span v-if="teacher.is_approved" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-shield-check me-1"></i>{{ __('Active / Approved') }}
                                    </span>
                                    <span v-else class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-shield-x me-1"></i>{{ __('Suspended / Pending') }}
                                    </span>
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
.object-fit-cover { object-fit: cover; }
.badge { border-radius: 4px; }
</style>
