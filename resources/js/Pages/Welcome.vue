<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head :title="__('Welcome to Permaculture Methods')" />

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success py-3 shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <div class="bg-white text-success rounded-circle d-flex align-items-center justify-center p-1 font-bold" style="width: 35px; height: 35px; justify-content: center; font-size: 14px;">PM</div>
                <span class="fw-bold tracking-tight">{{ __('Permaculture Methods') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link text-white" href="#">{{ __('Education') }}</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">{{ __('Projects') }}</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">{{ __('About') }}</a></li>
                </ul>
                <div v-if="canLogin" class="d-flex align-items-center gap-2">
                    <!-- Language Switcher -->
                    <div class="btn-group btn-group-sm me-3" role="group">
                        <Link :href="route('locale', 'en')" 
                            class="btn btn-outline-light py-1 px-3 shadow-sm" 
                            :class="{ 'active': $page.props.translations && !$page.props.translations['Home'] }">
                            EN
                        </Link>
                        <Link :href="route('locale', 'bn')" 
                            class="btn btn-outline-light py-1 px-3 shadow-sm"
                            :class="{ 'active': $page.props.translations && $page.props.translations['Home'] }">
                            BN
                        </Link>
                    </div>

                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="btn btn-outline-light rounded-pill px-4">
                        {{ __('Dashboard') }}
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="btn btn-link text-white text-decoration-none px-4">
                            {{ __('Log in') }}
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="btn btn-light text-success fw-bold rounded-pill px-4 shadow-sm">
                            {{ __('Sign Up') }}
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-white d-flex align-items-center justify-content-center pt-5" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; min-height: 100vh;">
        <div class="container text-center pt-5">
            <h1 class="display-1 fw-bold mb-4 animate-up">
                {{ __('Cultivating a Sustainable Future') }}
            </h1>
            <p class="lead mb-5 max-w-2xl mx-auto fs-4 opacity-90">
                {{ __('Join thousands of changemakers worldwide in designing nature-based systems that regenerate our planet and communities.') }}
            </p>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mb-5">
                <button type="button" class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg">{{ __('Explore Courses') }}</button>
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

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container text-center">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center p-1 font-bold" style="width: 40px; height: 40px; justify-content: center;">PM</div>
                <h4 class="mb-0 fw-bold">{{ __('Permaculture Methods') }}</h4>
            </div>
            <p class="text-muted mb-4">&copy; 2026 {{ __('Permaculture Methods') }}. {{ __('Rooted in Earth Care, People Care, and Fair Share.') }}</p>
            <div class="social-links d-flex justify-content-center gap-3">
                <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
                <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
    </footer>
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
