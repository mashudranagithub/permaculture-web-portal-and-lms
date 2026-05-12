<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    certificates: Object,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'issue_date');
const sortDirection = ref(props.filters.sort_direction || 'desc');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('admin.certificates.index'), {
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
    const data = props.certificates.data.map((c, index) => ({
        SL: (props.certificates.current_page - 1) * props.certificates.per_page + index + 1,
        'Cert No': c.certificate_no,
        Student: c.user?.name,
        Course: c.course?.title?.bn || c.course?.title?.en,
        Issued: new Date(c.issue_date).toLocaleDateString()
    }));

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
        a.download = `certificates_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print' || type === 'pdf') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>Certificate Report - PRINT</title>
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
                <h2>Certificate Issuance Report</h2>
                <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>${Object.keys(data[0]).map(h => `<th>${h}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `<tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
                <div class="footer">Generated via Permaculture LMS System</div>
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
    <Head :title="__('Manage Certificates')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Issued Certificates') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check-fill text-success"></i>{{ __('Organization Certificates') }}
                            </h3>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm w-auto shadow-none border-success-subtle">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="all">{{ __('All') }}</option>
                                </select>
                            </div>
                            <div class="col-md-auto ms-md-auto d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm shadow-sm rounded-1 overflow-visible border text-muted">
                                    <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center" title="Copy"><i class="bi bi-clipboard me-2 small text-secondary"></i>{{ __('Copy') }}</button>
                                    <button @click="exportData('csv')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="CSV"><i class="bi bi-file-earmark-spreadsheet me-2 small text-info"></i>CSV</button>
                                    <button @click="exportData('excel')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Excel"><i class="bi bi-file-earmark-excel me-2 small text-success"></i>Excel</button>
                                    <button @click="exportData('pdf')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="PDF"><i class="bi bi-file-earmark-pdf-fill me-2 small text-danger"></i>PDF</button>
                                    <button @click="exportData('print')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Print"><i class="bi bi-printer me-2 small text-dark"></i>{{ __('Print') }}</button>
                                </div>
                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search...')" style="min-width: 200px;">
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
                                        <th class="py-2">{{ __('Certificate No') }}</th>
                                        <th class="py-2">{{ __('Student') }}</th>
                                        <th class="py-2">{{ __('Course') }}</th>
                                        <th @click="toggleSort('issue_date')" class="py-2 cursor-pointer">
                                            {{ __('Issued Date') }}
                                            <i v-if="sortField === 'issue_date'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th class="text-center py-2">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cert, index) in certificates.data" :key="cert.id">
                                        <td class="text-center fw-bold text-muted">
                                            {{ (certificates.current_page - 1) * certificates.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ cert.certificate_no }}</span>
                                        </td>
                                        <td>{{ cert.user?.name }}</td>
                                        <td>{{ cert.course?.title?.bn || cert.course?.title?.en }}</td>
                                        <td class="text-muted small">
                                            {{ new Date(cert.issue_date).toLocaleDateString() }}
                                        </td>
                                        <td class="text-center">
                                            <a :href="route('certificates.download', cert.id)" target="_blank" class="btn btn-outline-success btn-xs px-2 rounded-1">
                                                <i class="bi bi-download me-1"></i> {{ __('PDF') }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr v-if="certificates.data.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No certificates found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-2">
                        <!-- Pagination same as others -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-pointer { cursor: pointer; }
.custom-small-table { font-size: 0.85rem; }
.btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
</style>
