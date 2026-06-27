<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    teachers: Object,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');

// Create form
const createForm = useForm({
    name: '',
    email: '',
    password: '',
});

// Edit form
const editForm = useForm({
    id: null,
    name: '',
    email: '',
    password: '',
    is_approved: true,
});

const openCreateModal = () => {
    createForm.reset();
    const modal = new window.bootstrap.Modal(document.getElementById('createTeacherModal'));
    modal.show();
};

const createTeacher = () => {
    createForm.post(route('admin.teachers.store'), {
        onSuccess: () => {
            const modal = window.bootstrap.Modal.getInstance(document.getElementById('createTeacherModal'));
            modal.hide();
        }
    });
};

const openEditModal = (user) => {
    editForm.reset();
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.is_approved = user.is_approved === 1 || user.is_approved === true || user.is_approved === '1';
    editForm.password = ''; // Keep password empty unless changing it
    
    const modal = new window.bootstrap.Modal(document.getElementById('editTeacherModal'));
    modal.show();
};

const updateTeacher = () => {
    editForm.patch(route('admin.teachers.update', editForm.id), {
        onSuccess: () => {
            const modal = window.bootstrap.Modal.getInstance(document.getElementById('editTeacherModal'));
            modal.hide();
        }
    });
};

const generatePassword = (formType) => {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
    let pass = "";
    for (let i = 0; i < 12; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    if (formType === 'create') {
        createForm.password = pass;
    } else {
        editForm.password = pass;
    }
};

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('admin.teachers.index'), {
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
    const data = props.teachers.data.map((u, index) => {
        const row = {
            SL: (props.teachers.current_page - 1) * props.teachers.per_page + index + 1,
            Name: u.name,
            Email: u.email,
            Status: u.is_approved ? 'Active' : 'Suspended',
            Joined: new Date(u.created_at).toLocaleDateString()
        };
        if (u.organization) {
            row.Organization = u.organization.name;
        }
        return row;
    });

    if (type === 'copy') {
        const text = data.map(obj => Object.values(obj).join('\t')).join('\n');
        navigator.clipboard.writeText(text);
        alert(__('Data copied to clipboard!'));
    } else if (type === 'csv' || type === 'excel') {
        const headers = Object.keys(data[0]).join(',');
        const rows = data.map(obj => Object.values(obj).map(v => `"${v}"`).join(',')).join('\n');
        const blob = new Blob([headers + '\n' + rows], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `teachers_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print' || type === 'pdf') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>Teacher Report - PRINT</title>
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
                <h2>Teacher Management Report</h2>
                <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>${Object.keys(data[0]).map(h => `<th>${h}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `<tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
                <div class="footer">Generated via Regenerative Systems LMS</div>
                <script>
                    window.onload = function() {
                        setTimeout(function() { window.print(); }, 500);
                    }
                <\/script>
            </body>
            </html>
        `;
        printWindow.document.write(tableHtml);
        printWindow.document.close();
    }
};
</script>

<template>
    <Head :title="__('Manage Teachers')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Teacher Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge-fill text-success"></i>{{ __('Organization Teachers') }}
                            </h3>
                            <button @click="openCreateModal" class="btn btn-success btn-sm rounded-1 px-4 shadow-sm fw-bold">
                                <i class="bi bi-person-plus-fill me-2"></i>{{ __('Register Teacher') }}
                            </button>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm w-auto shadow-none border-success-subtle">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ __('All') }}</option>
                                </select>
                                <span class="small fw-bold text-muted">{{ __('entries') }}</span>
                            </div>
                            <div class="col-md-auto ms-md-auto d-flex align-items-center gap-2">
                                <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center" title="Copy"><i class="bi bi-clipboard me-2 small text-secondary"></i>{{ __('Copy') }}</button>
                                <button @click="exportData('csv')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="CSV"><i class="bi bi-file-earmark-spreadsheet me-2 small text-info"></i>CSV</button>
                                <button @click="exportData('excel')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Excel"><i class="bi bi-file-earmark-excel me-2 small text-success"></i>Excel</button>
                                <button @click="exportData('pdf')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="PDF"><i class="bi bi-file-earmark-pdf-fill me-2 small text-danger"></i>PDF</button>
                                <button @click="exportData('print')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Print"><i class="bi bi-printer me-2 small text-dark"></i>{{ __('Print') }}</button>

                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search...')" style="min-width: 180px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0 custom-small-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-center py-2" style="width: 50px;">{{ __('SL.') }}</th>
                                        <th @click="toggleSort('name')" class="py-2 cursor-pointer">
                                            {{ __('Teacher Name') }}
                                            <i v-if="sortField === 'name'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th @click="toggleSort('email')" class="py-2 cursor-pointer">
                                            {{ __('Email Address') }}
                                            <i v-if="sortField === 'email'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th v-if="$page.props.auth.user.roles.includes('super-admin')" class="py-2">
                                            {{ __('Organization') }}
                                        </th>
                                        <th @click="toggleSort('created_at')" class="py-2 cursor-pointer">
                                            {{ __('Registered At') }}
                                            <i v-if="sortField === 'created_at'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th class="text-center py-2">{{ __('Status') }}</th>
                                        <th class="text-center py-2" style="width: 120px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in teachers.data" :key="user.id">
                                        <td class="text-center fw-bold text-muted">
                                            {{ (teachers.current_page - 1) * teachers.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <Link :href="route('admin.teachers.show', user.id)" class="d-flex align-items-center gap-2 text-decoration-none transition-all hover-translate">
                                                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold border shadow-xs" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                    {{ user.name.charAt(0) }}
                                                </div>
                                                <div class="fw-bold text-dark">{{ user.name }}</div>
                                            </Link>
                                        </td>
                                        <td class="text-muted small">{{ user.email }}</td>
                                        <td v-if="$page.props.auth.user.roles.includes('super-admin')" class="small fw-semibold text-success">
                                            {{ user.organization?.name || __('System Teacher') }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ new Date(user.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="text-center">
                                            <span v-if="!user.is_approved" class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">{{ __('Suspended') }}</span>
                                            <span v-else-if="!user.email_verified_at" class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">{{ __('Pending Verification') }}</span>
                                            <span v-else class="badge bg-success-subtle text-success border border-success-subtle px-2">{{ __('Active') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <Link :href="route('admin.teachers.show', user.id)" class="btn btn-outline-success btn-xs" title="View Profile">
                                                    <i class="bi bi-eye"></i>
                                                </Link>
                                                <button @click="openEditModal(user)" class="btn btn-outline-primary btn-xs" title="Edit Teacher">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="teachers.data.length === 0">
                                        <td :colspan="$page.props.auth.user.roles.includes('super-admin') ? 7 : 6" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No teachers found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ teachers.from || 0 }} {{ __('to') }} {{ teachers.to || 0 }} {{ __('of') }} {{ teachers.total }} {{ __('teachers') }}
                        </div>
                        <nav v-if="teachers.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in teachers.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                    <Link class="page-link rounded-1 mx-1 shadow-none border" :href="link.url" v-html="link.label" preserve-scroll />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Teacher Modal -->
        <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-1 overflow-hidden">
                    <form @submit.prevent="createTeacher">
                        <div class="modal-header bg-success text-white border-bottom py-2 px-4">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-person-plus-fill me-2"></i>{{ __('Register New Teacher') }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Full Name') }}</label>
                                <input v-model="createForm.name" type="text" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" placeholder="Teacher Name" required>
                                <div v-if="createForm.errors.name" class="text-danger small mt-1">{{ createForm.errors.name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Email Address') }}</label>
                                <input v-model="createForm.email" type="email" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" placeholder="teacher@example.com" required>
                                <div v-if="createForm.errors.email" class="text-danger small mt-1">{{ createForm.errors.email }}</div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Password') }}</label>
                                <div class="input-group">
                                    <input v-model="createForm.password" type="text" class="form-control rounded-start-1 border-success-subtle shadow-sm px-3 py-2" placeholder="At least 8 characters" required>
                                    <button @click.prevent="generatePassword('create')" class="btn btn-outline-success border-success-subtle rounded-end-1" type="button"><i class="bi bi-magic me-2"></i>{{ __('Generate') }}</button>
                                </div>
                                <div v-if="createForm.errors.password" class="text-danger small mt-1">{{ createForm.errors.password }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-2 px-4 bg-light">
                            <button type="button" class="btn btn-light btn-sm rounded-1 px-4 fw-bold border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" :disabled="createForm.processing">
                                <i class="bi bi-save me-2"></i> {{ __('Register Teacher') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Teacher Modal -->
        <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-1 overflow-hidden">
                    <form @submit.prevent="updateTeacher">
                        <div class="modal-header bg-success text-white border-bottom py-2 px-4">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-pencil-square me-2"></i>{{ __('Edit Teacher Credentials') }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Full Name') }}</label>
                                <input v-model="editForm.name" type="text" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" required>
                                <div v-if="editForm.errors.name" class="text-danger small mt-1">{{ editForm.errors.name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Email Address') }}</label>
                                <input v-model="editForm.email" type="email" class="form-control rounded-1 border-success-subtle shadow-sm px-3 py-2" required>
                                <div v-if="editForm.errors.email" class="text-danger small mt-1">{{ editForm.errors.email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Password (Leave empty to keep current)') }}</label>
                                <div class="input-group">
                                    <input v-model="editForm.password" type="text" class="form-control rounded-start-1 border-success-subtle shadow-sm px-3 py-2" placeholder="Enter new password">
                                    <button @click.prevent="generatePassword('edit')" class="btn btn-outline-success border-success-subtle rounded-end-1" type="button"><i class="bi bi-magic me-2"></i>{{ __('Generate') }}</button>
                                </div>
                                <div v-if="editForm.errors.password" class="text-danger small mt-1">{{ editForm.errors.password }}</div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold small text-uppercase text-muted d-block">{{ __('Approval status') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input v-model="editForm.is_approved" class="form-check-input shadow-none cursor-pointer" type="checkbox" role="switch" id="teacherApprovedSwitch">
                                    <label class="form-check-label fw-semibold cursor-pointer ms-2" for="teacherApprovedSwitch">
                                        {{ editForm.is_approved ? __('Active / Approved') : __('Suspended') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-2 px-4 bg-light">
                            <button type="button" class="btn btn-light btn-sm rounded-1 px-4 fw-bold border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" :disabled="editForm.processing">
                                <i class="bi bi-save me-2"></i> {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-pointer { cursor: pointer; }
.custom-small-table { font-size: 0.85rem; }
.badge { border-radius: 4px; }
.x-small { font-size: 0.7rem; }
.hover-translate { transition: transform 0.2s ease; }
.hover-translate:hover { transform: translateX(5px); }
.btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
</style>
