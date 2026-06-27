<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

// Setup ethics repeater with unique client-side IDs
const initialEthics = (props.settings.about_ethics || []).map((item, idx) => ({
    id: idx + Date.now(),
    icon: item.icon || 'bi-globe2',
    title: {
        en: item.title?.en || '',
        bn: item.title?.bn || '',
    },
    description: {
        en: item.description?.en || '',
        bn: item.description?.bn || '',
    }
}));

const form = useForm({
    about_mission: {
        en: props.settings.about_mission?.en || '',
        bn: props.settings.about_mission?.bn || '',
    },
    about_vision: {
        en: props.settings.about_vision?.en || '',
        bn: props.settings.about_vision?.bn || '',
    },
    about_lms: {
        en: props.settings.about_lms?.en || '',
        bn: props.settings.about_lms?.bn || '',
    },
    about_ethics: initialEthics,
    about_image: null,
    about_header: {
        title: {
            en: props.settings.about_header?.title?.en || '',
            bn: props.settings.about_header?.title?.bn || '',
        },
        subtitle: {
            en: props.settings.about_header?.subtitle?.en || '',
            bn: props.settings.about_header?.subtitle?.bn || '',
        },
        badge: {
            en: props.settings.about_header?.badge?.en || '',
            bn: props.settings.about_header?.badge?.bn || '',
        },
    },
    about_header_bg: null,
});

const activeLanguage = ref('en');

const aboutImagePreview = ref(
    props.settings.about_image?.en 
        ? (props.settings.about_image.en.startsWith('http') 
            ? props.settings.about_image.en 
            : '/storage/' + props.settings.about_image.en)
        : null
);

const headerBgPreview = ref(
    props.settings.about_header?.bg_image 
        ? (props.settings.about_header.bg_image.startsWith('http') 
            ? props.settings.about_header.bg_image 
            : '/storage/' + props.settings.about_header.bg_image)
        : null
);

const onHeaderBgChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.about_header_bg = file;
        headerBgPreview.value = URL.createObjectURL(file);
    }
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.about_image = file;
        aboutImagePreview.value = URL.createObjectURL(file);
    }
};

const addEthicsCard = () => {
    form.about_ethics.push({
        id: Date.now(),
        icon: 'bi-award-fill',
        title: { en: '', bn: '' },
        description: { en: '', bn: '' }
    });
};

const removeEthicsCard = (id) => {
    form.about_ethics = form.about_ethics.filter(item => item.id !== id);
};

const submit = () => {
    form.post(route('admin.cms.about.update'));
};
</script>

