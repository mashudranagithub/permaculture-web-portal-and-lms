<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    // Add any props needed
});

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

const activeTab = ref('bn');
</script>

<template>
    <Head :title="__('Create Course')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Course Management') }}
        </template>

        <div class="row">
            <div class="col-xl-8">
                <form @submit.prevent="submit">
                    <div class="card border-0 shadow-sm rounded-1 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-pencil-square text-success me-2"></i>{{ __('Course Details') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Language Tabs -->
                            <ul class="nav nav-pills nav-fill mb-4 bg-light p-1 rounded-1" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-1 py-2" :class="{ 'active bg-success text-white': activeTab === 'bn' }" @click="activeTab = 'bn'" type="button">
                                        <i class="bi bi-type me-2"></i>{{ __('Bangla Content') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-1 py-2" :class="{ 'active bg-success text-white': activeTab === 'en' }" @click="activeTab = 'en'" type="button">
                                        <i class="bi bi-alphabet me-2"></i>{{ __('English Content') }}
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content border p-4 rounded-1">
                                <!-- Bangla Section -->
                                <div v-show="activeTab === 'bn'" class="animate__animated animate__fadeIn">
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Course Title (Bangla)') }}</label>
                                        <input v-model="form.title.bn" type="text" class="form-control rounded-1" placeholder="উদাঃ পারমাকালচার ডিজাইন সার্টিফিকেট">
                                        <div v-if="form.errors['title.bn']" class="text-danger small mt-1">{{ form.errors['title.bn'] }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Short Description (Bangla)') }}</label>
                                        <RichTextEditor v-model="form.short_description.bn" :height="150" :placeholder="__('সারসংক্ষেপ এখানে লিখুন...')" />
                                        <div v-if="form.errors['short_description.bn']" class="text-danger small mt-1">{{ form.errors['short_description.bn'] }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Full Description (Bangla)') }}</label>
                                        <RichTextEditor v-model="form.description.bn" :placeholder="__('কোর্স বিবরণ বাংলায় লিখুন...')" />
                                        <div v-if="form.errors['description.bn']" class="text-danger small mt-1">{{ form.errors['description.bn'] }}</div>
                                    </div>
                                </div>

                                <!-- English Section -->
                                <div v-show="activeTab === 'en'" class="animate__animated animate__fadeIn">
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Course Title (English)') }}</label>
                                        <input v-model="form.title.en" type="text" class="form-control rounded-1" :placeholder="__('e.g. Permaculture Design Certificate')">
                                        <div v-if="form.errors['title.en']" class="text-danger small mt-1">{{ form.errors['title.en'] }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Short Description (English)') }}</label>
                                        <RichTextEditor v-model="form.short_description.en" :height="150" :placeholder="__('Brief overview for search results...')" />
                                        <div v-if="form.errors['short_description.en']" class="text-danger small mt-1">{{ form.errors['short_description.en'] }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Full Description (English)') }}</label>
                                        <RichTextEditor v-model="form.description.en" :placeholder="__('Enter full course overview in English...')" />
                                        <div v-if="form.errors['description.en']" class="text-danger small mt-1">{{ form.errors['description.en'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Data Section -->
                    <div class="card border-0 shadow-sm rounded-1 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-search text-success me-2"></i>{{ __('SEO Configuration') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Meta Title (BN)') }}</label>
                                    <input v-model="form.meta_title.bn" type="text" class="form-control rounded-1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Meta Title (EN)') }}</label>
                                    <input v-model="form.meta_title.en" type="text" class="form-control rounded-1">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Meta Description (BN)') }}</label>
                                    <RichTextEditor v-model="form.meta_description.bn" :height="100" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Meta Description (EN)') }}</label>
                                    <RichTextEditor v-model="form.meta_description.en" :height="100" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4">
                <!-- Settings Sidebar -->
                <div class="card border-0 shadow-sm rounded-1 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-gear-fill text-success me-2"></i>{{ __('Course Settings') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Base Price (BDT)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-cash-stack"></i></span>
                                <input v-model="form.price" type="number" class="form-control rounded-1 border-start-0 ps-0">
                            </div>
                            <div v-if="form.errors.price" class="text-danger small mt-1">{{ form.errors.price }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Course Level') }}</label>
                            <select v-model="form.level" class="form-select rounded-1">
                                <option value="Foundation">{{ __('Foundation') }}</option>
                                <option value="Intermediate">{{ __('Intermediate') }}</option>
                                <option value="Advanced">{{ __('Advanced') }}</option>
                            </select>
                            <div v-if="form.errors.level" class="text-danger small mt-1">{{ form.errors.level }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Delivery Mode') }}</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check custom-radio">
                                    <input v-model="form.delivery_mode" class="form-check-input" type="radio" value="online" id="modeOnline">
                                    <label class="form-check-label" for="modeOnline">{{ __('Online Only') }}</label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input v-model="form.delivery_mode" class="form-check-input" type="radio" value="offline" id="modeOffline">
                                    <label class="form-check-label" for="modeOffline">{{ __('In-Person Only') }}</label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input v-model="form.delivery_mode" class="form-check-input" type="radio" value="hybrid" id="modeHybrid">
                                    <label class="form-check-label" for="modeHybrid">{{ __('Hybrid Mode') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Duration') }}</label>
                            <input v-model="form.duration" type="text" class="form-control rounded-1" :placeholder="__('e.g. 12 Weeks')">
                            <div v-if="form.errors.duration" class="text-danger small mt-1">{{ form.errors.duration }}</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Status') }}</label>
                            <select v-model="form.status" class="form-select rounded-1">
                                <option value="draft">{{ __('Draft') }}</option>
                                <option value="published">{{ __('Published') }}</option>
                                <option value="archived">{{ __('Archived') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="card border-0 shadow-sm rounded-1 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-images text-success me-2"></i>{{ __('Course Media') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Thumbnail Image') }}</label>
                            <div class="upload-vessel border rounded-1 p-3 text-center bg-light position-relative">
                                <img v-if="imagePreview" :src="imagePreview" class="img-fluid rounded-1 mb-2" style="max-height: 150px;">
                                <i v-else class="bi bi-image fs-1 text-muted d-block mb-2"></i>
                                <input type="file" @change="e => handleImageChange(e, 'image')" class="form-control form-control-sm border-0 bg-white" accept="image/*">
                                <p class="small text-muted mb-0 mt-2">{{ __('Recommended: 600x400px') }}</p>
                            </div>
                            <div v-if="form.errors.image" class="text-danger small mt-1">{{ form.errors.image }}</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Banner Image') }}</label>
                            <div class="upload-vessel border rounded-1 p-3 text-center bg-light position-relative">
                                <img v-if="bannerPreview" :src="bannerPreview" class="img-fluid rounded-1 mb-2" style="max-height: 100px;">
                                <i v-else class="bi bi-aspect-ratio fs-1 text-muted d-block mb-2"></i>
                                <input type="file" @change="e => handleImageChange(e, 'banner_image')" class="form-control form-control-sm border-0 bg-white" accept="image/*">
                                <p class="small text-muted mb-0 mt-2">{{ __('Recommended: 1200x300px') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top p-4">
                        <button @click="submit" class="btn btn-success btn-lg w-100 rounded-1 fw-bold shadow-sm" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            <i class="bi bi-cloud-upload me-2"></i>{{ __('Create & Save') }}
                        </button>
                        <Link :href="route('courses.index')" class="btn btn-outline-secondary w-100 mt-2 rounded-1 fw-bold">{{ __('Cancel') }}</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.tab-content { background-color: #fff; margin-top: -1px; }
.form-control:focus, .form-select:focus { border-color: #28a745; box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.1); }
.nav-pills .nav-link { color: #555; transition: all 0.2s; }
.nav-pills .nav-link:hover { background-color: #f1f1f1; }
.upload-vessel { border-style: dashed !important; border-width: 2px !important; border-color: #dee2e6 !important; }
.custom-radio .form-check-input:checked { background-color: #198754; border-color: #198754; }
.btn-lg { font-size: 1rem; padding: 0.75rem 1rem; }
</style>
