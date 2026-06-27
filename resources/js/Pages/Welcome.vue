<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import StatsCounter from '@/Components/StatsCounter.vue';
import EthicsSection from '@/Components/EthicsSection.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    hero_title: String,
    hero_description: String,
    about_lms: String,
    counter_courses: String,
    counter_teachers: String,
    counter_students: String,
    counter_batches: String,
    courses: Array,
    partners: Array,
    ethics: Array,
    homepage_slides: Array,
});

const page = usePage();
const currentLocale = computed(() => {
    return (page.props.translations && page.props.translations['Home']) ? 'bn' : 'en';
});
</script>

<template>
    <PublicLayout>
        <Head :title="__('Welcome to Regenerative Systems')" />

        <!-- 1. Hero Section (Bootstrap Carousel with Multiple Slides) -->
        <header id="heroCarousel" class="carousel slide carousel-fade hero-section" data-bs-ride="carousel" style="min-height: 90vh; background-color: #0b1e14;">
            <!-- Indicators -->
            <div v-if="homepage_slides && homepage_slides.length > 1" class="carousel-indicators z-2">
                <button v-for="(slide, idx) in homepage_slides" :key="idx" type="button" data-bs-target="#heroCarousel" :data-bs-slide-to="idx" :class="{ 'active': idx === 0 }" aria-current="true" :aria-label="'Slide ' + (idx + 1)"></button>
            </div>

            <!-- Slides List -->
            <div class="carousel-inner h-100" style="min-height: 90vh;">
                <div v-for="(slide, idx) in (homepage_slides && homepage_slides.length > 0 ? homepage_slides : [{ title: { en: hero_title, bn: hero_title }, description: { en: hero_description, bn: hero_description }, image: 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }])" 
                     :key="idx" 
                     class="carousel-item h-100" 
                     :class="{ 'active': idx === 0 }"
                     style="min-height: 90vh;">
                     
                    <!-- Background style -->
                    <div class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center"
                         :style="{
                             background: 'linear-gradient(rgba(10, 45, 25, 0.65), rgba(15, 60, 35, 0.72)), url(' + (slide.image || 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') + ')',
                             backgroundSize: 'cover',
                             backgroundPosition: 'center',
                             minHeight: '90vh'
                         }">
                         
                        <div class="container py-5 text-center position-relative z-1 animate-up">
                            <div class="glass-hero-panel mx-auto max-w-4xl p-4 p-md-5 rounded-4 shadow-lg border border-white-10 backdrop-blur">
                                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-light-20 border border-success-light-40 mb-3">
                                    <span class="pulse-dot"></span>
                                    <span class="text-uppercase tracking-wider fw-bold small text-light">{{ __('Interactive Learning Common') }}</span>
                                </div>
                                <h1 class="display-1 fw-extrabold mb-3 text-shadow lh-sm text-white">
                                    {{ slide.title[currentLocale] || slide.title['en'] }}
                                </h1>
                                <p class="lead max-w-3xl mx-auto fs-4 opacity-90 text-shadow mb-4 text-white">
                                    {{ slide.description[currentLocale] || slide.description['en'] }}
                                </p>
                                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                                    <Link :href="route('courses.browse')" class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm transition-hover">
                                        {{ __('Explore Courses') }}
                                    </Link>
                                    <Link :href="route('about')" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold transition-hover">
                                        {{ __('Learn More') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <template v-if="homepage_slides && homepage_slides.length > 1">
                <button class="carousel-control-prev z-2" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next z-2" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </template>
        </header>

        <!-- 2. About LMS Portal Section -->
        <section class="py-5 bg-white border-bottom">
            <div class="container py-4">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6 animate-up">
                        <div class="position-relative">
                            <div class="absolute-green-glow"></div>
                            <img src="https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800" class="img-fluid rounded-4 shadow position-relative z-1 w-100" alt="LMS Portal">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small mb-2 text-uppercase tracking-wider fw-bold">{{ __('Interactive Academy') }}</span>
                        <h2 class="display-5 fw-bold text-dark mb-4">{{ __('About Our Learning Portal') }}</h2>
                        <p class="text-muted fs-5 leading-relaxed mb-4">
                            {{ about_lms }}
                        </p>
                        <p class="text-muted mb-4">
                            {{ __('Whether you are an urban kitchen gardener, a suburban homesteader, or a rural scale agricultural manager, our tailored learning pathways enable you to gain credentials from internationally recognized partners.') }}
                        </p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span class="fw-semibold text-dark">{{ __('Bilingual Curriculums') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span class="fw-semibold text-dark">{{ __('Host-Led Fieldwork') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span class="fw-semibold text-dark">{{ __('Gateway Verification') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span class="fw-semibold text-dark">{{ __('Global Certification') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <Link :href="route('about')" class="btn btn-success rounded-pill px-5 py-3 shadow-sm fw-bold transition-hover">
                                {{ __('Learn More About Us') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Permaculture Core Ethics Section (Dynamic from CMS) -->
        <EthicsSection :ethics="ethics" :current-locale="currentLocale" />

        <!-- 3. Featured Courses Section (Dynamic) -->
        <section class="py-5 bg-light position-relative">
            <div class="container py-4">
                <div class="text-center mb-5 max-w-2xl mx-auto">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small mb-2 text-uppercase tracking-wider fw-bold">{{ __('Featured Curriculums') }}</span>
                    <h2 class="display-5 fw-bold text-dark">{{ __('Restorative Learning Programs') }}</h2>
                    <p class="text-muted lead mb-0">{{ __('Unlock certificate training courses hosted by verified regional organic farms.') }}</p>
                </div>

                <div v-if="courses && courses.length > 0" class="row g-4 justify-content-center">
                    <div v-for="course in courses" :key="course.id" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white course-card transition-hover">
                            <!-- Course Thumbnail -->
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img :src="course.image_url" class="w-100 h-100 object-fit-cover" alt="Course Cover" />
                                <span class="badge bg-dark-glass position-absolute top-3 right-3 text-white small px-3 py-1 rounded-pill backdrop-blur">
                                    {{ course.level }}
                                </span>
                            </div>
                            
                            <!-- Course Content -->
                            <div class="card-body p-4 d-flex flex-column">
                                <span v-if="course.organization_name" class="text-success small fw-bold text-uppercase tracking-wider mb-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-patch-check-fill"></i> {{ course.organization_name }}
                                </span>
                                <h4 class="fw-bold text-dark mb-2 text-truncate">{{ course.title }}</h4>
                                <p class="text-muted small line-clamp-3 mb-4">{{ course.short_description || __('Learn how to cultivate dynamic landscapes.') }}</p>
                                
                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-success">
                                        {{ course.price > 0 ? '$' + course.price : __('Free') }}
                                    </span>
                                    <Link :href="route('courses.browse')" class="btn btn-success rounded-pill px-4 btn-sm fw-bold">
                                        {{ __('Explore Catalog') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State courses fallback -->
                <div v-else class="text-center py-5 bg-white rounded-4 shadow-sm border border-success-subtle max-w-xl mx-auto">
                    <div class="bg-success-subtle text-success rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-book fs-1"></i>
                    </div>
                    <h4 class="fw-bold">{{ __('Dynamic courses coming soon') }}</h4>
                    <p class="text-muted mb-4">{{ __('Our host partner organizations are uploading new permaculture curricula. Stay tuned!') }}</p>
                    <Link :href="route('courses.browse')" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                        {{ __('View Catalog') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- 5. Stats Counter Section -->
        <StatsCounter 
            :counter-courses="counter_courses"
            :counter-students="counter_students"
            :counter-teachers="counter_teachers"
            :counter-batches="counter_batches"
        />

        <!-- 6. Verified Partner Organizations Section (Dynamic) -->
        <section v-if="partners && partners.length > 0" class="py-5 bg-light border-bottom">
            <div class="container py-4">
                <div class="text-center mb-5 max-w-2xl mx-auto">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small mb-2 text-uppercase tracking-wider fw-bold">{{ __('Collaborative Ecosystem') }}</span>
                    <h3 class="display-6 fw-bold text-dark">{{ __('Our Verified Partner Centers') }}</h3>
                    <p class="text-muted small mb-0">{{ __('Resilient ecosystems, cooperative networks, and permaculture sites that host programs.') }}</p>
                </div>

                <div class="row g-4 justify-content-center align-items-center">
                    <div v-for="partner in partners" :key="partner.id" class="col-6 col-sm-4 col-md-3 col-lg-2 text-center">
                        <Link :href="route('partners.index')" class="d-block p-3 bg-white rounded-4 border shadow-sm transition-hover cursor-pointer text-decoration-none">
                            <div class="partner-logo-wrapper mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                <img :src="partner.logo_url" class="img-fluid rounded-circle" style="max-height: 100%; object-fit: contain;" alt="Partner Logo">
                            </div>
                            <span class="text-dark small fw-bold text-truncate d-block">{{ partner.name }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Premium Dark Forest Newsletter Section (Full Width Background) -->
        <section class="py-5 text-white bg-forest-gradient position-relative overflow-hidden">
            <div class="container py-4 text-center max-w-4xl">
                <div class="position-relative z-1 py-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle p-3 mb-4 shadow" style="width: 60px; height: 60px;">
                        <i class="bi bi-send-fill fs-3"></i>
                    </div>
                    <h2 class="display-5 fw-bold mb-3 text-shadow">{{ __('Join the Regenerative Movement') }}</h2>
                    <p class="lead max-w-2xl mx-auto opacity-90 fs-5 mb-5 text-shadow">
                        {{ __('Subscribe to receive permaculture design insights, organic farm stories, and newly launched courses directory.') }}
                    </p>
                    <form @submit.prevent="" class="d-flex flex-column flex-sm-row justify-content-center gap-2 max-w-lg mx-auto">
                        <input type="email" class="form-control rounded-pill px-4 border-0 py-3 shadow" :placeholder="__('Enter your email address')" required />
                        <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm transition-hover">{{ __('Subscribe') }}</button>
                    </form>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style>
.transition-hover {
    transition: all 0.3s ease-in-out;
}
.transition-hover:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.075) !important;
}
.animate-up {
    animation: fadeInUp 0.8s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.glass-hero-panel {
    background: rgba(10, 30, 20, 0.38);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.125);
}
.bg-glass-morphism {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.bg-dark-glass {
    background: rgba(10, 25, 15, 0.65);
    backdrop-filter: blur(8px);
}
.max-w-4xl { max-width: 900px; }
.max-w-3xl { max-width: 800px; }
.max-w-2xl { max-width: 700px; }
.max-w-xl { max-width: 600px; }
.max-w-lg { max-width: 500px; }
.text-shadow {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
}
.bg-gradient-light {
    background: linear-gradient(135deg, #f8fdf9 0%, #ffffff 100%);
    border: 1px solid rgba(25, 135, 84, 0.08);
}
.pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #39da8a;
    border-radius: 50%;
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0% { transform: scale(0.9); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.6; }
    100% { transform: scale(0.9); opacity: 1; }
}
.bg-success-light-20 {
    background-color: rgba(25, 135, 84, 0.2);
}
.border-success-light-40 {
    border-color: rgba(25, 135, 84, 0.4);
}
.bg-forest-gradient {
    background: linear-gradient(135deg, #092c17 0%, #155b32 100%);
}
.course-card img {
    transition: transform 0.5s ease;
}
.course-card:hover img {
    transform: scale(1.05);
}
.absolute-green-glow {
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(25, 135, 84, 0.15);
    filter: blur(100px);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
