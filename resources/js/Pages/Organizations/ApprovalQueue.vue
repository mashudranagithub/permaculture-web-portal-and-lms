<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    pendingOrganizations: Array,
});

// Reject modal state
const rejectForm = useForm({ reason: '' });
const rejectingOrg = ref(null);

const openRejectModal = (org) => {
    rejectingOrg.value = org;
    rejectForm.reason = '';
};

const submitReject = () => {
    rejectForm.post(route('admin.organizations.reject', rejectingOrg.value.id), {
        onSuccess: () => {
            rejectingOrg.value = null;
            rejectForm.reset();
        },
    });
};

const approveOrg = (org) => {
    Swal.fire({
        title: `Approve "${org.name}"?`,
        text: 'The organization will be activated and their admin will be notified by email.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.organizations.approve', org.id));
        }
    });
};
</script>

<template>
    <Head title="Pending Approvals" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center gap-3">
                <i class="bx bx-buildings fs-4 text-warning"></i>
                Pending Organization Approvals
                <span v-if="pendingOrganizations.length" class="badge bg-warning text-dark rounded-pill">
                    {{ pendingOrganizations.length }}
                </span>
            </div>
        </template>

        <div class="container py-4">

            <!-- Empty State -->
            <div v-if="pendingOrganizations.length === 0" class="text-center py-5">
                <div class="bg-success-subtle rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bx bx-check-circle text-success fs-1"></i>
                </div>
                <h5 class="fw-bold">All Clear!</h5>
                <p class="text-muted">No pending organization registrations at this time.</p>
            </div>

            <!-- Pending List -->
            <div v-else class="row g-4">
                <div v-for="org in pendingOrganizations" :key="org.id" class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-4">
                                <!-- Logo -->
                                <img :src="org.logo_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(org.name)}&background=198754&color=fff&size=80`"
                                    class="rounded-3 object-fit-cover flex-shrink-0" style="width:64px;height:64px;" :alt="org.name">

                                <!-- Info -->
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h5 class="fw-bold mb-0">{{ org.name }}</h5>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">Pending</span>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="bx bx-envelope me-1"></i>{{ org.email }}
                                        <span v-if="org.phone" class="ms-3"><i class="bx bx-phone me-1"></i>{{ org.phone }}</span>
                                        <span v-if="org.website" class="ms-3">
                                            <i class="bx bx-globe me-1"></i>
                                            <a :href="org.website" target="_blank" class="text-decoration-none text-muted">{{ org.website }}</a>
                                        </span>
                                    </div>
                                    <p v-if="org.description" class="text-muted small mb-2">{{ org.description }}</p>
                                    <div class="text-muted x-small">
                                        <i class="bx bx-user me-1"></i>{{ org.users_count }} admin user(s) registered
                                        &bull;
                                        <i class="bx bx-time me-1"></i>Submitted {{ new Date(org.created_at).toLocaleDateString() }}
                                    </div>

                                    <!-- Admin Users -->
                                    <div v-if="org.users && org.users.length" class="mt-2">
                                        <span class="x-small text-muted text-uppercase fw-bold me-2">Admin:</span>
                                        <span v-for="u in org.users" :key="u.id" class="badge bg-light text-dark border me-1 x-small fw-normal">
                                            {{ u.name }} &lt;{{ u.email }}&gt;
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex gap-2 flex-shrink-0">
                                    <button @click="approveOrg(org)"
                                        class="btn btn-success rounded-3 px-4 fw-bold shadow-sm">
                                        <i class="bx bx-check me-1"></i>Approve
                                    </button>
                                    <button @click="openRejectModal(org)"
                                        class="btn btn-outline-danger rounded-3 px-4 fw-bold">
                                        <i class="bx bx-x me-1"></i>Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="rejectingOrg" class="modal-backdrop-custom" @click.self="rejectingOrg = null">
            <div class="modal-card shadow-lg rounded-4 p-4">
                <h5 class="fw-bold mb-1">Reject "{{ rejectingOrg.name }}"</h5>
                <p class="text-muted small mb-3">Please provide a reason. This will be sent to the organization by email.</p>
                <textarea v-model="rejectForm.reason" class="form-control rounded-3 mb-3" rows="4"
                    placeholder="e.g. Incomplete information, invalid contact details..." required></textarea>
                <div v-if="rejectForm.errors.reason" class="text-danger small mb-2">{{ rejectForm.errors.reason }}</div>
                <div class="d-flex gap-2 justify-content-end">
                    <button @click="rejectingOrg = null" class="btn btn-light rounded-3 px-4">Cancel</button>
                    <button @click="submitReject" class="btn btn-danger rounded-3 px-4 fw-bold"
                        :disabled="rejectForm.processing || !rejectForm.reason.trim()">
                        <span v-if="rejectForm.processing" class="spinner-border spinner-border-sm me-1"></span>
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.x-small { font-size: 0.72rem; }
.modal-backdrop-custom {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
}
.modal-card {
    background: #fff;
    width: 100%;
    max-width: 520px;
}
</style>
