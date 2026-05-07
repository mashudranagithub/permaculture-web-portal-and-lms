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
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift transition-all bg-white">
                            <div class="position-relative">
                                <img :src="enr.course.image_url || '/images/course-placeholder.jpg'" class="card-img-top object-fit-cover" style="height: 180px;" :alt="enr.course.title">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm" :class="enr.payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark'">
                                        {{ enr.payment_status === 'paid' ? __('Enrolled') : __('Payment Pending') }}
                                    </span>
                                </div>
                                <!-- Progress Overlay -->
                                <div v-if="enr.payment_status === 'paid'" class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark-transparent">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-white x-small fw-bold">{{ enr.progress_percent }}% {{ __('Complete') }}</span>
                                    </div>
                                    <div class="progress bg-white-20" style="height: 4px;">
                                        <div class="progress-bar bg-success" :style="{ width: enr.progress_percent + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-1 text-dark">{{ enr.course.title }}</h5>
                                <p class="text-muted small mb-3"><i class="bi bi-people-fill me-1"></i> {{ enr.batch.title }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted x-small">
                                        <div class="mb-1">{{ __('Enrolled') }}: <span class="fw-medium">{{ enr.enrolled_at || '--' }}</span></div>
                                        <div v-if="enr.payment_status === 'paid'">{{ enr.completed_count }}/{{ enr.total_count }} {{ __('Lessons') }}</div>
                                    </div>
                                    <template v-if="enr.payment_status === 'paid'">
                                        <Link :href="route('enrollments.learn', enr.course.id)" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
                                            {{ enr.progress_percent > 0 ? __('Resume') : __('Start') }}
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <Link :href="route('payments.initiate', { enrollment_id: enr.id })" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
                                            {{ __('Pay Now') }}
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-5 bg-white rounded-4 shadow-sm border-0 glass-morphism">
                    <div class="mb-4">
                        <div class="bg-light-soft d-inline-block p-4 rounded-circle">
                            <i class="bi bi-journal-x fs-1 text-muted opacity-50"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark">{{ __('No courses found') }}</h4>
                    <p class="text-muted">{{ __("You haven't enrolled in any courses yet.") }}</p>
                    <Link :href="route('courses.browse')" class="btn btn-success mt-4 rounded-pill px-5 py-2 fw-bold shadow-sm transition-all hover-lift">
                        {{ __('Explore Courses') }}
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
.transition-all {
    transition: all 0.3s ease;
}
.x-small {
    font-size: 0.7rem;
}
.object-fit-cover {
    object-fit: cover;
}
.bg-gradient-dark-transparent {
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
}
.bg-white-20 {
    background-color: rgba(255,255,255,0.2);
}
.glass-morphism {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(10px);
}
.bg-light-soft {
    background-color: #f8f9fa;
}
</style>
