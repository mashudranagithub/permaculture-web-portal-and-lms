<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    // Add any props needed
});

const activeTab = ref('bn');

const form = useForm({
    title: { en: '', bn: '' },
    description: { en: '', bn: '' },
    short_description: { en: '', bn: '' },
    price: 0,
    level: 'Foundation',
    delivery_mode: 'online',
    duration: '',
    status: 'draft',
    image: null,
    banner_image: null,
    meta_title: { en: '', bn: '' },
    meta_description: { en: '', bn: '' },
});

const imagePreview = ref(null);
const bannerPreview = ref(null);

const handleImageChange = (e, target) => {
    const file = e.target.files[0];
    if (file) {
        form[target] = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            if (target === 'image') imagePreview.value = event.target.result;
            if (target === 'banner_image') bannerPreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('courses.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Success logic
        }
    });
};
</script>

<template>
    <Head :title="__('Create Course')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center">
                <i class="bx bx-book-open me-2 text-success"></i>
                {{ __('Course Management') }}
            </div>
        </template>

        <div class="row pb-5 mt-4">
            <div class="col-12">
                <form @submit.prevent="submit">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-success-subtle text-success p-2 rounded-2 me-3">
                                    <i class="bx bx-plus-circle fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark">{{ __('New Course Creation') }}</h5>
                                    <p class="mb-0 text-muted small">{{ __('Define curriculum, pricing, and SEO details.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-4">
                                <!-- Left Column: Core Content -->
                                <div class="col-lg-8">
                                    <!-- Section 1: Bilingual Content -->
                                    <fieldset class="premium-fieldset mb-4 border rounded-3 p-4 animate__animated animate__fadeIn">
                                        <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                            <i class="bx bx-detail me-1"></i> {{ __('COURSE CONTENT') }}
                                        </legend>

                                        <div class="bilingual-tabs-wrapper rounded-2 border p-1 bg-light mb-4 d-flex mt-2">
                                            <button @click="activeTab = 'bn'" type="button" 
                                                class="btn flex-fill border-0 py-2 fw-bold small transition-all" 
                                                :class="activeTab === 'bn' ? 'bg-success text-white shadow-sm' : 'text-muted'">
                                                {{ __('Bangla Content') }}
                                            </button>
                                            <button @click="activeTab = 'en'" type="button" 
                                                class="btn flex-fill border-0 py-2 fw-bold small transition-all" 
                                                :class="activeTab === 'en' ? 'bg-success text-white shadow-sm' : 'text-muted'">
                                                {{ __('English Content') }}
                                            </button>
                                        </div>

                                        <div class="tab-content border rounded-2 p-4 bg-white shadow-sm">
                                            <!-- Bangla Section -->
                                            <div v-show="activeTab === 'bn'" class="animate__animated animate__fadeIn">
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Course Title (Bangla)') }}</label>
                                                    <input v-model="form.title.bn" type="text" class="form-control standard-height border-2 shadow-none rounded-2" placeholder="উদাঃ পারমাকালচার ডিজাইন সার্টিফিকেট" required>
                                                    <div v-if="form.errors['title.bn']" class="text-danger small mt-1">{{ form.errors['title.bn'] }}</div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Short Description (Bangla)') }}</label>
                                                    <RichTextEditor v-model="form.short_description.bn" :height="150" :placeholder="__('সারসংক্ষেপ এখানে লিখুন...')" />
                                                    <div v-if="form.errors['short_description.bn']" class="text-danger small mt-1">{{ form.errors['short_description.bn'] }}</div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Full Description (Bangla)') }}</label>
                                                    <RichTextEditor v-model="form.description.bn" :placeholder="__('কোর্স বিবরণ বাংলায় লিখুন...')" />
                                                    <div v-if="form.errors['description.bn']" class="text-danger small mt-1">{{ form.errors['description.bn'] }}</div>
                                                </div>
                                            </div>

                                            <!-- English Section -->
                                            <div v-show="activeTab === 'en'" class="animate__animated animate__fadeIn">
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Course Title (English)') }}</label>
                                                    <input v-model="form.title.en" type="text" class="form-control standard-height border-2 shadow-none rounded-2" :placeholder="__('e.g. Permaculture Design Certificate')" required>
                                                    <div v-if="form.errors['title.en']" class="text-danger small mt-1">{{ form.errors['title.en'] }}</div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Short Description (English)') }}</label>
                                                    <RichTextEditor v-model="form.short_description.en" :height="150" :placeholder="__('Brief overview for search results...')" />
                                                    <div v-if="form.errors['short_description.en']" class="text-danger small mt-1">{{ form.errors['short_description.en'] }}</div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Full Description (English)') }}</label>
                                                    <RichTextEditor v-model="form.description.en" :placeholder="__('Enter full course overview in English...')" />
                                                    <div v-if="form.errors['description.en']" class="text-danger small mt-1">{{ form.errors['description.en'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <!-- Section 2: SEO Meta -->
                                    <fieldset class="premium-fieldset border rounded-3 p-4 animate__animated animate__fadeIn">
                                        <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                            <i class="bx bx-search-alt me-1"></i> {{ __('SEO CONFIGURATION') }}
                                        </legend>
                                        <div class="row g-4 mt-1">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Meta Title (BN)') }}</label>
                                                <input v-model="form.meta_title.bn" type="text" class="form-control standard-height border-2 shadow-none rounded-2">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Meta Title (EN)') }}</label>
                                                <input v-model="form.meta_title.en" type="text" class="form-control standard-height border-2 shadow-none rounded-2">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Meta Description') }}</label>
                                                <RichTextEditor v-model="form.meta_description.en" :height="120" />
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <!-- Right Column: Settings & Media -->
                                <div class="col-lg-4">
                                    <!-- Course Settings -->
                                    <fieldset class="premium-fieldset mb-4 border rounded-3 p-4 animate__animated animate__fadeIn">
                                        <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                            <i class="bx bx-cog me-1"></i> {{ __('SETTINGS') }}
                                        </legend>
                                        
                                        <div class="mb-4 mt-2">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Base Price (BDT)') }}</label>
                                            <div class="input-group standard-height">
                                                <span class="input-group-text bg-light border-2 border-end-0 fw-bold px-3">৳</span>
                                                <input v-model="form.price" type="number" class="form-control border-2 border-start-0 shadow-none" required>
                                            </div>
                                            <div v-if="form.errors.price" class="text-danger small mt-1">{{ form.errors.price }}</div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Course Level') }}</label>
                                            <Select2 v-model="form.level" :placeholder="__('Select Level')" allowClear>
                                                <option value="Foundation">{{ __('Foundation') }}</option>
                                                <option value="Intermediate">{{ __('Intermediate') }}</option>
                                                <option value="Advanced">{{ __('Advanced') }}</option>
                                            </Select2>
                                            <div v-if="form.errors.level" class="text-danger small mt-1">{{ form.errors.level }}</div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Delivery Mode') }}</label>
                                            <div class="d-flex flex-column gap-2 p-2 bg-light rounded-2 border border-2">
                                                <div class="form-check custom-radio">
                                                    <input v-model="form.delivery_mode" class="form-check-input" type="radio" value="online" id="modeOnline">
                                                    <label class="form-check-label small fw-bold text-dark" for="modeOnline">{{ __('Online Only') }}</label>
                                                </div>
                                                <div class="form-check custom-radio">
                                                    <input v-model="form.delivery_mode" class="form-check-input" type="radio" value="offline" id="modeOffline">
                                                    <label class="form-check-label small fw-bold text-dark" for="modeOffline">{{ __('In-Person Only') }}</label>
                                                </div>
                                                <div class="form-check custom-radio">
                                                    <input v-model="form.delivery_mode" class="form-check-input" type="radio" value="hybrid" id="modeHybrid">
                                                    <label class="form-check-label small fw-bold text-dark" for="modeHybrid">{{ __('Hybrid Mode') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Duration') }}</label>
                                            <input v-model="form.duration" type="text" class="form-control standard-height border-2 shadow-none rounded-2" :placeholder="__('e.g. 12 Weeks')">
                                            <div v-if="form.errors.duration" class="text-danger small mt-1">{{ form.errors.duration }}</div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Status') }}</label>
                                            <Select2 v-model="form.status" :placeholder="__('Select Status')" allowClear>
                                                <option value="draft">{{ __('Draft') }}</option>
                                                <option value="published">{{ __('Published') }}</option>
                                                <option value="archived">{{ __('Archived') }}</option>
                                            </Select2>
                                        </div>
                                    </fieldset>

                                    <!-- Media Sidebar -->
                                    <fieldset class="premium-fieldset border rounded-3 p-4 animate__animated animate__fadeIn">
                                        <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                            <i class="bx bx-image me-1"></i> {{ __('MEDIA') }}
                                        </legend>
                                        
                                        <div class="mb-4 mt-2">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Thumbnail Image') }}</label>
                                            <div class="upload-container border-2 border-dashed rounded-3 p-3 text-center bg-light transition-all">
                                                <div v-if="imagePreview" class="position-relative">
                                                    <img :src="imagePreview" class="img-fluid rounded-2 mb-2 shadow-sm" style="max-height: 150px;">
                                                    <button @click="imagePreview = null; form.image = null" type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 rounded-circle shadow">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </div>
                                                <div v-else class="py-3">
                                                    <i class="bx bx-cloud-upload fs-1 text-muted opacity-50"></i>
                                                    <p class="small text-muted mb-2">{{ __('Drop image or click to upload') }}</p>
                                                </div>
                                                <input type="file" @change="e => handleImageChange(e, 'image')" class="form-control form-control-sm border-0 bg-transparent shadow-none" accept="image/*">
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Course Banner') }}</label>
                                            <div class="upload-container border-2 border-dashed rounded-3 p-3 text-center bg-light transition-all">
                                                <div v-if="bannerPreview" class="position-relative">
                                                    <img :src="bannerPreview" class="img-fluid rounded-2 mb-2 shadow-sm" style="max-height: 100px;">
                                                    <button @click="bannerPreview = null; form.banner_image = null" type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 rounded-circle shadow">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </div>
                                                <div v-else class="py-3">
                                                    <i class="bx bx-image-add fs-1 text-muted opacity-50"></i>
                                                </div>
                                                <input type="file" @change="e => handleImageChange(e, 'banner_image')" class="form-control form-control-sm border-0 bg-transparent shadow-none" accept="image/*">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-light border-top p-4 d-flex justify-content-end gap-3">
                            <Link :href="route('courses.index')" class="btn btn-link text-muted fw-bold text-decoration-none px-4">{{ __('Cancel') }}</Link>
                            <button @click="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="bx bx-check-circle me-2"></i>{{ __('Save & Create Course') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.premium-fieldset { border-color: #e9ecef !important; }
.standard-height { height: 42px !important; }
.form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1); }
.border-dashed { border-style: dashed !important; border-width: 2px !important; }

.upload-container {
    cursor: pointer;
}
.upload-container:hover {
    background-color: #fff !important;
    border-color: #198754 !important;
}

.transition-all { transition: all 0.25s ease; }
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }

.icon-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

legend { font-size: 0.8rem !important; letter-spacing: 0.5px; transform: translateY(-5px); }

.custom-radio .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.btn-xs {
    padding: 1px 5px;
    font-size: 0.75rem;
}
</style>
