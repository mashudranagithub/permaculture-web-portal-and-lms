<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    contact_phone: {
        en: props.settings.contact_phone?.en || '',
        bn: props.settings.contact_phone?.bn || '',
    },
    contact_address: {
        en: props.settings.contact_address?.en || '',
        bn: props.settings.contact_address?.bn || '',
    },
    contact_email: {
        en: props.settings.contact_email?.en || '',
        bn: props.settings.contact_email?.bn || '',
    },
    contact_facebook: {
        en: props.settings.contact_facebook?.en || '',
        bn: props.settings.contact_facebook?.bn || '',
    },
    contact_twitter: {
        en: props.settings.contact_twitter?.en || '',
        bn: props.settings.contact_twitter?.bn || '',
    },
    contact_youtube: {
        en: props.settings.contact_youtube?.en || '',
        bn: props.settings.contact_youtube?.bn || '',
    },
    contact_google_map: {
        en: props.settings.contact_google_map?.en || '',
        bn: props.settings.contact_google_map?.bn || '',
    },
    footer_description: {
        en: props.settings.footer_description?.en || '',
        bn: props.settings.footer_description?.bn || '',
    },
    contact_header: {
        title: {
            en: props.settings.contact_header?.title?.en || '',
            bn: props.settings.contact_header?.title?.bn || '',
        },
        subtitle: {
            en: props.settings.contact_header?.subtitle?.en || '',
            bn: props.settings.contact_header?.subtitle?.bn || '',
        },
        badge: {
            en: props.settings.contact_header?.badge?.en || '',
            bn: props.settings.contact_header?.badge?.bn || '',
        },
    },
    contact_header_bg: null,
});

const activeLanguage = ref('en');

const headerBgPreview = ref(
    props.settings.contact_header?.bg_image 
        ? (props.settings.contact_header.bg_image.startsWith('http') 
            ? props.settings.contact_header.bg_image 
            : '/storage/' + props.settings.contact_header.bg_image)
        : null
);

const onHeaderBgChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.contact_header_bg = file;
        headerBgPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.cms.contact.update'));
};
</script>

<template>
    <Head :title="__('Contact Us Settings')" />

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
                                <h5 class="fw-bold mb-0"><i class="bi bi-envelope-paper-fill me-2"></i>{{ __('Contact & Social Channels Manager') }}</h5>
                                <small class="opacity-75">{{ __('Customize direct phone numbers, email support, socials, and Google maps embed code.') }}</small>
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
                                        <input type="text" v-model="form.contact_header.title.en" class="form-control rounded-3" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Title (Bangla)') }}</label>
                                        <input type="text" v-model="form.contact_header.title.bn" class="form-control rounded-3" required />
                                    </div>

                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Badge (English)') }}</label>
                                        <input type="text" v-model="form.contact_header.badge.en" class="form-control rounded-3" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Badge (Bangla)') }}</label>
                                        <input type="text" v-model="form.contact_header.badge.bn" class="form-control rounded-3" required />
                                    </div>

                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Description (English)') }}</label>
                                        <textarea v-model="form.contact_header.subtitle.en" class="form-control rounded-3" rows="2" required></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Banner Description (Bangla)') }}</label>
                                        <textarea v-model="form.contact_header.subtitle.bn" class="form-control rounded-3" rows="2" required></textarea>
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

                            <!-- Address & Support Channels -->
                            <div class="mb-5">
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-geo-alt-fill me-2"></i>{{ __('Support Contacts & Address') }}
                                </h4>
                                <div class="row g-4">
                                    <!-- Phone -->
                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Phone Number (English)') }}</label>
                                        <input type="text" v-model="form.contact_phone.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Phone Number (Bangla)') }}</label>
                                        <input type="text" v-model="form.contact_phone.bn" class="form-control rounded-3 py-2" required />
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Support Email (English)') }}</label>
                                        <input type="email" v-model="form.contact_email.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-md-6" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Support Email (Bangla)') }}</label>
                                        <input type="email" v-model="form.contact_email.bn" class="form-control rounded-3 py-2" required />
                                    </div>

                                    <!-- Address -->
                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Office Address (English)') }}</label>
                                        <input type="text" v-model="form.contact_address.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Office Address (Bangla)') }}</label>
                                        <input type="text" v-model="form.contact_address.bn" class="form-control rounded-3 py-2" required />
                                    </div>
                                </div>
                            </div>

                            <!-- Social Ecosystem -->
                            <div class="mb-5">
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-share-fill me-2"></i>{{ __('Social Channels') }}
                                </h4>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted"><i class="bi bi-facebook text-primary me-1"></i>{{ __('Facebook link') }}</label>
                                        <input type="text" v-model="form.contact_facebook.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted"><i class="bi bi-twitter-x text-dark me-1"></i>{{ __('Twitter link') }}</label>
                                        <input type="text" v-model="form.contact_twitter.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted"><i class="bi bi-youtube text-danger me-1"></i>{{ __('YouTube link') }}</label>
                                        <input type="text" v-model="form.contact_youtube.en" class="form-control rounded-3 py-2" required />
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Map -->
                            <div class="mb-5">
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-map-fill me-2"></i>{{ __('Google Maps Location Embed') }}
                                </h4>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted">{{ __('Google Maps Embed URL') }}</label>
                                        <textarea v-model="form.contact_google_map.en" class="form-control rounded-3 font-monospace text-xs" rows="3" required></textarea>
                                        <small class="text-muted d-block mt-1">Paste the <code>src</code> attribute of Google Maps iframe embed code here.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Description Settings -->
                            <div>
                                <h4 class="fw-bold text-success mb-4 border-bottom pb-2">
                                    <i class="bi bi-paragraph me-2"></i>{{ __('Global Footer Branding Text') }}
                                </h4>
                                <div class="row g-4">
                                    <div class="col-12" v-show="activeLanguage === 'en'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Footer Description (English)') }}</label>
                                        <textarea v-model="form.footer_description.en" class="form-control rounded-3" rows="3"></textarea>
                                    </div>
                                    <div class="col-12" v-show="activeLanguage === 'bn'">
                                        <label class="form-label fw-semibold text-muted">{{ __('Footer Description (Bangla)') }}</label>
                                        <textarea v-model="form.footer_description.bn" class="form-control rounded-3" rows="3"></textarea>
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
