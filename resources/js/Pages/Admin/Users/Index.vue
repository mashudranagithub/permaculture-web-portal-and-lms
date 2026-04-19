<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    allRoles: Array,
    allPermissions: Array,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('admin.users.index'), {
        search: search.value,
        per_page: perPage.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true
    });
};

watch([search, perPage], debounce(updateTable, 300));

const toggleSort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    updateTable();
};

const exportData = async (type) => {
    let exportSet = props.users.data;
    const data = exportSet.map((u, index) => ({
        SL: (props.users.current_page - 1) * props.users.per_page + index + 1,
        Name: u.name,
        Email: u.email,
        Roles: u.roles.join(', '),
        Joined: u.joined_date || 'N/A'
    }));

    if (type === 'pdf') {
        window.location.href = route('admin.reports.users.pdf');
        return;
    }

    if (type === 'csv' || type === 'excel') {
        const headers = Object.keys(data[0]).join(',');
        const rows = data.map(obj => Object.values(obj).map(v => `"${v}"`).join(',')).join('\n');
        const blob = new Blob([headers + '\n' + rows], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `users_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>User Report - PRINT</title>
                <style>
                    body { font-family: sans-serif; padding: 30px; color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 10px; text-align: left; font-size: 11px; }
                    th { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; }
                    h2 { color: #198754; margin-top: 0; border-bottom: 2px solid #198754; padding-bottom: 10px; }
                    .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: right; }
                </style>
            </head>
            <body>
                <h2>User Management Report</h2>
                <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>${Object.keys(data[0]).map(h => `<th>${h}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `<tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
                <div class="footer">Generated via Permaculture ERP Management System</div>
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                        }, 500);
                    }
                <\/script>
            </body>
            </html>
        `;
        printWindow.document.write(tableHtml);
        printWindow.document.close();
    } else if (type === 'copy') {
        const text = data.map(obj => Object.values(obj).join('\t')).join('\n');
        navigator.clipboard.writeText(text);
        alert(__('Data copied to clipboard!'));
    }
};

const selectedUser = ref(null);
const editForm = useForm({
    role_ids: [],
    permission_ids: []
});

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    role_ids: [],
    permission_ids: []
});

const openCreateModal = () => {
    createForm.reset();
    const modalElement = document.getElementById('createUserModal');
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
};

const createUser = () => {
    createForm.post(route('admin.users.store'), {
        onSuccess: () => {
            const modalElement = document.getElementById('createUserModal');
            const modal = window.bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
        }
    });
};

const openEditModal = (user) => {
    selectedUser.value = user;
    editForm.role_ids = [...(user.role_ids || [])];
    editForm.permission_ids = [...(user.permission_ids || [])];
    
    const modalElement = document.getElementById('editModal');
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
};

const updateRoles = () => {
    editForm.patch(route('admin.users.update', selectedUser.value.id), {
        onSuccess: () => {
            const modalElement = document.getElementById('editModal');
            const modal = window.bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
        }
    });
};

const approveUser = (user) => {
    if (confirm(__('Are you sure you want to approve this user?'))) {
        router.patch(route('admin.users.approve', user.id));
    }
};

