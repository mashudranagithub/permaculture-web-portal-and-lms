<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    roles: Object,
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
    router.get(route('admin.roles.index'), {
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

const exportData = (type) => {
    const data = props.roles.data.map((r, index) => ({
        SL: (props.roles.current_page - 1) * props.roles.per_page + index + 1,
        Name: r.name,
        Slug: r.slug,
        Permissions: r.permissions.join(', ')
    }));

    if (type === 'pdf') {
        window.location.href = route('admin.reports.roles.pdf');
        return;
    }

    if (type === 'csv' || type === 'excel') {
        const headers = Object.keys(data[0]).join(',');
        const rows = data.map(obj => Object.values(obj).map(v => `"${v}"`).join(',')).join('\n');
        const blob = new Blob([headers + '\n' + rows], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `roles_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>Roles Report - PRINT</title>
                <style>
                    body { font-family: sans-serif; padding: 30px; color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 10px; text-align: left; font-size: 11px; }
                    th { background-color: #f0f0f0; }
                    h2 { color: #198754; border-bottom: 2px solid #198754; padding-bottom: 10px; }
                </style>
            </head>
            <body>
                <h2>Platform Roles Report</h2>
                <p><strong>Date:</strong> ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>${Object.keys(data[0]).map(h => `<th>${h}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `<tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
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
        alert('Data copied!');
    }
};

const form = useForm({
    name: '',
    permission_ids: []
});

const isEditing = ref(false);
const selectedRoleId = ref(null);

const openModal = (role = null) => {
    if (role) {
        isEditing.value = true;
        selectedRoleId.value = role.id;
        form.name = role.name;
        form.permission_ids = role.permission_ids || [];
    } else {
        isEditing.value = false;
        selectedRoleId.value = null;
        form.reset();
    }
    const modalElement = document.getElementById('roleModal');
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
};

const submit = () => {
    if (isEditing.value) {
        form.patch(route('admin.roles.update', selectedRoleId.value), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.roles.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const closeModal = () => {
    const modalElement = document.getElementById('roleModal');
    const modal = window.bootstrap.Modal.getInstance(modalElement);
    if (modal) modal.hide();
};

const deleteRole = (role) => {
    if (confirm(`Are you sure you want to delete the ${role.name} role?`)) {
        router.delete(route('admin.roles.destroy', role.id));
    }
};
</script>

<template>
    <Head :title="__('Manage Roles')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Role Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-1">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock text-success"></i>{{ __('Platform Roles') }}
                            </h3>
                            <button @click="openModal()" class="btn btn-success btn-sm rounded-1 px-4 shadow-sm fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('Create Role') }}
                            </button>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center text-secondary">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm border-secondary-subtle shadow-none w-auto rounded-1">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ __('All') }}</option>
                                </select>
                                <span class="small fw-bold">{{ __('entries') }}</span>
                            </div>

                            <div class="col-md-auto ms-md-auto d-flex align-items-center gap-2">
                                <!-- Export Buttons Pack -->
                                <div class="btn-group btn-group-sm shadow-sm rounded-1 overflow-visible border">
                                    <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center" title="Copy"><i class="bi bi-clipboard small me-2 text-secondary"></i>{{ __('Copy') }}</button>
                                    <button @click="exportData('csv')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="CSV"><i class="bi bi-file-earmark-spreadsheet small me-2 text-info"></i>CSV</button>
                                    <button @click="exportData('excel')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Excel"><i class="bi bi-file-earmark-excel small me-2 text-success"></i>Excel</button>
                                    <button @click="exportData('pdf')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="PDF"><i class="bi bi-file-earmark-pdf-fill small me-2 text-danger"></i>PDF</button>
                                    <button @click="exportData('print')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Print"><i class="bi bi-printer small me-2 text-dark"></i>{{ __('Print') }}</button>
                                </div>

                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 px-2"><i class="bi bi-search text-muted"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none py-2" :placeholder="__('Search...')" style="min-width: 180px; height: 31px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0 dataTable custom-small-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-center py-2" style="width: 50px;">SL.</th>
                                        <th @click="toggleSort('name')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'name' && sortDirection === 'asc', 'sorting_desc': sortField === 'name' && sortDirection === 'desc' }">
                                            {{ __('Role Name') }}
                                        </th>
                                        <th class="py-2">{{ __('Role Slug') }}</th>
                                        <th class="py-2">{{ __('Permissions') }}</th>
                                        <th class="text-center py-2" style="width: 120px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(role, index) in roles.data" :key="role.id">
                                        <td class="text-center fw-bold text-muted small">
                                            {{ (roles.current_page - 1) * roles.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ role.name }}</div>
                                        </td>
                                        <td>
                                            <code class="text-secondary" style="font-size: 0.75rem;">{{ role.slug }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span v-if="role.slug === 'super-admin'" class="badge bg-dark px-2">
                                                    {{ __('All Permissions') }}
                                                </span>
                                                <span v-else v-for="perm in role.permissions" :key="perm" class="badge bg-light text-dark border px-2 shadow-xs" style="font-size: 0.7rem;">
                                                    {{ perm }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button @click="openModal(role)" class="btn btn-outline-primary btn-xs rounded-1 p-1 px-2" :title="__('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button v-if="role.slug !== 'super-admin'" @click="deleteRole(role)" class="btn btn-outline-danger btn-xs rounded-1 p-1 px-2" :title="__('Delete')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="roles.data.length === 0">
                                        <td colspan="5" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No roles found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ roles.from || 0 }} {{ __('to') }} {{ roles.to || 0 }} {{ __('of') }} {{ roles.total }} {{ __('roles') }}
                        </div>
                        <nav v-if="roles.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in roles.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                    <Link class="page-link rounded-1 mx-1 border shadow-none" :href="link.url" v-html="link.label" preserve-scroll />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Modal -->
        <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-1">
                    <form @submit.prevent="submit">
                        <div class="modal-header bg-success text-white border-bottom py-2 px-4">
                            <h6 class="modal-title fw-bold">
                                {{ isEditing ? __('Edit Role') : __('Create New Role') }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small text-uppercase mb-1">{{ __('Role Name') }}</label>
                                <input v-model="form.name" type="text" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" placeholder="e.g. Course Manager" required>
                                <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div v-if="form.name.toLowerCase() !== 'super admin'" class="mb-0">
                                <label class="form-label fw-bold text-dark d-block mb-3 small text-uppercase">{{ __('Assign Permissions') }}</label>
                                <div class="row g-2">
                                    <div v-for="perm in allPermissions" :key="perm.id" class="col-md-4">
                                        <div class="form-check form-switch p-2 bg-light rounded-1 border shadow-xs transition-all">
                                            <input class="form-check-input ms-0 me-2 cursor-pointer" type="checkbox" :id="'perm' + perm.id" v-model="form.permission_ids" :value="perm.id">
                                            <label class="form-check-label small fw-semibold text-dark ps-4 cursor-pointer" :for="'perm' + perm.id">
                                                {{ perm.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-2 px-4 text-end bg-light">
                            <button type="button" class="btn btn-light btn-sm rounded-1 px-4 me-2 fw-bold border" @click="closeModal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" :disabled="form.processing">
                                <i class="bi bi-save me-2"></i> {{ __('Save Role') }}
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
.badge { border-radius: 2px !important; }

.cursor-pointer { cursor: pointer; }
.form-check-input:checked { background-color: var(--bs-success); border-color: var(--bs-success); }
.pagination .page-link { color: #198754; font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.pagination .page-item.active .page-link { background-color: #198754; color: white; border-color: #198754; }

th.sorting { position: relative; padding-right: 20px !important; }
th.sorting::after { position: absolute; right: 8px; content: "↑"; opacity: 0.2; }
th.sorting::before { position: absolute; right: 14px; content: "↓"; opacity: 0.2; }
th.sorting_asc::after { opacity: 1; color: #198754; }
th.sorting_desc::before { opacity: 1; color: #198754; }

@media print {
    body * { visibility: hidden; }
    .table-responsive, .table-responsive * { visibility: visible; }
    .table-responsive { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
