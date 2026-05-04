<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
</script>

<template>
    <Head :title="__('Browse Courses')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Available Courses') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="row g-4">
                    <div v-for="course in courses" :key="course.id" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-shadow transition-all">
                            <div class="position-relative">
                                <img :src="course.image_url || '/images/course-placeholder.jpg'" class="card-img-top object-fit-cover" style="height: 220px;" alt="">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">{{ course.level }}</span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold text-dark mb-2">{{ course.title }}</h5>
                                <p class="text-muted small mb-4 line-clamp-2">{{ course.short_description }}</p>
                                
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="d-flex align-items-center small text-muted">
                                        <i class="bi bi-clock me-1 text-success"></i> {{ course.duration }}
                                    </div>
                                    <div class="d-flex align-items-center small text-muted">
                                        <i class="bi bi-play-circle me-1 text-success"></i> {{ course.delivery_mode }}
                                    </div>
                                </div>

                                <div v-if="course.is_enrolled" class="mt-auto">
                                    <div class="p-4 bg-success-subtle border border-success rounded-4 text-center">
                                        <div class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                        </div>
                                        <div class="fw-bold text-dark mb-3">{{ __('Already Enrolled') }}</div>
                                        <Link :href="route('enrollments.my-courses')" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">
                                            {{ __('Go to My Learning') }}
                                        </Link>
                                    </div>
                                </div>
                                <div v-else-if="course.active_batches && course.active_batches.length > 0" class="mt-auto">
                                    <div v-for="batch in course.active_batches" :key="batch.id" class="p-3 bg-light rounded-3 border mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small fw-bold text-dark">{{ batch.title }}</span>
                                            <span class="badge bg-white text-success border border-success-subtle rounded-1">{{ __('Open') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fs-5 fw-bold text-success">
                                                    {{ batch.price > 0 ? 'BDT ' + Number(batch.price).toLocaleString() : __('FREE') }}
                                                </div>
                                                <div class="x-small text-muted">{{ __('Starts') }}: {{ batch.start_date }}</div>
                                            </div>
                                            <button 
                                                @click="enroll(batch.id)" 
                                                class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm"
                                                :disabled="enrolling === batch.id"
                                            >
                                                <span v-if="enrolling === batch.id" class="spinner-border spinner-border-sm me-1"></span>
                                                {{ batch.price > 0 ? __('Enroll Now') : __('Join Free') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center p-3 bg-light rounded-3">
                                    <span class="small text-muted italic">{{ __('No active batches available') }}</span>
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
.hover-shadow:hover {
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    transform: translateY(-5px);
}
.transition-all { transition: all 0.3s ease; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.x-small { font-size: 0.7rem; }
</style>
