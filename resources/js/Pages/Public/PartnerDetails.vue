<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    partner: Object,
    courses: Array,
    teachers: Array,
});
</script>

<template>
    <Head :title="partner.name" />

    <PublicLayout>
        <!-- Header Banner Section -->
        <PageHeader 
            :title="partner.name" 
            :subtitle="partner.description ? partner.description.substring(0, 120) + '...' : ''" 
            :badge="__('PARTNER PROFILE')" 
            :bg-image="partner.cover_image || 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1920&q=80'" 
        />

        <!-- Main Profile Body -->
        <section class="py-5 bg-light" style="min-height: 70vh;">
            <div class="container">
                <div class="row g-4">
                    
                    <!-- Left Sidebar: Organization Info -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <!-- Logo Wrapper -->
                            <div class="mb-4 bg-white rounded-circle shadow-sm border p-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <img :src="partner.logo_url" class="img-fluid rounded-circle" style="max-height: 100%; object-fit: contain;" alt="Partner Logo">
                            </div>
                            <h4 class="fw-bold text-dark mb-3">{{ __('About the Partner') }}</h4>
                            <p class="text-muted small mb-4" style="white-space: pre-line; line-height: 1.6;">
                                {{ partner.description || __('No description provided.') }}
                            </p>

                            <hr>

                            <h5 class="fw-bold text-dark mb-3">{{ __('Contact Details') }}</h5>
                            <ul class="list-unstyled mb-0 space-y-3 small text-muted">
                                <li v-if="partner.address" class="d-flex gap-2 align-items-start">
                                    <i class="bi bi-geo-alt text-success mt-1"></i>
                                    <span>{{ partner.address }}</span>
                                </li>
                                <li class="d-flex gap-2 align-items-start mt-2">
                                    <i class="bi bi-shield-check text-success mt-1"></i>
                                    <span>{{ __('Verified Partner Institution') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Column: Courses and Faculty -->
                    <div class="col-lg-8">
                        
                        <!-- Courses Section -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-book-fill text-success"></i> {{ __('Hosted Programs & Courses') }}
                            </h4>

                            <div v-if="courses.length > 0" class="row g-3">
                                <div v-for="course in courses" :key="course.id" class="col-md-6">
                                    <div class="card h-100 border rounded-3 p-3 bg-light-subtle d-flex flex-column">
                                        <div class="position-relative overflow-hidden rounded mb-3" style="height: 140px;">
                                            <img :src="course.image_url || '/images/course-placeholder.jpg'" class="img-fluid w-100 h-100 object-fit-cover" alt="Course Image">
                                        </div>
                                        <h6 class="fw-bold text-dark mb-2 text-truncate">{{ course.title }}</h6>
                                        <p class="text-muted x-small line-clamp-2 mb-3">{{ course.short_description }}</p>
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <span class="badge bg-secondary text-white rounded-pill x-small px-2 py-1">{{ course.level }}</span>
                                            <Link :href="route('courses.browse')" class="btn btn-sm btn-success rounded-pill px-3 fw-bold x-small">
                                                {{ __('Explore') }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4 bg-light rounded-3">
                                <i class="bi bi-book text-muted fs-2"></i>
                                <p class="text-muted small mt-2 mb-0">{{ __('No active courses hosted currently.') }}</p>
                            </div>
                        </div>

                        <!-- Instructors Section -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge-fill text-success"></i> {{ __('Our Instructors & Faculty') }}
                            </h4>

                            <div v-if="teachers.length > 0" class="row g-3">
                                <div v-for="teacher in teachers" :key="teacher.id" class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3 p-3 border rounded-3 bg-light-subtle">
                                        <img :src="teacher.avatar_url" class="rounded-circle shadow-sm border" style="width: 50px; height: 50px; object-fit: cover;" alt="Avatar">
                                        <div class="overflow-hidden">
                                            <h6 class="fw-bold mb-1 text-dark text-truncate">{{ teacher.name }}</h6>
                                            <span class="x-small text-muted d-block text-truncate"><i class="bi bi-envelope me-1"></i> {{ teacher.email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4 bg-light rounded-3">
                                <i class="bi bi-people text-muted fs-2"></i>
                                <p class="text-muted small mt-2 mb-0">{{ __('No instructors are listed currently.') }}</p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.hover-white:hover {
    color: white !important;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.x-small {
    font-size: 0.7rem;
}
.space-y-3 > * + * {
    margin-top: 0.75rem;
}
</style>
