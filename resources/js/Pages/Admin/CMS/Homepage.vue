<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    homepage_hero_title: {
        en: props.settings.homepage_hero_title?.en || '',
        bn: props.settings.homepage_hero_title?.bn || '',
    },
    homepage_hero_description: {
        en: props.settings.homepage_hero_description?.en || '',
        bn: props.settings.homepage_hero_description?.bn || '',
    },
    counter_courses: {
        en: props.settings.counter_courses?.en || '',
        bn: props.settings.counter_courses?.bn || '',
    },
    counter_teachers: {
        en: props.settings.counter_teachers?.en || '',
        bn: props.settings.counter_teachers?.bn || '',
    },
    counter_students: {
        en: props.settings.counter_students?.en || '',
        bn: props.settings.counter_students?.bn || '',
    },
    counter_batches: {
        en: props.settings.counter_batches?.en || '',
        bn: props.settings.counter_batches?.bn || '',
    },
    homepage_slides: (props.settings.homepage_slides || []).map(slide => ({
        title: { en: slide.title?.en || '', bn: slide.title?.bn || '' },
        description: { en: slide.description?.en || '', bn: slide.description?.bn || '' },
        image: slide.image || '',
        image_file: null,
        image_preview: slide.image || ''
    }))
});

const activeLanguage = ref('en');

const addSlide = () => {
    form.homepage_slides.push({
        title: { en: '', bn: '' },
        description: { en: '', bn: '' },
        image: '',
        image_file: null,
        image_preview: ''
    });
};

const removeSlide = (index) => {
    form.homepage_slides.splice(index, 1);
};

const handleSlideImage = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        form.homepage_slides[index].image_file = file;
        form.homepage_slides[index].image_preview = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.cms.homepage.update'));
};
</script>

<template>
    <Head :title="__('Homepage CMS Settings')" />

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
                                <h5 class="fw-bold mb-0"><i class="bi bi-house-door-fill me-2"></i>{{ __('Homepage Content Manager') }}</h5>
                                <small class="opacity-75">{{ __('Customize dynamic slider, title descriptions and statistics counters.') }}</small>
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
                            
                            <!-- Hidden elements to fallback legacy non-slider inputs if needed -->
                            <input type="hidden" v-model="form.homepage_hero_title.en" />
                            <input type="hidden" v-model="form.homepage_hero_title.bn" />
                            <input type="hidden" v-model="form.homepage_hero_description.en" />
                            <input type="hidden" v-model="form.homepage_hero_description.bn" />

                            <!-- Slider Section -->
                            <div class="mb-5">
                                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                    <h4 class="fw-bold text-success mb-0">
                                        <i class="bi bi-images me-2"></i>{{ __('Hero Viewport Carousel Slides') }}
                                    </h4>
                                    <button type="button" @click="addSlide" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Slide') }}
                                    </button>
                                </div>

                                <div v-if="form.homepage_slides.length === 0" class="text-center py-4 bg-light rounded-3 text-muted">
                                    {{ __('No slides added. Click "Add Slide" to insert one.') }}
                                </div>

                                <div v-else class="d-flex flex-column gap-4">
                                    <div v-for="(slide, index) in form.homepage_slides" :key="index" class="card border border-success-subtle rounded-3 p-4 shadow-sm bg-light-subtle">
                                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                            <h6 class="fw-bold text-success mb-0">{{ __('Slide') }} #{{ index + 1 }}</h6>
                                            <button type="button" @click="removeSlide(index)" class="btn btn-outline-danger btn-sm border-0 rounded-circle animate-hover" title="Delete Slide">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>

                                        <div class="row g-3">
                                            <!-- Slide Background Image Upload -->
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted">{{ __('Background Image') }}</label>
                                                <div class="d-flex flex-column align-items-center gap-2">
                                                    <!-- Image Preview -->
                                                    <div class="border rounded-3 overflow-hidden d-flex align-items-center justify-content-center bg-white" style="width: 100%; height: 160px;">
                                                        <img v-if="slide.image_preview" :src="slide.image_preview" class="w-100 h-100 object-fit-cover" alt="Slide Background Preview" />
                                                        <span v-else class="text-muted small">{{ __('No Cover Photo') }}</span>
                                                    </div>
                                                    <!-- File Selector -->
                                                    <input type="file" @change="handleSlideImage($event, index)" class="form-control form-control-sm" accept="image/*" />
                                                </div>
                                            </div>

                                            <!-- Slide Texts -->
                                            <div class="col-md-8">
                                                <div class="row g-3">
                                                    <!-- Title -->
                                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                                        <label class="form-label fw-semibold text-muted">{{ __('Slide Title (English)') }}</label>
                                                        <input type="text" v-model="slide.title.en" class="form-control rounded-3" />
                                                    </div>
                                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                                        <label class="form-label fw-semibold text-muted">{{ __('Slide Title (Bangla)') }}</label>
                                                        <input type="text" v-model="slide.title.bn" class="form-control rounded-3" />
                                                    </div>

                                                    <!-- Description -->
                                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                                        <label class="form-label fw-semibold text-muted">{{ __('Slide Sub-description (English)') }}</label>
                                                        <textarea v-model="slide.description.en" class="form-control rounded-3" rows="3"></textarea>
                                                    </div>
                                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                                        <label class="form-label fw-semibold text-muted">{{ __('Slide Sub-description (Bangla)') }}</label>
                                                        <textarea v-model="slide.description.bn" class="form-control rounded-3" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Counters Section -->
                            <div>
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-calculator me-2"></i>{{ __('Statistics Counter Metrics') }}
                                </h4>
                                <div class="row g-4">
                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Partner Organizations Counter (English)') }}</label>
                                        <input type="text" v-model="form.counter_courses.en" class="form-control rounded-3 py-2" />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Partner Organizations Counter (Bangla)') }}</label>
                                        <input type="text" v-model="form.counter_courses.bn" class="form-control rounded-3 py-2" />
                                    </div>

                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Certified Graduates Counter (English)') }}</label>
                                        <input type="text" v-model="form.counter_students.en" class="form-control rounded-3 py-2" />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Certified Graduates Counter (Bangla)') }}</label>
                                        <input type="text" v-model="form.counter_students.bn" class="form-control rounded-3 py-2" />
                                    </div>

                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Expert Instructors Counter (English)') }}</label>
                                        <input type="text" v-model="form.counter_teachers.en" class="form-control rounded-3 py-2" />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Expert Instructors Counter (Bangla)') }}</label>
                                        <input type="text" v-model="form.counter_teachers.bn" class="form-control rounded-3 py-2" />
                                    </div>

                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Active Batches Counter (English)') }}</label>
                                        <input type="text" v-model="form.counter_batches.en" class="form-control rounded-3 py-2" />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Active Batches Counter (Bangla)') }}</label>
                                        <input type="text" v-model="form.counter_batches.bn" class="form-control rounded-3 py-2" />
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
