<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div class="min-vh-100 d-flex flex-column bg-light">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success py-3 shadow-sm sticky-top">
            <div class="container">
                <Link class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center p-1 fw-bold" style="width: 35px; height: 35px; font-size: 14px;">PM</div>
                    <span class="fw-bold tracking-tight">Permaculture Methods</span>
                </Link>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item">
                            <Link :href="route('courses.browse')" class="nav-link text-white" :class="{ 'active fw-bold': route().current('courses.browse') }">{{ __('Education') }}</Link>
                        </li>
                        <li class="nav-item">
                            <Link :href="route('org.register')" class="nav-link text-white" :class="{ 'active fw-bold': route().current('org.register') }">{{ __('Partner with Us') }}</Link>
                        </li>
                        <li class="nav-item"><a class="nav-link text-white" href="#">{{ __('Projects') }}</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#">{{ __('About') }}</a></li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">
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

                        <template v-if="user">
                            <Link :href="route('dashboard')" class="btn btn-outline-light rounded-pill px-4 shadow-sm">
                                {{ __('Dashboard') }}
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('org.register')" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-success">
                                {{ __('Become a Partner') }}
                            </Link>
                            <Link :href="route('login')" class="btn btn-outline-light rounded-pill px-4 shadow-sm fw-bold">
                                {{ __('Log in') }}
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-white py-5 mt-auto">
            <div class="container text-center">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center p-1 fw-bold" style="width: 40px; height: 40px;">PM</div>
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
    </div>
</template>

<style scoped>
.navbar-nav .nav-link:hover {
    color: #f8f9fa !important;
}
.btn-group .btn.active {
    background-color: white !important;
    color: var(--bs-success) !important;
    font-weight: bold;
}
.nav-link.active {
    border-bottom: 2px solid white;
}
</style>
