<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <div class="row">
            <div class="col-md-10">
                <div class="mb-4">
                    <p class="text-muted small">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                    </p>
                </div>

                <button @click="confirmUserDeletion" class="btn btn-danger px-4 rounded-1 fw-bold shadow-sm">
                    <i class="bi bi-trash3 me-2"></i>{{ __('Delete Permanent Account') }}
                </button>

                <Modal :show="confirmingUserDeletion" @close="closeModal">
                    <div class="p-4">
                        <h5 class="fw-bold text-danger mb-3">
                            <i class="bi bi-exclamation-octagon me-2"></i>{{ __('Confirm Account Deletion') }}
                        </h5>

                        <p class="small text-muted mb-4">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mb-4">
                            <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Your Password') }}</label>
                            <input 
                                v-model="form.password" 
                                type="password" 
                                class="form-control rounded-1" 
                                :placeholder="__('Type your password to confirm')"
                                ref="passwordInput"
                                @keyup.enter="deleteUser"
                            >
                            <div v-if="form.errors.password" class="text-danger small mt-1">{{ form.errors.password }}</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button @click="closeModal" class="btn btn-light px-4 rounded-1 fw-bold">{{ __('Cancel') }}</button>
                            <button 
                                @click="deleteUser" 
                                class="btn btn-danger px-4 rounded-1 fw-bold shadow-sm"
                                :disabled="form.processing"
                            >
                                <i v-if="form.processing" class="spinner-border spinner-border-sm me-2"></i>
                                {{ __('Delete My Account') }}
                            </button>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </section>
</template>

<style scoped>
.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.1);
}
</style>
