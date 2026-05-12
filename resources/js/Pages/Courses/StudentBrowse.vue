<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    courses: Array
});

const form = useForm({
    batch_id: null
});

const enrolling = ref(null);

const enroll = (batchId) => {
    enrolling.value = batchId;
    form.batch_id = batchId;
    form.post(route('enrollments.store'), {
        onFinish: () => enrolling.value = null
    });
};

const getLevelBadgeClass = (level) => {
    const l = level?.toLowerCase();
    if (l?.includes('beginner') || l?.includes('foundation')) return 'bg-smart-success';
    if (l?.includes('intermediate')) return 'bg-smart-warning';
    if (l?.includes('advanced')) return 'bg-smart-danger';
    return 'bg-smart-success';
};
</script>

<template>
    <Head :title="__('Organization Courses')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Available Courses') }}
        </template>

        <div class="py-4">
            <div class="container-fluid">
                <div class="row g-4">
                    <div v-for="course in courses" :key="course.id" class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-smart transition-all bg-white">
                            <!-- Image Section -->
                            <div class="position-relative overflow-hidden" style="height: 180px;">
                                <img :src="course.image_url" class="card-img-top object-fit-cover transition-all image-scale" style="height: 100%;" alt="">
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-overlay-smart"></div>
                                <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-1 align-items-end">
                                    <template v-if="course.is_enrolled">
                                        <span v-if="course.enrollment_status === 'active'" class="badge bg-primary shadow-sm small text-uppercase py-1 px-2 border border-white-50">
                                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('Enrolled') }}
                                        </span>
                                        <span v-else class="badge bg-warning text-dark shadow-sm small text-uppercase py-1 px-2 border border-white-50">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ __('Pending Payment') }}
                                        </span>
                                    </template>
                                    <span class="badge badge-smart-level shadow-sm" :class="getLevelBadgeClass(course.level)">{{ course.level }}</span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title fw-bold text-dark mb-2 fs-6">{{ course.title }}</h5>
                                <p class="text-muted small mb-3 line-clamp-2 flex-grow-1">{{ course.short_description }}</p>
                                
                                <div class="mt-auto">
                                    <div v-if="course.is_enrolled" class="d-grid gap-2">
                                        <Link v-if="course.enrollment_status === 'active'" :href="route('enrollments.my-courses')" class="btn btn-outline-primary btn-sm rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="bi bi-play-circle-fill me-2"></i>{{ __('Continue Learning') }}
                                        </Link>
                                        <Link v-else :href="route('payments.initiate', { enrollment_id: course.enrollment_id })" class="btn btn-warning btn-sm rounded-pill fw-bold py-2 shadow-sm text-dark">
                                            <i class="bi bi-credit-card-fill me-2"></i>{{ __('Pay Now') }}
                                        </Link>
                                    </div>
                                    <div v-else-if="course.active_batches && course.active_batches.length > 0" class="d-grid">
                                        <button 
                                            @click="enroll(course.active_batches[0].id)" 
                                            class="btn btn-success btn-sm rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                            :disabled="enrolling === course.active_batches[0].id"
                                        >
                                            <span v-if="enrolling === course.active_batches[0].id" class="spinner-border spinner-border-sm"></span>
                                            <i v-else class="bi bi-cart-plus-fill"></i>
                                            {{ enrolling === course.active_batches[0].id ? __('Enrolling...') : __('Enroll Now') }} — ৳{{ Number(course.active_batches[0].price).toLocaleString() }}
                                        </button>
                                    </div>
                                    <div v-else class="text-center py-2 border rounded-pill bg-light">
                                        <span class="text-muted small fw-bold"><i class="bi bi-clock-history me-1"></i>{{ __('No Active Batch') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="courses.length === 0" class="col-12">
                        <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                            <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                            <h4 class="text-muted">{{ __('No courses available in your organization yet.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.hover-smart:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.image-scale { transition: transform 0.5s ease; }
.hover-smart:hover .image-scale { transform: scale(1.05); }

.bg-overlay-smart {
    background: linear-gradient(180deg, rgba(0,0,0,0) 60%, rgba(0,0,0,0.4) 100%);
}

.badge-smart-level {
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.bg-smart-success { background-color: #10b981; color: white; }
.bg-smart-warning { background-color: #f59e0b; color: white; }
.bg-smart-danger { background-color: #ef4444; color: white; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
