<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    organization: Object,
    isSuperAdmin: Boolean,
});

const logoPreview = ref(props.organization.logo_url || null);
const logoInput = ref(null);

const form = useForm({
    name: props.organization.name || '',
    email: props.organization.email || '',
    phone: props.organization.phone || '',
    website: props.organization.website || '',
    address: props.organization.address || '',
    description: props.organization.description || '',
    logo: null,
});

const triggerLogoUpload = () => {
    logoInput.value.click();
};

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            logoPreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    const url = props.isSuperAdmin
        ? route('admin.organizations.update', props.organization.id)
        : route('admin.settings.organization.update');

    // Since we are uploading files, we submit as POST
    // We can also spoof PUT if Laravel expects it, but since we defined POST routes in web.php, a direct POST is perfect.
    form.post(url, {
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Organization profile updated successfully.',
                icon: 'success',
                confirmButtonColor: '#198754',
            });
        },
        onError: () => {
            Swal.fire({
                title: 'Error!',
                text: 'Please check the form for validation errors.',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
        }
    });
};
</script>

<template>
    <Head :title="__('Edit Organization Profile')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-3">
                    <Link 
                        :href="isSuperAdmin ? route('admin.organizations.show', organization.id) : route('dashboard')" 
                        class="btn btn-light btn-sm rounded-circle p-2 shadow-sm"
                    >
                        <i class="bx bx-left-arrow-alt fs-4"></i>
                    </Link>
                    <h5 class="mb-0 fw-bold">{{ __('Edit Organization Profile') }}</h5>
                </div>
            </div>
        </template>

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-success text-white py-4 px-4 border-0">
                            <h4 class="mb-1 fw-bold">{{ __('Organization Information') }}</h4>
                            <p class="mb-0 opacity-75 small">{{ __('Update the profile details, logo, and contact info of the organization.') }}</p>
                        </div>
                        
                        <form @submit.prevent="submit" class="card-body p-4 bg-white">
                            <div class="row g-4">
                                <!-- Logo Upload -->
                                <div class="col-12 text-center pb-3 border-bottom border-light-subtle">
                                    <div class="position-relative d-inline-block">
                                        <img 
                                            :src="logoPreview || 'https://ui-avatars.com/api/?name=Org&size=128'" 
                                            class="rounded-4 shadow-sm border border-2 border-white object-fit-cover bg-light" 
                                            style="width: 130px; height: 130px;"
                                            alt="Logo Preview"
                                        >
                                        <button 
                                            type="button" 
                                            @click="triggerLogoUpload" 
                                            class="btn btn-success btn-sm rounded-circle position-absolute shadow-sm p-2" 
                                            style="bottom: -10px; right: -10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                                        >
                                            <i class="bx bx-camera fs-5"></i>
                                        </button>
                                    </div>
                                    <input 
                                        type="file" 
                                        ref="logoInput" 
                                        class="d-none" 
                                        accept="image/*" 
                                        @change="handleLogoChange"
                                    >
                                    <div class="mt-3 text-muted small">{{ __('Click the camera icon to upload a logo (PNG, JPG up to 2MB)') }}</div>
                                    <div v-if="form.errors.logo" class="text-danger small mt-1">{{ form.errors.logo }}</div>
                                </div>

                                <!-- Org Name -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">{{ __('Organization Name') }}</label>
                                    <input 
                                        type="text" 
                                        v-model="form.name" 
                                        class="form-control rounded-3 border-light-subtle shadow-sm py-2 px-3" 
                                        placeholder="e.g. Green Valley Academy"
                                        required
                                    >
                                    <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                                </div>

                                <!-- Org Email -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">{{ __('Email Address') }}</label>
                                    <input 
                                        type="email" 
                                        v-model="form.email" 
                                        class="form-control rounded-3 border-light-subtle shadow-sm py-2 px-3" 
                                        placeholder="info@academy.org"
                                        required
                                    >
                                    <div v-if="form.errors.email" class="text-danger small mt-1">{{ form.errors.email }}</div>
                                </div>

                                <!-- Org Phone -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">{{ __('Phone Number') }}</label>
                                    <input 
                                        type="text" 
                                        v-model="form.phone" 
                                        class="form-control rounded-3 border-light-subtle shadow-sm py-2 px-3" 
                                        placeholder="+1 234 567 890"
                                    >
                                    <div v-if="form.errors.phone" class="text-danger small mt-1">{{ form.errors.phone }}</div>
                                </div>

                                <!-- Org Website -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">{{ __('Website URL') }}</label>
                                    <input 
                                        type="url" 
                                        v-model="form.website" 
                                        class="form-control rounded-3 border-light-subtle shadow-sm py-2 px-3" 
                                        placeholder="https://academy.org"
                                    >
                                    <div v-if="form.errors.website" class="text-danger small mt-1">{{ form.errors.website }}</div>
                                </div>

                                <!-- Org Address -->
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark">{{ __('Address') }}</label>
                                    <textarea 
                                        v-model="form.address" 
                                        rows="2" 
                                        class="form-control rounded-3 border-light-subtle shadow-sm py-2 px-3" 
                                        placeholder="123 Ecological Street, Nature City"
                                    ></textarea>
                                    <div v-if="form.errors.address" class="text-danger small mt-1">{{ form.errors.address }}</div>
                                </div>

                                <!-- Org Description -->
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark">{{ __('Description') }}</label>
                                    <textarea 
                                        v-model="form.description" 
                                        rows="4" 
                                        class="form-control rounded-3 border-light-subtle shadow-sm py-2 px-3" 
                                        placeholder="Describe the organization mission, values, and courses..."
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-danger small mt-1">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top border-light-subtle d-flex justify-content-between align-items-center">
                                <Link 
                                    :href="isSuperAdmin ? route('admin.organizations.show', organization.id) : route('dashboard')" 
                                    class="btn btn-outline-secondary rounded-pill px-4"
                                >
                                    {{ __('Cancel') }}
                                </Link>
                                <button 
                                    type="submit" 
                                    class="btn btn-success rounded-pill px-5 fw-bold shadow-sm"
                                    :disabled="form.processing"
                                >
                                    <i v-if="form.processing" class="spinner-border spinner-border-sm me-2"></i>
                                    <i v-else class="bx bx-check-circle me-1"></i>
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
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
}
</style>
