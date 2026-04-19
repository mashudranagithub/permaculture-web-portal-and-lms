<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center bg-light px-3 py-5" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
        <div class="mb-4">
            <Link href="/" class="text-decoration-none d-flex flex-column align-items-center">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-lg" style="width: 70px; height: 70px; font-size: 24px;">PM</div>
                <h4 class="mt-3 fw-bold text-success text-uppercase tracking-wider font-outfit">{{ __('Permaculture Methods') }}</h4>
            </Link>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 450px;">
            <div class="card-body p-4 p-md-5">
                <slot />
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-muted small">&copy; 2026 Permaculture Methods. {{ __('Rooted in Earth Care.') }}</p>
        </div>
    </div>
</template>

<style>
.font-outfit {
    font-family: 'Outfit', sans-serif;
}
</style>
