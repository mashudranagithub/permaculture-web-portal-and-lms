<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    organizations: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const doSearch = () => {
    router.get(route('admin.organizations.index'), { search: search.value }, { preserveState: true });
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'active': return 'bg-success';
        case 'pending': return 'bg-warning text-dark';
        case 'suspended': return 'bg-danger';
        case 'rejected': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
};

const deleteOrg = (org) => {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${org.name}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.organizations.destroy', org.id));
        }
    });
};
</script>

<template>
    <Head title="Organizations" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-3">
                    <i class="bx bx-buildings fs-4 text-success"></i>
                    {{ __('Organization Management') }}
                </div>
                <Link :href="route('admin.organizations.queue')" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                    <i class="bx bx-time-five"></i> Approval Queue
                </Link>
            </div>
        </template>

        <div class="container py-4">
            <!-- Search & Filter -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input v-model="search" @keyup.enter="doSearch" type="text" 
                                    class="form-control border-start-0 ps-0" 
                                    placeholder="Search by name, email or slug...">
                                <button @click="doSearch" class="btn btn-success px-4">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Organization</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Members</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="org in organizations.data" :key="org.id">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img :src="org.logo_url" class="rounded-3 object-fit-cover" style="width:40px;height:40px;" :alt="org.name">
                                        <div>
                                            <div class="fw-bold text-dark">{{ org.name }}</div>
                                            <div class="text-muted x-small">{{ org.slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small"><i class="bx bx-envelope me-1"></i>{{ org.email }}</div>
                                    <div v-if="org.phone" class="x-small text-muted"><i class="bx bx-phone me-1"></i>{{ org.phone }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill text-uppercase px-2" :class="getStatusBadge(org.status)">
                                        {{ org.status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal">
                                        <i class="bx bx-user me-1"></i>{{ org.users_count }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <Link :href="route('admin.organizations.show', org.id)" 
                                            class="btn btn-light btn-sm rounded-circle p-2" title="View Details">
                                            <i class="bx bx-show fs-5"></i>
                                        </Link>
                                        <button @click="deleteOrg(org)" 
                                            class="btn btn-light btn-sm rounded-circle p-2 text-danger" title="Delete">
                                            <i class="bx bx-trash fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="organizations.data.length === 0">
                                <td colspan="5" class="text-center py-5 text-muted">No organizations found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="organizations.links.length > 3" class="card-footer bg-white border-top-0 py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li v-for="(link, k) in organizations.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                <Link class="page-link shadow-none" :href="link.url || '#'" v-html="link.label" />
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.x-small { font-size: 0.75rem; }
.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.025em;
    color: #6c757d;
}
</style>
