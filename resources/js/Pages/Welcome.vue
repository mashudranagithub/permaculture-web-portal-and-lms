<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <PublicLayout>
        <Head :title="__('Welcome to Permaculture Methods')" />

        <!-- Hero Section -->
        <header class="hero-section text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; min-height: 90vh;">
            <div class="container text-center">
                <h1 class="display-1 fw-bold mb-4 animate-up">
                    {{ __('Cultivating a Sustainable Future') }}
                </h1>
                <p class="lead mb-5 max-w-2xl mx-auto fs-4 opacity-90">
                    {{ __('Join thousands of changemakers worldwide in designing nature-based systems that regenerate our planet and communities.') }}
                </p>
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mb-5">
                    <Link :href="route('courses.browse')" class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg">{{ __('Explore Courses') }}</Link>
                    <button type="button" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold">{{ __('Learn More') }}</button>
                </div>
            </div>
        </header>

        <!-- Feature Section -->
        <section class="py-5 bg-light">
            <div class="container py-5">
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 rounded-4 transition-hover">
                            <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 p-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zM5 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5zM1 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3z"/></svg>
                            </div>
                            <h3 class="fw-bold mb-3">{{ __('Regenerative Design') }}</h3>
                            <p class="text-muted">{{ __('Master the principles of earth care and people care through experts in nature-based systems.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 rounded-4 transition-hover">
                            <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 p-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M7.005 3.1a1 1 0 1 1 1.99 0l-.388 6.35a.61.61 0 0 1-1.214 0L7.005 3.1zM7 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0z"/></svg>
                            </div>
                            <h3 class="fw-bold mb-3">{{ __('Dynamic Education') }}</h3>
                            <p class="text-muted">{{ __('Interactive online courses and hands-on face-to-face training for all experience levels.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 rounded-4 transition-hover">
                            <div class="bg-success-subtle text-success rounded-circle mx-auto mb-4 p-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/></svg>
                            </div>
                            <h3 class="fw-bold mb-3">{{ __('Community Focused') }}</h3>
                            <p class="text-muted">{{ __('Join a global network of changemakers sharing wisdom and ethical economic principles.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style>
.transition-hover:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
}
.animate-up {
    animation: fadeInUp 1s ease;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.navbar-nav .nav-link:hover {
    color: #f8f9fa !important;
    text-decoration: underline;
}
.btn-group .btn.active {
    background-color: white !important;
    color: var(--bs-success) !important;
    font-weight: bold;
}
</style>
