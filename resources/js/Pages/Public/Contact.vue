<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    header: Object,
    phone: String,
    email: String,
    address: String,
    facebook: String,
    twitter: String,
    youtube: String,
    google_map: String,
});
</script>

<template>
    <Head :title="__('Contact Us')" />

    <PublicLayout>
        <!-- Hero Section -->
        <PageHeader 
            :title="header.title" 
            :subtitle="header.subtitle" 
            :badge="header.badge" 
            :bg-image="header.bg_image" 
        />

        <!-- Details & Map Section -->
        <section class="py-5 bg-light">
            <div class="container py-5">
                <div class="row g-5">
                    
                    <!-- Left: Contact Details -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="fw-bold text-success mb-4">{{ __('Contact Information') }}</h3>
                                
                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <div class="bg-success-subtle text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-geo-alt-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ __('Our Office') }}</h5>
                                        <p class="text-muted mb-0">{{ address }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <div class="bg-success-subtle text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-telephone-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ __('Phone Call') }}</h5>
                                        <p class="text-muted mb-0">{{ phone }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <div class="bg-success-subtle text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-envelope-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ __('Email Address') }}</h5>
                                        <p class="text-muted mb-0">{{ email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Links -->
                            <div class="border-top pt-4 mt-4">
                                <h6 class="fw-bold text-muted text-uppercase mb-3 small">{{ __('Follow Our Channels') }}</h6>
                                <div class="d-flex gap-3">
                                    <a :href="facebook" target="_blank" class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="bi bi-facebook fs-5"></i>
                                    </a>
                                    <a :href="twitter" target="_blank" class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="bi bi-twitter-x fs-5"></i>
                                    </a>
                                    <a :href="youtube" target="_blank" class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="bi bi-youtube fs-5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Google Map -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white" style="min-height: 400px;">
                            <iframe 
                                :src="google_map" 
                                class="w-100 h-100 border-0" 
                                style="min-height: 450px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </PublicLayout>
</template>