const generatePassword = () => {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
    let pass = "";
    for (let i = 0; i < 12; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    createForm.password = pass;
};
</script>

<template>
    <Head :title="__('Manage Users')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('User Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-people text-success"></i>{{ __('System Users') }}
                            </h3>
                            <button @click="openCreateModal" class="btn btn-success btn-sm rounded-1 px-4 shadow-sm fw-bold">
                                <i class="bi bi-person-plus-fill me-2"></i>{{ __('Create User') }}
                            </button>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm border-secondary-subtle shadow-none w-auto rounded-1">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ __('All') }}</option>
                                </select>
                                <span class="small fw-bold text-muted">{{ __('entries') }}</span>
                            </div>
                            <div class="col-md-auto ms-md-auto d-flex align-items-center gap-2">
                                <!-- Export Buttons Pack -->
                                <div class="btn-group btn-group-sm shadow-sm rounded-1 overflow-visible border">
                                    <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center" title="Copy"><i class="bi bi-clipboard me-2 small text-secondary"></i>{{ __('Copy') }}</button>
                                    <button @click="exportData('csv')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="CSV"><i class="bi bi-file-earmark-spreadsheet me-2 small text-info"></i>CSV</button>
                                    <button @click="exportData('excel')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Excel"><i class="bi bi-file-earmark-excel me-2 small text-success"></i>Excel</button>
                                    <button @click="exportData('pdf')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="PDF"><i class="bi bi-file-earmark-pdf-fill me-2 small text-danger"></i>PDF</button>
                                    <button @click="exportData('print')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Print"><i class="bi bi-printer me-2 small text-dark"></i>{{ __('Print') }}</button>
                                </div>

                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search...')" style="min-width: 180px; height: 31px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0 dataTable custom-small-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-center py-2" style="width: 50px;">{{ __('SL.') }}</th>
                                        <th @click="toggleSort('name')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'name' && sortDirection === 'asc', 'sorting_desc': sortField === 'name' && sortDirection === 'desc' }">
                                            {{ __('User Name') }}
                                        </th>
                                        <th @click="toggleSort('email')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'email' && sortDirection === 'asc', 'sorting_desc': sortField === 'email' && sortDirection === 'desc' }">
                                            {{ __('Email Address') }}
                                        </th>
                                        <th class="py-2">{{ __('Roles') }}</th>
                                        <th @click="toggleSort('created_at')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'created_at' && sortDirection === 'asc', 'sorting_desc': sortField === 'created_at' && sortDirection === 'desc' }">
                                            {{ __('Joined At') }}
                                        </th>
                                        <th class="text-center py-2" style="width: 120px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in users.data" :key="user.id">
                                        <td class="text-center fw-bold text-muted">
                                            {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-success-subtle text-success rounded-0 d-flex align-items-center justify-content-center fw-bold border shadow-xs" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                    {{ user.name.charAt(0) }}
                                                </div>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                                    {{ user.name }}
                                                    <span v-if="user.is_approved" class="badge bg-success-subtle text-success border border-success-subtle px-1" style="font-size: 0.6rem;">{{ __('Approved') }}</span>
                                                    <span v-else class="badge bg-warning-subtle text-warning border border-warning-subtle px-1" style="font-size: 0.6rem;">{{ __('Pending') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted">
                                            {{ user.email }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span v-for="role in user.roles" :key="role" class="badge bg-success text-white px-2">
                                                    {{ __(role) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-muted fw-semibold">
                                            {{ user.joined_date || 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button v-if="!user.is_approved" @click="approveUser(user)" class="btn btn-outline-success btn-xs rounded-1 p-1 px-2" :title="__('Approve')">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button @click="openEditModal(user)" class="btn btn-outline-primary btn-xs rounded-1 p-1 px-2" :title="__('Edit Access')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-xs rounded-1 p-1 px-2" :title="__('Delete')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No users found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ users.from || 0 }} {{ __('to') }} {{ users.to || 0 }} {{ __('of') }} {{ users.total }} {{ __('users') }}
                        </div>
                        <nav v-if="users.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in users.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                    <Link class="page-link rounded-1 mx-1 shadow-none border" :href="link.url" v-html="link.label" preserve-scroll />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-1 overflow-hidden">
                    <form @submit.prevent="createUser">
                        <div class="modal-header bg-success text-white border-bottom py-2 px-4">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-person-plus-fill me-2"></i>{{ isEditing ? __('Edit User') : __('Create New User') }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Full Name') }}</label>
                                    <input v-model="createForm.name" type="text" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" placeholder="John Doe" required>
                                    <div v-if="createForm.errors.name" class="text-danger small mt-1">{{ createForm.errors.name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Email Address') }}</label>
                                    <input v-model="createForm.email" type="email" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" placeholder="john@example.com" required>
                                    <div v-if="createForm.errors.email" class="text-danger small mt-1">{{ createForm.errors.email }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Initial Password') }}</label>
                                    <div class="input-group">
                                        <input v-model="createForm.password" type="text" class="form-control rounded-start-1 border-success-subtle shadow-sm px-3 py-2" placeholder="At least 8 characters" required>
                                        <button @click.prevent="generatePassword" class="btn btn-outline-success border-success-subtle rounded-end-1" type="button"><i class="bi bi-magic me-2"></i>{{ __('Generate') }}</button>
                                    </div>
                                    <div v-if="createForm.errors.password" class="text-danger small mt-1">{{ createForm.errors.password }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-2 px-4 bg-light">
                            <button type="button" class="btn btn-light btn-sm rounded-1 px-4 fw-bold border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" :disabled="createForm.processing">
                                <i class="bi bi-save me-2"></i> {{ isEditing ? __('Update User') : __('Create User') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manage Access Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-1">
                    <form @submit.prevent="updateRoles">
                        <div class="modal-header bg-success text-white border-bottom py-2 px-4">
                            <h6 class="modal-title fw-bold">{{ __('Manage User Access') }}</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Roles Section -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2 small"><i class="bi bi-shield-shaded me-2 text-success"></i>{{ __('Assign Roles') }}</label>
                                <div class="row g-2">
                                    <div v-for="role in allRoles" :key="role.id" class="col-md-6">
                                        <div class="form-check p-2 bg-light rounded-1 border px-3">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" :id="'role' + role.id" v-model="editForm.role_ids" :value="role.id">
                                            <label class="form-check-label ps-4 fw-semibold small cursor-pointer" :for="'role' + role.id">{{ role.name }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Permissions Section -->
                            <div class="mb-0 border-top pt-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold text-dark mb-0 small"><i class="bi bi-key-fill me-2 text-primary"></i>{{ __('Direct Permissions') }}</label>
                                    <span class="badge bg-primary-subtle text-primary fw-normal small px-2 py-1">{{ __('Overrides Role Defaults') }}</span>
                                </div>
                                <div class="row g-2">
                                    <div v-for="perm in allPermissions" :key="perm.id" class="col-md-4">
                                        <div class="form-check p-2 border-dashed rounded-1 px-3 border-secondary">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" :id="'user_perm' + perm.id" v-model="editForm.permission_ids" :value="perm.id">
                                            <label class="form-check-label ps-4 small cursor-pointer" :for="'user_perm' + perm.id">{{ perm.name }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-2 px-4 bg-light">
                            <button type="button" class="btn btn-light btn-sm rounded-1 px-4 border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" :disabled="editForm.processing">
                                <i class="bi bi-check2-circle me-2"></i> {{ __('Apply Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }

.pagination .page-link { color: #198754; border: 1px solid #dee2e6; font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.pagination .page-item.active .page-link { background-color: #198754; color: white; border-color: #198754; }
.cursor-pointer { cursor: pointer; }

th.sorting { position: relative; padding-right: 20px !important; }
th.sorting::after { position: absolute; right: 8px; content: "↑"; opacity: 0.2; }
th.sorting::before { position: absolute; right: 14px; content: "↓"; opacity: 0.2; }
th.sorting_asc::after { opacity: 1; color: #198754; }
th.sorting_desc::before { opacity: 1; color: #198754; }

.badge { border-radius: 2px !important; }

@media print {
    body * { visibility: hidden; }
    .table-responsive, .table-responsive * { visibility: visible; }
    .table-responsive { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
