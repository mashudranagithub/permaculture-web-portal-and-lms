<script setup>
import { ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const avatarPreview = ref(user.avatar_url);

const form = useForm({
    _method: 'patch',
    name: user.name,
    email: user.email,
    avatar: null,
});

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            avatarPreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const updateProfile = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Form reset or success message handled by Inertia
        }
    });
};
</script>

<template>
    <section>
        <form @submit.prevent="updateProfile" class="row g-4">
            <!-- Avatar Upload Section -->
            <div class="col-md-4 text-center border-end pe-md-4">
                <div class="mb-3">
                    <label class="form-label text-uppercase small fw-bold text-muted d-block">{{ __('Profile Picture') }}</label>
                    <div class="position-relative d-inline-block">
                        <img 
                            :src="avatarPreview" 
                            class="rounded-circle shadow border border-3 border-white object-fit-cover" 
                            style="width: 150px; height: 150px; background-color: #f8f9fa;"
                        >
                        <div class="position-absolute bottom-0 end-0">
                            <label for="avatarUpload" class="btn btn-success btn-sm rounded-circle shadow-sm p-2">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input 
                                id="avatarUpload" 
                                type="file" 
                                class="d-none" 
                                @change="handleAvatarChange" 
                                accept="image/*"
                            >
                        </div>
                    </div>
                </div>
                <p class="small text-muted">{{ __('JPG, PNG or GIF. Max 2MB') }}</p>
                <div v-if="form.errors.avatar" class="text-danger small mt-1">{{ form.errors.avatar }}</div>
            </div>

            <!-- Basic Info Section -->
            <div class="col-md-8 ps-md-4">
                <div class="mb-4">
                    <p class="text-muted small">
                        {{ __("Update your account's profile information and email address.") }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Full Name') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            class="form-control rounded-1 border-start-0 ps-0" 
                            required 
                            autofocus
                        >
                    </div>
                    <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                        <input 
                            v-model="form.email" 
                            type="email" 
                            class="form-control rounded-1 border-start-0 ps-0" 
                            required
                        >
                    </div>
                    <div v-if="form.errors.email" class="text-danger small mt-1">{{ form.errors.email }}</div>
                </div>

                <div v-if="mustVerifyEmail && user.email_verified_at === null" class="alert alert-warning border-0 rounded-1 small py-2">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ __('Your email address is unverified.') }}
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="alert-link ms-1"
                    >
                        {{ __('Click here to re-send.') }}
                    </Link>
                    <div v-show="status === 'verification-link-sent'" class="mt-1 text-success fw-bold">
                        {{ __('A new verification link has been sent.') }}
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 mt-4">
                    <button type="submit" class="btn btn-success px-4 rounded-1 fw-bold shadow-sm" :disabled="form.processing">
                        <i class="bi bi-check2-circle me-2"></i>{{ __('Save Changes') }}
                    </button>

                    <Transition
                        enter-active-class="animate__animated animate__fadeIn"
                        leave-active-class="animate__animated animate__fadeOut"
                    >
                        <span v-if="form.recentlySuccessful" class="text-success small fw-bold">
                            <i class="bi bi-check-lg me-1"></i>{{ __('Saved.') }}
                        </span>
                    </Transition>
                </div>
            </div>
        </form>
    </section>
</template>

<style scoped>
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.1);
}
.input-group-text {
    color: #6c757d;
}
</style>
