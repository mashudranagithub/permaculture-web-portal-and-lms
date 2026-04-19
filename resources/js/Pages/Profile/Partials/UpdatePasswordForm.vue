<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <div class="row">
            <div class="col-md-10">
                <div class="mb-4">
                    <p class="text-muted small">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </p>
                </div>

                <form @submit.prevent="updatePassword" class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Current Password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key-fill"></i></span>
                            <input 
                                v-model="form.current_password" 
                                type="password" 
                                class="form-control rounded-1 border-start-0 ps-0" 
                                autocomplete="current-password"
                                ref="currentPasswordInput"
                            >
                        </div>
                        <div v-if="form.errors.current_password" class="text-danger small mt-1">{{ form.errors.current_password }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('New Password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock-fill"></i></span>
                            <input 
                                v-model="form.password" 
                                type="password" 
                                class="form-control rounded-1 border-start-0 ps-0" 
                                autocomplete="new-password"
                                ref="passwordInput"
                            >
                        </div>
                        <div v-if="form.errors.password" class="text-danger small mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Confirm Password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check"></i></span>
                            <input 
                                v-model="form.password_confirmation" 
                                type="password" 
                                class="form-control rounded-1 border-start-0 ps-0" 
                                autocomplete="new-password"
                            >
                        </div>
                        <div v-if="form.errors.password_confirmation" class="text-danger small mt-1">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <div class="col-12 mt-4 d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-success px-4 rounded-1 fw-bold shadow-sm" :disabled="form.processing">
                            <i class="bi bi-save me-2"></i>{{ __('Update Password') }}
                        </button>

                        <Transition
                            enter-active-class="animate__animated animate__fadeIn"
                            leave-active-class="animate__animated animate__fadeOut"
                        >
                            <span v-if="form.recentlySuccessful" class="text-success small fw-bold">
                                <i class="bi bi-check-lg me-1"></i>{{ __('Password updated successfully.') }}
                            </span>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
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
