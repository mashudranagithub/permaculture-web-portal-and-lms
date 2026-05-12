<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    student: Object,
    certificates: Array
});
</script>

<template>
    <Head :title="__('Student Profile') + ' - ' + student.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center gap-3">
                <Link :href="route('admin.students.index')" class="btn btn-outline-success btn-sm rounded-circle p-1" style="width: 32px; height: 32px;">
                    <i class="bi bi-arrow-left"></i>
                </Link>
                {{ __('Student Profile') }}
            </div>
        </template>

        <div class="row g-4">
            <!-- Profile Overview -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4 text-center">
                        <img :src="student.avatar_url" class="rounded-circle shadow mb-3 border border-3 border-success-subtle object-fit-cover" style="width: 120px; height: 120px;" alt="Avatar">
                        <h4 class="fw-bold mb-1 text-dark">{{ student.name }}</h4>
                        <p class="text-muted small mb-3">{{ student.email }}</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                            <span v-for="role in student.roles" :key="role.id" class="badge bg-success text-white px-3 py-2 rounded-pill small">
                                {{ role.name }}
                            </span>
                        </div>
                        <hr class="opacity-10">
                        <div class="text-start mt-4">
                            <div class="mb-3">
                                <label class="text-muted x-small text-uppercase fw-bold d-block">{{ __('Joined At') }}</label>
                                <span class="fw-bold">{{ new Date(student.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted x-small text-uppercase fw-bold d-block">{{ __('Verification Status') }}</label>
                                <span v-if="student.email_verified_at" class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ __('Verified') }}
                                </span>
                                <span v-else class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i>{{ __('Unverified') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificates Summary -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-patch-check-fill text-success me-2"></i>{{ __('Earned Certificates') }}</h6>
                    </div>
                    <div class="card-body p-4">
                        <div v-if="certificates.length > 0" class="d-flex flex-column gap-3">
                            <div v-for="cert in certificates" :key="cert.id" class="p-3 bg-light rounded-3 border-start border-4 border-success">
                                <div class="fw-bold small text-dark mb-1">{{ cert.course?.title?.bn || cert.course?.title?.en }}</div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="x-small text-muted">{{ cert.certificate_no }}</span>
                                    <a :href="route('certificates.download', cert.id)" target="_blank" class="btn btn-link btn-xs p-0 text-success fw-bold text-decoration-none">
                                        <i class="bi bi-download me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-muted small italic">
                            {{ __('No certificates earned yet.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Tabs -->
            <div class="col-lg-8">
                <!-- Enrollments Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-journal-bookmark-fill text-success"></i>{{ __('Active Enrollments') }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Course / Batch') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Enrolled Date') }}</th>
                                        <th class="text-center">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="enr in student.enrollments" :key="enr.id">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ enr.batch?.course?.title?.bn || enr.batch?.course?.title?.en }}</div>
                                            <div class="x-small text-success fw-bold">{{ enr.batch?.title?.bn || enr.batch?.title?.en }}</div>
                                        </td>
                                        <td>{{ enr.price_at_enrollment }} BDT</td>
                                        <td class="small">{{ new Date(enr.enrolled_at).toLocaleDateString() }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-2" :class="enr.status === 'active' ? 'bg-success' : 'bg-secondary'">{{ enr.status }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="student.enrollments.length === 0">
                                        <td colspan="4" class="text-center py-5 text-muted">{{ __('No enrollments found.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment History Section -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-cash-stack text-success"></i>{{ __('Payment History') }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Transaction ID') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Method') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th class="text-center">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pay in student.payments" :key="pay.id">
                                        <td class="ps-4">
                                            <code class="text-muted x-small">{{ pay.transaction_id }}</code>
                                        </td>
                                        <td class="fw-bold text-dark">{{ pay.amount }} BDT</td>
                                        <td class="small text-uppercase">{{ pay.method }}</td>
                                        <td class="small">{{ new Date(pay.created_at).toLocaleDateString() }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-2" :class="pay.status === 'success' ? 'bg-success' : (pay.status === 'pending' ? 'bg-warning text-dark' : 'bg-danger')">
                                                {{ pay.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="student.payments.length === 0">
                                        <td colspan="5" class="text-center py-5 text-muted">{{ __('No payments found.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.x-small { font-size: 0.7rem; }
.object-fit-cover { object-fit: cover; }
.bg-light-soft { background-color: #f8f9fa; }
.btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
.badge { border-radius: 4px; }
</style>
