<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import StatsCounter from '@/Components/StatsCounter.vue';
import PageHeader from '@/Components/PageHeader.vue';
import EthicsSection from '@/Components/EthicsSection.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    header: Object,
    about_image: String,
    mission: String,
    vision: String,
    about_lms: String,
    about_ethics: Array,
    counter_courses: String,
    counter_teachers: String,
    counter_students: String,
    counter_batches: String,
});

const page = usePage();
const currentLocale = computed(() => {
    return (page.props.translations && page.props.translations['Home']) ? 'bn' : 'en';
});
</script>

<template>
    <Head :title="__('About Us')" />

    <PublicLayout>
        <div class="about-page-wrapper">
            <!-- Hero Header Section -->
            <PageHeader 
                :title="header.title" 
                :subtitle="header.subtitle" 
                :badge="header.badge" 
                :bg-image="header.bg_image" 
            />

            <!-- Mission & Vision Duo Section (One Row, Two Columns) -->
            <section class="py-5 position-relative bg-white section-divider">
                <div class="container py-4">
                    <div class="row g-4 align-items-stretch">
                        <!-- Left Column: Mission Card -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 rounded-4 shadow-sm p-4 p-md-5 bg-gradient-light position-relative overflow-hidden mission-card">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; box-shadow: 0 4px 15px rgba(25, 135, 84, 0.2);">
                                        <i class="bi bi-compass-fill fs-4"></i>
                                    </div>
                                    <h2 class="h3 fw-bold text-success mb-0 text-uppercase tracking-wide">{{ __('Our Mission') }}</h2>
                                </div>
                                <p class="fs-4 fw-medium text-dark leading-relaxed mb-0">
                                    {{ mission }}
                                </p>
                            </div>
                        </div>
                        <!-- Right Column: Vision Card -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 rounded-4 shadow-sm p-4 p-md-5 text-white bg-forest-gradient position-relative overflow-hidden vision-card">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);">
                                        <i class="bi bi-eye-fill fs-4"></i>
                                    </div>
                                    <h2 class="h3 fw-bold text-white mb-0 text-uppercase tracking-wide">{{ __('Our Vision') }}</h2>
                                </div>
                                <p class="fs-4 fw-medium leading-relaxed opacity-95 mb-0">
                                    {{ vision }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Ethics / Core Values Section -->
            <EthicsSection :ethics="about_ethics" :current-locale="currentLocale" />

            <!-- About LMS section -->
            <section class="py-5 bg-white">
                <div class="container py-4">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6 position-relative">
                            <div class="image-deco-dots"></div>
                            <div class="image-mask shadow-lg rounded-5 overflow-hidden">
                                <img :src="about_image || 'https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800'" class="img-fluid w-100 transform-hover-img" alt="LMS Portal">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <span class="text-success text-uppercase fw-bold tracking-wider fs-6 mb-2 d-block">{{ __('Interactive Learning') }}</span>
                            <h3 class="display-5 fw-bold text-dark mb-3">{{ __('About Our Learning Portal') }}</h3>
                            <p class="text-muted fs-5 leading-relaxed mb-4">
                                {{ about_lms }}
                            </p>
                            <p class="text-muted fs-6 mb-4">
                                {{ __('Whether you are an urban kitchen gardener, a suburban homesteader, or a rural scale agricultural manager, our tailored learning pathways enable you to gain credentials from internationally recognized partners.') }}
                            </p>
                            
                            <!-- Key badges -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                        <i class="bi bi-patch-check-fill text-success fs-5"></i>
                                        <span>{{ __('Certified Pathways') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                        <i class="bi bi-people-fill text-success fs-5"></i>
                                        <span>{{ __('Peer Networking') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                        <i class="bi bi-journal-check text-success fs-5"></i>
                                        <span>{{ __('Bilingual Access') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                        <i class="bi bi-award-fill text-success fs-5"></i>
                                        <span>{{ __('Verified Certificates') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <Link :href="route('courses.browse')" class="btn btn-success rounded-pill px-5 py-3 shadow fw-bold transition-all text-white border-0 bg-success hover-success">
                                    {{ __('Browse Course Catalog') }}
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <StatsCounter 
                :counter-courses="counter_courses"
                :counter-students="counter_students"
                :counter-teachers="counter_teachers"
                :counter-batches="counter_batches"
            />

            <!-- Bottom CTA Section -->
            <section class="py-5 bg-white text-center position-relative">
                <div class="container py-5">
                    <div class="max-w-2xl mx-auto py-5 rounded-5 px-4 bg-gradient-cta shadow">
                        <h2 class="display-5 fw-bold text-white mb-3">{{ __('Ready to Start Your Regenerative Journey?') }}</h2>
                        <p class="text-white-50 fs-5 mb-4">{{ __('Join our bilingual learning platform today to build practical permaculture skills and make a lasting environmental impact.') }}</p>
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <Link :href="route('courses.browse')" class="btn btn-warning rounded-pill px-4 py-3 fw-bold text-dark transition-all hover-warning">
                                {{ __('Explore Courses') }}
                            </Link>
                            <Link :href="route('org.register')" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold transition-all">
                                {{ __('Become a Partner') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Outfit:wght@300..900&display=swap');

.about-page-wrapper {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    overflow-x: hidden;
}

h1, h2, h3, h4, .display-2, .display-4, .display-5 {
    font-family: 'Outfit', sans-serif;
    letter-spacing: -0.02em;
}

/* Animations */
.animate-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #ffc107;
    border-radius: 50%;
    display: inline-block;
    animation: pulseGlow 2.0s infinite ease-in-out;
}

@keyframes pulseGlow {
    0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
    70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

/* Hero Section */
.bg-success-light-20 {
    background-color: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
}
.border-success-light-40 {
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
}
.text-shadow {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

/* Mission & Vision Row */
.bg-gradient-light {
    background: linear-gradient(135deg, #f3fdf6 0%, #eafcf0 100%);
    border-left: 5px solid #198754 !important;
}
.bg-forest-gradient {
    background: linear-gradient(135deg, #0b341f 0%, #155536 100%);
}
.mission-card, .vision-card {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
}
.mission-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(25, 135, 84, 0.1) !important;
}
.vision-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(11, 52, 31, 0.25) !important;
}

/* Core Ethics */
.bg-light-green-subtle {
    background-color: #f7faf8;
}
.ethics-card {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.ethics-card-accent {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.ethics-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(25, 135, 84, 0.08) !important;
}
.ethics-card:hover .ethics-card-accent {
    opacity: 1;
}
.ethics-icon-container {
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.3s ease, color 0.3s ease;
}
.ethics-card:hover .ethics-icon-container {
    transform: scale(1.1) rotate(5deg);
    background-color: #198754 !important;
    color: white !important;
}

/* LMS Overview Section */
.image-mask {
    border-top-right-radius: 4rem !important;
    border-bottom-left-radius: 4rem !important;
    border: 6px solid #fff;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08) !important;
}
.transform-hover-img {
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.transform-hover-img:hover {
    transform: scale(1.05);
}
.image-deco-dots {
    position: absolute;
    width: 100px;
    height: 100px;
    background-image: radial-gradient(#198754 20%, transparent 20%);
    background-size: 12px 12px;
    bottom: -20px;
    left: -20px;
    z-index: -1;
    opacity: 0.35;
}
.hover-success {
    transition: all 0.3s ease;
}
.hover-success:hover {
    background-color: #115e3a !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3) !important;
}


/* CTA Section */
.max-w-2xl {
    max-width: 780px;
}
.bg-gradient-cta {
    background: linear-gradient(135deg, #115e3a 0%, #0c2f1c 100%);
}
.hover-warning {
    transition: all 0.3s ease;
}
.hover-warning:hover {
    background-color: #e0a800 !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.4) !important;
}
</style>
