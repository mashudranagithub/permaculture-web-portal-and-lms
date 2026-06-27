<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    wide: { type: Boolean, default: false },
});
</script>

<template>
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center bg-light px-3 py-5" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
        <div class="mb-4">
            <Link href="/" class="text-decoration-none d-flex flex-column align-items-center">
                <img src="/favicon.png" alt="Logo" class="rounded-circle shadow-lg" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff;">
                <h4 class="mt-3 fw-bold text-success text-uppercase tracking-wider font-outfit">{{ __('Regenerative Systems') }}</h4>
            </Link>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden w-100" :style="wide ? 'max-width: 760px;' : 'max-width: 450px;'">
            <div class="card-body p-4 p-md-5">
                <slot />
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-muted small">&copy; 2026 Regenerative Systems. {{ __('Rooted in Earth Care.') }}</p>
        </div>
    </div>
</template>

<style>
.font-outfit {
    font-family: 'Outfit', sans-serif;
}
</style>
