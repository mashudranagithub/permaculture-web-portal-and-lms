<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    enrollments: Array
});
</script>

<template>
    <Head :title="__('My Courses')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Learning Dashboard') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 p-6 border-l-4 border-success">
                    <h3 class="text-2xl font-bold text-dark mb-2">{{ __('Welcome back,') }} {{ $page.props.auth.user.name }}!</h3>
                    <p class="text-muted">{{ __('You have') }} {{ enrollments.length }} {{ __('active enrollments.') }}</p>
                </div>

                <!-- Course Grid -->
                <div v-if="enrollments.length > 0" class="row g-4">
                    <div v-for="enr in enrollments" :key="enr.id" class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift transition-all">
                            <div class="position-relative">
                                <img :src="enr.course.image_url || '/images/course-placeholder.jpg'" class="card-img-top object-fit-cover" style="height: 200px;" :alt="enr.course.title">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm" :class="enr.payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark'">
                                        {{ enr.payment_status === 'paid' ? __('Enrolled') : __('Payment Pending') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-1">{{ enr.course.title }}</h5>
                                <p class="text-muted small mb-3"><i class="bi bi-people-fill me-1"></i> {{ enr.batch.title }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted x-small">
                                        {{ __('Enrolled on') }}: <span class="fw-medium">{{ enr.enrolled_at || '--' }}</span>
                                    </div>
                                    <template v-if="enr.payment_status === 'paid'">
                                        <Link :href="route('enrollments.learn', enr.course.id)" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                            {{ __('Continue Learning') }}
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <Link :href="route('payments.initiate', { enrollment_id: enr.id })" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                                            {{ __('Pay Now') }}
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-10 bg-white rounded-lg shadow-sm">
                    <div class="mb-4">
                        <i class="bi bi-journal-x fs-1 text-muted opacity-25"></i>
                    </div>
                    <h4 class="fw-bold">{{ __('No courses found') }}</h4>
                    <p class="text-muted">{{ __("You haven't enrolled in any courses yet.") }}</p>
                    <Link :href="route('courses.index')" class="btn btn-success mt-4 rounded-pill px-5 py-2 fw-bold">
                        {{ __('Browse Courses') }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.transition-all {
    transition: all 0.3s ease;
}
.x-small {
    font-size: 0.75rem;
}
.object-fit-cover {
    object-fit: cover;
}
</style>
