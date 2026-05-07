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
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-smart transition-all bg-white">
                            <!-- Image Section with Smart Overlay -->
                            <div class="position-relative overflow-hidden" style="height: 220px;">
                                <img :src="course.image_url || '/images/course-placeholder.jpg'" class="card-img-top object-fit-cover transition-all image-scale" style="height: 100%;" alt="">
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-overlay-smart"></div>
                                
                                <!-- Status Badges (Top) -->
                                <div class="position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between align-items-start">
                                    <span class="badge badge-smart-level px-3 py-2 font-inter" :class="getLevelBadgeClass(course.level)">
                                        {{ course.level }}
                                    </span>
                                    <span v-if="course.is_enrolled" class="badge badge-smart-enrolled px-3 py-2 font-inter">
                                        <i class="bi bi-check-circle-fill me-1"></i> {{ __('ENROLLED') }}
                                    </span>
                                </div>

                                <!-- Action/Info Overlay (Bottom) -->
                                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-smart">
                                    <!-- Dynamic Action Panel -->
                                    <div class="glass-smart-panel p-2 rounded-3 mb-2">
                                        <!-- Enrolled State -->
                                        <div v-if="course.is_enrolled" class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="play-indicator-smart">
                                                    <i class="bi bi-play-fill text-white"></i>
                                                </div>
                                                <span class="fw-bold text-white font-inter small">{{ __('Resume Learning') }}</span>
                                            </div>
                                            <Link :href="route('enrollments.learn', course.id)" class="btn btn-smart-action btn-sm rounded-pill px-4">
                                                {{ __('Go') }}
                                            </Link>
                                        </div>

                                        <!-- Pending Payment State -->
                                        <div v-else-if="course.pending_enrollment_id" class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2 text-warning">
                                                <i class="bi bi-credit-card-2-front-fill"></i>
                                                <span class="fw-bold font-inter small text-white">{{ __('Payment Required') }}</span>
                                            </div>
                                            <Link :href="route('payments.initiate', { enrollment_id: course.pending_enrollment_id })" class="btn btn-warning btn-sm rounded-pill px-3 py-1 fw-bold font-inter x-small text-dark">
                                                {{ __('Pay Now') }}
                                            </Link>
                                        </div>

                                        <!-- Available Batches -->
                                        <div v-else-if="course.active_batches && course.active_batches.length > 0">
                                            <div v-for="batch in course.active_batches" :key="batch.id" class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex flex-column">
                                                    <span class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.55rem;">{{ batch.title }}</span>
                                                    <div class="d-flex align-items-baseline gap-2">
                                                        <span class="fw-bold text-white fs-6 font-inter lh-1">{{ batch.price > 0 ? '৳' + Number(batch.price).toLocaleString() : __('FREE') }}</span>
                                                        <span class="text-white-50" style="font-size: 0.65rem;">{{ batch.start_date }}</span>
                                                    </div>
                                                </div>
                                                <button @click="enroll(batch.id)" class="btn btn-smart-action btn-sm rounded-pill px-4" :disabled="enrolling === batch.id">
                                                    <span v-if="enrolling === batch.id" class="spinner-border spinner-border-sm me-1"></span>
                                                    {{ __('Enroll') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- No Batches -->
                                        <div v-else class="text-center py-1">
                                            <span class="text-white-50 fw-bold x-small text-uppercase">{{ __('Coming Soon') }}</span>
                                        </div>
                                    </div>

                                    <!-- Core Metadata -->
                                    <div class="d-flex gap-3 px-1">
                                        <div class="d-flex align-items-center x-small text-white-75 fw-medium">
                                            <i class="bi bi-clock me-2 text-success-light"></i> {{ course.duration }}
                                        </div>
                                        <div class="d-flex align-items-center x-small text-white-75 fw-medium">
                                            <i class="bi bi-record-circle me-2 text-success-light"></i> {{ course.delivery_mode }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <div v-if="course.organization" class="d-flex align-items-center gap-2 mb-2">
                                    <div class="org-avatar-smart shadow-sm">
                                        {{ course.organization.initials }}
                                    </div>
                                    <span class="x-small fw-bold text-muted text-uppercase letter-spacing-1">{{ course.organization.name }}</span>
                                </div>

                                <h5 class="card-title fw-bold mb-0 font-inter fs-5 lh-sm">
                                    <Link v-if="course.is_enrolled" :href="route('enrollments.learn', course.id)" class="text-dark text-decoration-none hover-smart-title">
                                        {{ course.title }}
                                    </Link>
                                    <Link v-else-if="course.pending_enrollment_id" :href="route('payments.initiate', { enrollment_id: course.pending_enrollment_id })" class="text-dark text-decoration-none hover-smart-title">
                                        {{ course.title }}
                                    </Link>
                                    <a v-else-if="course.active_batches && course.active_batches.length > 0" href="javascript:void(0)" @click="enroll(course.active_batches[0].id)" class="text-dark text-decoration-none hover-smart-title">
                                        {{ course.title }}
                                    </a>
                                    <span v-else class="text-dark">{{ course.title }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    methods: {
        getLevelBadgeClass(level) {
            const l = level?.toLowerCase();
            if (l?.includes('beginner') || l?.includes('foundation')) return 'bg-smart-success';
            if (l?.includes('intermediate')) return 'bg-smart-warning';
            if (l?.includes('advanced')) return 'bg-smart-danger';
            return 'bg-smart-success';
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.font-inter { font-family: 'Inter', sans-serif !important; }

.hover-smart:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
}
.hover-smart:hover .image-scale { transform: scale(1.05); }
.image-scale { transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }

.bg-overlay-smart {
    background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.8) 100%);
    z-index: 1;
}
.bg-gradient-smart { z-index: 2; }

/* Smart Badges */
.badge-smart-level {
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.65rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.bg-smart-info { background-color: #6366f1; color: white; } /* Smart Indigo */
.bg-smart-warning { background-color: #f59e0b; color: white; }
.bg-smart-danger { background-color: #ef4444; color: white; }
.bg-smart-success { background-color: #10b981; color: white; }

.badge-smart-enrolled {
    background: rgba(255, 255, 255, 0.9);
    color: #10b981;
    border-radius: 6px;
    font-weight: 800;
    font-size: 0.6rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Glass Panel */
.glass-smart-panel {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.btn-smart-action {
    background: #10b981;
    color: white;
    font-weight: 700;
    font-size: 0.75rem;
    border: none;
    transition: all 0.3s ease;
}
.btn-smart-action:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.play-indicator-smart {
    width: 24px;
    height: 24px;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.text-success-light { color: #34d399; }
.text-white-75 { color: rgba(255, 255, 255, 0.75); }
.text-white-50 { color: rgba(255, 255, 255, 0.5); }

.hover-smart-title { transition: color 0.3s ease; }
.hover-smart-title:hover { color: #10b981 !important; }

.org-avatar-smart {
    width: 20px;
    height: 20px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.55rem;
    font-weight: 800;
}
.description-tagline-smart {
    border-left: 2px solid #e2e8f0;
    padding-left: 10px;
    transition: border-color 0.3s ease;
}
.hover-smart:hover .description-tagline-smart {
    border-color: #10b981;
}

.transition-all { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.x-small { font-size: 0.65rem; }
.text-white-50 { color: rgba(255, 255, 255, 0.5) !important; }
.white-10 { border-color: rgba(255, 255, 255, 0.1); }
.bg-white-10 { background-color: rgba(255, 255, 255, 0.1); }

.border-dashed { border: 1.5px dashed #dee2e6; }
.hover-text-success:hover {
    color: #198754 !important;
}
.hover-text-warning:hover {
    color: #ffc107 !important;
}
</style>
