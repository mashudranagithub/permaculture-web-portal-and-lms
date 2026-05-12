<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    certificates: Array
});
</script>

<template>
    <Head :title="__('My Certificates')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Certificates') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="certificates.length > 0" class="row g-4">
                    <div v-for="cert in certificates" :key="cert.id" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white hover-lift transition-all">
                            <div class="card-body p-4 text-center">
                                <div class="mb-4">
                                    <div class="bg-success-subtle d-inline-block p-4 rounded-circle mb-3">
                                        <i class="bi bi-patch-check-fill text-success fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">{{ cert.metadata?.course_title || cert.course?.title }}</h5>
                                    <p class="text-muted small mb-0">{{ cert.metadata?.organization_name || cert.organization?.name }}</p>
                                </div>

                                <div class="p-3 bg-light rounded-3 mb-4 text-start">
                                    <div class="mb-2">
                                        <span class="text-muted x-small d-block">{{ __('Certificate No') }}</span>
                                        <span class="fw-bold small">{{ cert.certificate_no }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted x-small d-block">{{ __('Issued On') }}</span>
                                        <span class="fw-bold small">{{ cert.issue_date }}</span>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a :href="route('certificates.download', cert.id)" target="_blank" class="btn btn-success rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-download me-2"></i> {{ __('Download PDF') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-5 bg-white rounded-4 shadow-sm border-0">
                    <div class="mb-4">
                        <div class="bg-light-soft d-inline-block p-4 rounded-circle">
                            <i class="bi bi-patch-exclamation fs-1 text-muted opacity-50"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark">{{ __('No certificates found') }}</h4>
                    <p class="text-muted">{{ __("You haven't earned any certificates yet. Keep learning!") }}</p>
                    <Link :href="route('enrollments.my-courses')" class="btn btn-success mt-4 rounded-pill px-5 py-2 fw-bold shadow-sm transition-all hover-lift">
                        {{ __('View My Courses') }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}
.transition-all { transition: all 0.3s ease; }
.x-small { font-size: 0.7rem; }
.bg-success-subtle { background-color: #d1e7dd; }
.bg-light-soft { background-color: #f8f9fa; }
</style>