<template>
    <Head :title="__('About Us & Terms Settings')" />

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
                                <h5 class="fw-bold mb-0"><i class="bi bi-file-person-fill me-2"></i>{{ __('About Page & Policies Manager') }}</h5>
                                <small class="opacity-75">{{ __('Customize mission, vision, dynamic ethics cards and terms page.') }}</small>
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
                            <div class="mb-5 border border-success-subtle rounded-4 p-4 bg-light-subtle">
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-layout-text-window-reverse me-2"></i>{{ __('Page Banner Header Settings') }}
                                </h4>
                                <div class="row g-4">
                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Title (English)') }}</label>
                                        <input type="text" v-model="form.about_header.title.en" class="form-control rounded-3" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Title (Bangla)') }}</label>
                                        <input type="text" v-model="form.about_header.title.bn" class="form-control rounded-3" required />
                                    </div>

                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Badge (English)') }}</label>
                                        <input type="text" v-model="form.about_header.badge.en" class="form-control rounded-3" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Badge (Bangla)') }}</label>
                                        <input type="text" v-model="form.about_header.badge.bn" class="form-control rounded-3" required />
                                    </div>

                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Description (English)') }}</label>
                                        <textarea v-model="form.about_header.subtitle.en" class="form-control rounded-3" rows="2" required></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Description (Bangla)') }}</label>
                                        <textarea v-model="form.about_header.subtitle.bn" class="form-control rounded-3" rows="2" required></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Background Cover') }}</label>
                                        <input type="file" @change="onHeaderBgChange" class="form-control rounded-3" accept="image/*" />
                                        <div v-if="headerBgPreview" class="mt-3 rounded-4 overflow-hidden border shadow-sm position-relative" style="max-height: 200px;">
                                            <img :src="headerBgPreview" class="w-100 object-fit-cover" style="max-height: 200px;" alt="Banner BG Preview" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mission & Vision Section -->
                            <div class="mb-5">
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-compass me-2"></i>{{ __('Core Values & Story') }}
                                </h4>
                                <div class="row g-4">
                                    <!-- Mission -->
                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Mission Statement (English)') }}</label>
                                        <textarea v-model="form.about_mission.en" class="form-control rounded-3" rows="3" required></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Mission Statement (Bangla)') }}</label>
                                        <textarea v-model="form.about_mission.bn" class="form-control rounded-3" rows="3" required></textarea>
                                    </div>

                                    <!-- Vision -->
                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Vision Statement (English)') }}</label>
                                        <textarea v-model="form.about_vision.en" class="form-control rounded-3" rows="3" required></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Vision Statement (Bangla)') }}</label>
                                        <textarea v-model="form.about_vision.bn" class="form-control rounded-3" rows="3" required></textarea>
                                    </div>

                                    <!-- About LMS -->
                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('About Learning Portal Description (English)') }}</label>
                                        <textarea v-model="form.about_lms.en" class="form-control rounded-3" rows="4" required></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('About Learning Portal Description (Bangla)') }}</label>
                                        <textarea v-model="form.about_lms.bn" class="form-control rounded-3" rows="4" required></textarea>
                                    </div>

                                    <!-- About Image -->
                                    <div class="col-12 mt-4">
                                        <label class="form-label fw-semibold text-muted">{{ __('About Page Image') }}</label>
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <div v-if="aboutImagePreview" class="border rounded-4 overflow-hidden shadow-sm bg-light" style="width: 160px; height: 120px;">
                                                <img :src="aboutImagePreview" class="w-100 h-100 object-fit-cover" alt="About Image Preview" />
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" @change="handleImageUpload" class="form-control rounded-3" accept="image/*" />
                                                <small class="text-muted d-block mt-1">{{ __('Recommended size: 800x600 pixels. Maximum file size: 2MB.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Ethics Repeater Section -->
                            <div class="mb-5">
                                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                    <h4 class="fw-bold text-success mb-0">
                                        <i class="bi bi-grid-3x3-gap-fill me-2"></i>{{ __('Dynamic Ethics & Core Values') }}
                                    </h4>
                                    <button type="button" @click="addEthicsCard" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add New Value Card') }}
                                    </button>
                                </div>

                                <div class="row g-4">
                                    <div v-for="(card, index) in form.about_ethics" :key="card.id" class="col-lg-6">
                                        <div class="card border border-light-subtle shadow-sm rounded-4 p-4 position-relative bg-light">
                                            <button type="button" @click="removeEthicsCard(card.id)" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Remove Value Card">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                            
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-muted">{{ __('Bootstrap Icon Class') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i :class="`bi ${card.icon || 'bi-square'}`"></i></span>
                                                        <input type="text" v-model="card.icon" class="form-control text-muted" placeholder="e.g. bi-globe2" required />
                                                    </div>
                                                </div>

                                                <!-- Title -->
                                                <div class="col-12" v-show="activeLanguage === 'en'">
                                                    <label class="form-label small fw-bold text-muted">{{ __('Card Title (English)') }}</label>
                                                    <input type="text" v-model="card.title.en" class="form-control rounded-3" required />
                                                </div>
                                                <div class="col-12" v-show="activeLanguage === 'bn'">
                                                    <label class="form-label small fw-bold text-muted">{{ __('Card Title (Bangla)') }}</label>
                                                    <input type="text" v-model="card.title.bn" class="form-control rounded-3" required />
                                                </div>

                                                <!-- Description -->
                                                <div class="col-12" v-show="activeLanguage === 'en'">
                                                    <label class="form-label small fw-bold text-muted">{{ __('Card Description (English)') }}</label>
                                                    <textarea v-model="card.description.en" class="form-control rounded-3" rows="3" required></textarea>
                                                </div>
                                                <div class="col-12" v-show="activeLanguage === 'bn'">
                                                    <label class="form-label small fw-bold text-muted">{{ __('Card Description (Bangla)') }}</label>
                                                    <textarea v-model="card.description.bn" class="form-control rounded-3" rows="3" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-if="form.about_ethics.length === 0" class="col-12 text-center text-muted py-4">
                                        <p class="mb-0">{{ __('No value cards added. Click "Add New Value Card" to create one.') }}</p>
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
