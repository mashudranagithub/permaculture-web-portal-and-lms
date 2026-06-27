<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    header: Object,
    partners: Array,
});
</script>

<template>
    <Head :title="__('Our Partners')" />

    <PublicLayout>
        <PageHeader 
            :title="header.title" 
            :subtitle="header.subtitle" 
            :badge="header.badge" 
            :bg-image="header.bg_image" 
        />

        <section class="py-5 bg-light" style="min-height: 80vh;">
            <div class="container py-lg-5">

                <div v-if="partners.length > 0" class="row g-4 justify-content-center">
                    <div v-for="partner in partners" :key="partner.id" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white transition-hover">
                            <!-- Logo Wrapper -->
                            <div class="mx-auto mb-4 bg-white rounded-circle shadow-sm border p-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <img :src="partner.logo_url" class="img-fluid rounded-circle" style="max-height: 100%; object-fit: contain;" alt="Partner Logo">
                            </div>

                            <h4 class="fw-bold text-dark mb-2">{{ partner.name }}</h4>
                            <p class="text-muted small line-clamp-3 mb-4">{{ partner.description || __('No description available.') }}</p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small">
                                    <i class="bi bi-book me-1"></i> {{ partner.courses_count }} {{ __('Courses') }}
                                </span>
                                <Link :href="route('partners.show', partner.slug)" class="btn btn-outline-success rounded-pill px-4 btn-sm fw-bold">
                                    {{ __('View Profile') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-5">
                    <div class="bg-success-subtle text-success rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-buildings fs-1"></i>
                    </div>
                    <h4 class="fw-bold">{{ __('No Active Partners') }}</h4>
                    <p class="text-muted mb-4">{{ __('Join us today and be the first partner organization.') }}</p>
                    <Link :href="route('org.register')" class="btn btn-success rounded-pill px-5 py-3 fw-bold shadow-sm">
                        {{ __('Register Your Organization') }}
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.transition-hover {
    transition: all 0.3s ease-in-out;
}
.transition-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
