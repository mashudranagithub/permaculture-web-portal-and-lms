<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    organization: Object,
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'active': return 'bg-success';
        case 'pending': return 'bg-warning text-dark';
        case 'suspended': return 'bg-danger';
        case 'rejected': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
};

const toggleStatus = async () => {
    const isSuspended = props.organization.status === 'suspended';
    const action = isSuspended ? 'reactivate' : 'suspend';
    const title = isSuspended ? 'Reactivate Organization?' : 'Suspend Organization?';
    const text = isSuspended 
        ? 'Users of this organization will regain access immediately.' 
        : 'All users belonging to this organization will be blocked from accessing the platform.';

    const result = await Swal.fire({
        title,
        text,
        icon: isSuspended ? 'info' : 'warning',
        showCancelButton: true,
        confirmButtonColor: isSuspended ? '#198754' : '#d33',
        confirmButtonText: `Yes, ${action}`
    });
    
    if (result.isConfirmed) {
        router.post(route(`admin.organizations.${action}`, props.organization.id));
    }
};

const approveOrg = () => {
    Swal.fire({
        title: `Approve "${props.organization.name}"?`,
        text: 'The organization will be activated and their admin will be notified by email.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        confirmButtonText: 'Yes, Approve',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.organizations.approve', props.organization.id));
        }
    });
};

const rejectOrg = async () => {
    const { value: reason } = await Swal.fire({
        title: 'Reject Organization',
        input: 'textarea',
        inputLabel: 'Reason for rejection',
        inputPlaceholder: 'e.g. Incomplete information...',
        inputAttributes: {
            'aria-label': 'Reason for rejection'
        },
        showCancelButton: true,
        confirmButtonColor: '#d33',
        inputValidator: (value) => {
            if (!value) return 'You need to provide a reason!';
        }
    });

    if (reason) {
        router.post(route('admin.organizations.reject', props.organization.id), { reason });
    }
};
</script>

<template>
    <Head :title="organization.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-3">
                    <Link :href="route('admin.organizations.index')" class="btn btn-light btn-sm rounded-circle p-2 shadow-sm">
                        <i class="bx bx-left-arrow-alt fs-4"></i>
                    </Link>
                    <h5 class="mb-0 fw-bold">{{ organization.name }}</h5>
                </div>
                <div class="d-flex gap-2">
                    <!-- Actions for Pending -->
                    <template v-if="organization.status === 'pending'">
                        <button @click="approveOrg" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bx bx-check me-1"></i> Approve
                        </button>
                        <button @click="rejectOrg" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                            <i class="bx bx-x me-1"></i> Reject
                        </button>
                    </template>

                    <!-- Actions for Active/Suspended -->
                    <button v-else-if="organization.status === 'active' || organization.status === 'suspended'" 
                        @click="toggleStatus"
                        class="btn rounded-pill px-4 fw-bold shadow-sm"
                        :class="organization.status === 'suspended' ? 'btn-success' : 'btn-outline-danger'">
                        <i class="bx me-1" :class="organization.status === 'suspended' ? 'bx-play' : 'bx-pause'"></i>
                        {{ organization.status === 'suspended' ? 'Reactivate' : 'Suspend' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="container py-4">
            <div class="row g-4">
                <!-- Main Info -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex align-items-start gap-4 mb-4">
                            <img :src="organization.logo_url" class="rounded-4 shadow-sm object-fit-cover" style="width:120px;height:120px;" :alt="organization.name">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h3 class="fw-bold mb-0 text-success">{{ organization.name }}</h3>
                                    <span class="badge rounded-pill text-uppercase px-3 py-2" :class="getStatusBadge(organization.status)">
                                        {{ organization.status }}
                                    </span>
                                </div>
                                <p class="text-muted mb-3">{{ organization.description || 'No description provided.' }}</p>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 text-muted small">
                                            <i class="bx bx-envelope text-success"></i> {{ organization.email }}
                                        </div>
                                    </div>
                                    <div v-if="organization.phone" class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 text-muted small">
                                            <i class="bx bx-phone text-success"></i> {{ organization.phone }}
                                        </div>
                                    </div>
                                    <div v-if="organization.website" class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 text-muted small">
                                            <i class="bx bx-globe text-success"></i> 
                                            <a :href="organization.website" target="_blank" class="text-success text-decoration-none">{{ organization.website }}</a>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 text-muted small">
                                            <i class="bx bx-calendar text-success"></i> Registered {{ new Date(organization.created_at).toLocaleDateString() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Admin Details</h6>
                        <div v-if="organization.users && organization.users.length" class="row g-3">
                            <div v-for="user in organization.users" :key="user.id" class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3">
                                    <img :src="user.avatar_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`" class="rounded-circle" style="width:40px;height:40px;" alt="">
                                    <div>
                                        <div class="fw-bold small">{{ user.name }}</div>
                                        <div class="text-muted x-small">{{ user.email }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-muted small">No admin users found for this organization.</div>
                    </div>

                    <!-- Statistics / Quick Look -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                                <div class="text-success fs-2 mb-1"><i class="bx bx-book-open"></i></div>
                                <h4 class="fw-bold mb-0">{{ organization.courses_count || 0 }}</h4>
                                <div class="text-muted small">Courses</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                                <div class="text-primary fs-2 mb-1"><i class="bx bx-group"></i></div>
                                <h4 class="fw-bold mb-0">{{ organization.users_count || 0 }}</h4>
                                <div class="text-muted small">Members</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                                <div class="text-warning fs-2 mb-1"><i class="bx bx-layer"></i></div>
                                <h4 class="fw-bold mb-0">{{ organization.batches_count || 0 }}</h4>
                                <div class="text-muted small">Batches</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Actions / Logs -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h6 class="fw-bold mb-3">Settings</h6>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="allowEnrollment" checked disabled>
                            <label class="form-check-label small" for="allowEnrollment">Allow New Enrollments</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="showInDirectory" checked disabled>
                            <label class="form-check-label small" for="showInDirectory">Show in Directory</label>
                        </div>
                        <button class="btn btn-light w-100 rounded-pill btn-sm text-muted mt-2" disabled>
                            <i class="bx bx-cog me-1"></i> Edit Organization Info
                        </button>
                    </div>

                    <div v-if="organization.rejection_reason" class="card border-0 shadow-sm rounded-4 p-4 border-start border-4 border-danger">
                        <h6 class="fw-bold text-danger mb-2"><i class="bx bx-error-circle me-1"></i> Rejection Reason</h6>
                        <p class="small text-muted mb-0 italic">"{{ organization.rejection_reason }}"</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.x-small { font-size: 0.72rem; }
.italic { font-style: italic; }
</style>
