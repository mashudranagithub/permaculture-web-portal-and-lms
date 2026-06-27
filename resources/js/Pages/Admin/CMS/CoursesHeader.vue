<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    courses_header: {
        title: {
            en: props.settings.courses_header?.title?.en || '',
            bn: props.settings.courses_header?.title?.bn || '',
        },
        subtitle: {
            en: props.settings.courses_header?.subtitle?.en || '',
            bn: props.settings.courses_header?.subtitle?.bn || '',
        },
        badge: {
            en: props.settings.courses_header?.badge?.en || '',
            bn: props.settings.courses_header?.badge?.bn || '',
        },
    },
    courses_header_bg: null,
});

const activeLanguage = ref('en');

const headerBgPreview = ref(
    props.settings.courses_header?.bg_image 
        ? (props.settings.courses_header.bg_image.startsWith('http') 
            ? props.settings.courses_header.bg_image 
            : '/storage/' + props.settings.courses_header.bg_image)
        : null
);

const onHeaderBgChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.courses_header_bg = file;
        headerBgPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.cms.courses-header.update'));
};
</script>

<template>
    <Head :title="__('Course Catalog Header CMS')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Web Portal CMS') }}
        </template>

        <div class="container-fluid px-4 px-md-5">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                        
                        <!-- Header with language toggle -->
                        <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-0"><i class="bi bi-journal-bookmark me-2"></i>{{ __('Course Catalog Page Header') }}</h5>
                                <small class="opacity-75">{{ __('Customize the banner title, description, and cover image on the browse courses catalog.') }}</small>
                            </div>
                            <div class="btn-group btn-group-sm rounded-pill overflow-hidden border border-white-50 bg-success-subtle">
                                <button type="button" @click="activeLanguage = 'en'" class="btn py-1 px-3 fw-bold" :class="activeLanguage === 'en' ? 'btn-white text-success' : 'btn-link text-white text-decoration-none'">
                                    ENGLISH
                                </button>
                                <button type="button" @click="activeLanguage = 'bn'" class="btn py-1 px-3 fw-bold" :class="activeLanguage === 'bn' ? 'btn-white text-success' : 'btn-link text-white text-decoration-none'">
                                    BANGLA
                                </button>
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="p-4 p-md-5 bg-white">
                            
                            <!-- Page Header Section -->
                            <div class="mb-4">
                                <div class="row g-4">
                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Title (English)') }}</label>
                                        <input type="text" v-model="form.courses_header.title.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Title (Bangla)') }}</label>
                                        <input type="text" v-model="form.courses_header.title.bn" class="form-control rounded-3 py-2" required />
                                    </div>

                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Badge (English)') }}</label>
                                        <input type="text" v-model="form.courses_header.badge.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Badge (Bangla)') }}</label>
                                        <input type="text" v-model="form.courses_header.badge.bn" class="form-control rounded-3 py-2" required />
                                    </div>

                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Description (English)') }}</label>
                                        <textarea v-model="form.courses_header.subtitle.en" class="form-control rounded-3 font-sans" rows="3" required></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Description (Bangla)') }}</label>
                                        <textarea v-model="form.courses_header.subtitle.bn" class="form-control rounded-3 font-sans" rows="3" required></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Background Cover') }}</label>
                                        <input type="file" @change="onHeaderBgChange" class="form-control rounded-3" accept="image/*" />
                                        <div v-if="headerBgPreview" class="mt-3 rounded-4 overflow-hidden border shadow-sm position-relative" style="max-height: 250px;">
                                            <img :src="headerBgPreview" class="w-100 object-fit-cover" style="max-height: 250px;" alt="Banner BG Preview" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <div class="d-flex justify-content-end gap-2 border-top pt-4 mt-5">
                                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" :disabled="form.processing">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ __('Save Changes') }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.btn-white {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>
