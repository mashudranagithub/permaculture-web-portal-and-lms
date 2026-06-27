<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    header: Object,
    privacy_content: String,
});
</script>

<template>
    <Head :title="__('Privacy Policy')" />

    <PublicLayout>
        <PageHeader 
            :title="header.title" 
            :subtitle="header.subtitle" 
            :badge="header.badge" 
            :bg-image="header.bg_image" 
        />

        <section class="py-5 bg-light">
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                            <p class="text-muted" style="white-space: pre-line; line-height: 1.8;">
                                {{ privacy_content }}
                            </p>

                            <hr class="my-5">

                            <div class="text-center text-muted small">
                                <p class="mb-0">{{ __('If you have any questions regarding this privacy policy, please contact us at support@regenerative.systems') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
