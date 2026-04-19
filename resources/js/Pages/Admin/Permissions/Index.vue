<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    permissions: Object,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 15);
const sortField = ref(props.filters.sort_field || 'name');
const sortDirection = ref(props.filters.sort_direction || 'asc');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('admin.permissions.index'), {
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
    const data = props.permissions.data.map((p, index) => ({
        SL: (props.permissions.current_page - 1) * props.permissions.per_page + index + 1,
        Name: p.name,
        Slug: p.slug,
        Description: p.description || 'N/A'
    }));

    if (type === 'pdf') {
        window.location.href = route('admin.reports.permissions.pdf');
        return;
    }

    if (type === 'csv' || type === 'excel') {
        const headers = Object.keys(data[0]).join(',');
        const rows = data.map(obj => Object.values(obj).map(v => `"${v}"`).join(',')).join('\n');
        const blob = new Blob([headers + '\n' + rows], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `permissions_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>Permissions Report - PRINT</title>
                <style>
                    body { font-family: sans-serif; padding: 30px; color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 10px; text-align: left; font-size: 11px; }
                    th { background-color: #f0f0f0; }
                    h2 { color: #0d6efd; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
                </style>
            </head>
            <body>
                <h2>Platform Permissions Report</h2>
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
        alert(__('Data copied!'));
    }
};

const form = useForm({
    name: '',
    description: ''
});

const isEditing = ref(false);
const selectedPermissionId = ref(null);

const openModal = (permission = null) => {
    if (permission) {
        isEditing.value = true;
        selectedPermissionId.value = permission.id;
        form.name = permission.name;
        form.description = permission.description;
    } else {
        isEditing.value = false;
        selectedPermissionId.value = null;
        form.reset();
    }
    const modalElement = document.getElementById('permissionModal');
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
};

const submit = () => {
    if (isEditing.value) {
        form.patch(route('admin.permissions.update', selectedPermissionId.value), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.permissions.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const closeModal = () => {
    const modalElement = document.getElementById('permissionModal');
    const modal = window.bootstrap.Modal.getInstance(modalElement);
    if (modal) modal.hide();
};

const deletePermission = (permission) => {
    if (confirm(__('Are you sure you want to delete the :name permission? This will remove it from all roles and users.', { name: permission.name }))) {
        router.delete(route('admin.permissions.destroy', permission.id));
    }
};
</script>

<template>
    <Head :title="__('Manage Permissions')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Permission Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-1">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-key text-success"></i>{{ __('Action Permissions') }}
                            </h3>
                            <button @click="openModal()" class="btn btn-success btn-sm rounded-1 px-4 shadow-sm fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('Create Permission') }}
                            </button>
                        </div>

                        <div class="mt-4 row g-3 align-items-center text-secondary">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm border-secondary-subtle shadow-none w-auto rounded-1">
                                    <option value="5">5</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
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
                                        <th class="ps-3 text-center py-2" style="width: 50px;">{{ __('SL.') }}</th>
                                        <th @click="toggleSort('name')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'name' && sortDirection === 'asc', 'sorting_desc': sortField === 'name' && sortDirection === 'desc' }">
                                            {{ __('Permission Name') }}
                                        </th>
                                        <th class="py-2">{{ __('Permission Slug') }}</th>
                                        <th class="py-2">{{ __('Description') }}</th>
                                        <th class="text-center py-2" style="width: 120px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(permission, index) in permissions.data" :key="permission.id">
                                        <td class="text-center fw-bold text-muted small">
                                            {{ (permissions.current_page - 1) * permissions.per_page + index + 1 }}
                                        </td>
                                        <td class="">
                                            <div class="fw-bold text-dark">{{ __(permission.name) }}</div>
                                        </td>
                                        <td>
                                            <code class="text-secondary" style="font-size: 0.75rem;">{{ permission.slug }}</code>
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ permission.description ? __(permission.description) : __('No description provided') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button @click="openModal(permission)" class="btn btn-outline-primary btn-xs rounded-1 p-1 px-2" :title="__('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button @click="deletePermission(permission)" class="btn btn-outline-danger btn-xs rounded-1 p-1 px-2" :title="__('Delete')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="permissions.data.length === 0">
                                        <td colspan="5" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No permissions found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ permissions.from || 0 }} {{ __('to') }} {{ permissions.to || 0 }} {{ __('of') }} {{ permissions.total }} {{ __('permissions') }}
                        </div>
                        <nav v-if="permissions.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in permissions.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                    <Link class="page-link rounded-1 mx-1 border shadow-none" :href="link.url" v-html="link.label" preserve-scroll />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permission Modal -->
        <div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-1 overflow-hidden">
                    <form @submit.prevent="submit">
                        <div class="modal-header bg-success text-white border-bottom py-2 px-4">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-key-fill me-2"></i>{{ isEditing ? __('Edit Permission') : __('New Permission') }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted mb-1">{{ __('Permission Name') }}</label>
                                <input v-model="form.name" type="text" class="form-control rounded-1 border-primary-subtle shadow-sm px-3 py-2" placeholder="e.g. Create Course" required>
                                <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-0 text-start">
                                <label class="form-label fw-bold small text-uppercase text-muted mb-1">{{ __('Description') }}</label>
                                <textarea v-model="form.description" class="form-control rounded-1 border-primary-subtle shadow-sm px-3 py-2" rows="3" :placeholder="__('Briefly describe this permission...')"></textarea>
                                <div v-if="form.errors.description" class="text-danger small mt-1">{{ form.errors.description }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-2 px-4 text-end bg-light">
                            <button type="button" class="btn btn-light btn-sm rounded-1 px-4 me-2 fw-bold border" @click="closeModal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" :disabled="form.processing">
                                <i class="bi bi-save me-2"></i> {{ __('Save Permission') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.pagination .page-link { color: #0d6efd; font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.pagination .page-item.active .page-link { background-color: #0d6efd; color: white; border-color: #0d6efd; }

th.sorting { position: relative; padding-right: 20px !important; }
th.sorting::after { position: absolute; right: 8px; content: "↑"; opacity: 0.2; }
th.sorting::before { position: absolute; right: 14px; content: "↓"; opacity: 0.2; }
th.sorting_asc::after { opacity: 1; color: #0d6efd; }
th.sorting_desc::before { opacity: 1; color: #0d6efd; }

@media print {
    body * { visibility: hidden; }
    .table-responsive, .table-responsive * { visibility: visible; }
    .table-responsive { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
