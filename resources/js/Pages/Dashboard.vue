<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head :title="__('Dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center gap-2">
                <div v-if="$page.props.auth.user.organization" class="bg-white p-1 rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <img :src="$page.props.auth.user.organization.logo_url" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                </div>
                <div>
                    <span class="fw-bold">{{ __('Admin Dashboard') }}</span>
                    <div v-if="$page.props.auth.user.organization" class="small text-muted fw-normal" style="font-size: 0.75rem;">
                        <i class="bi bi-buildings me-1"></i>{{ $page.props.auth.user.organization.name }}
                    </div>
                </div>
            </div>
        </template>

        <div class="row g-4">
            <!-- Small Box for Students -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-primary p-4 rounded-3 h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1 fs-3">1,250</h3>
                            <p class="mb-0 small opacity-75 fw-semibold">{{ __('Total Students') }}</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-people-fill fs-3 text-white"></i>
                        </div>
                    </div>
                    <a href="#" class="text-white text-decoration-none mt-3 small d-flex align-items-center gap-1 opacity-75 hover-opacity-100 pt-2 border-top border-white border-opacity-10">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Small Box for Courses -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-success p-4 rounded-3 h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1 fs-3">24</h3>
                            <p class="mb-0 small opacity-75 fw-semibold">{{ __('Active Courses') }}</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-journal-check fs-3 text-white"></i>
                        </div>
                    </div>
                    <a href="#" class="text-white text-decoration-none mt-3 small d-flex align-items-center gap-1 opacity-75 hover-opacity-100 pt-2 border-top border-white border-opacity-10">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Small Box for Revenue -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm text-dark bg-warning p-4 rounded-3 h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1 fs-3 text-nowrap">৳ 45,200</h3>
                            <p class="mb-0 small opacity-75 fw-semibold text-muted">{{ __('Monthly Revenue') }}</p>
                        </div>
                        <div class="bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-cash-stack fs-3 text-dark"></i>
                        </div>
                    </div>
                    <a href="#" class="text-dark text-decoration-none mt-3 small d-flex align-items-center gap-1 opacity-75 hover-opacity-100 pt-2 border-top border-dark border-opacity-10">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Small Box for Projects -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm text-white bg-danger p-4 rounded-3 h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1 fs-3">85%</h3>
                            <p class="mb-0 small opacity-75 fw-semibold">{{ __('Project Growth') }}</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-graph-up-arrow fs-3 text-white"></i>
                        </div>
                    </div>
                    <a href="#" class="text-white text-decoration-none mt-3 small d-flex align-items-center gap-1 opacity-75 hover-opacity-100 pt-2 border-top border-white border-opacity-10">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h3 class="card-title fw-bold text-success mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill"></i> {{ __('Recent Enrollments') }}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Student') }}</th>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="ps-4">
                                        <td class="ps-4">
                                            <div class="fw-bold">Tanvir Ahmed</div>
                                            <div class="small text-muted">Dhaka, BD</div>
                                        </td>
                                        <td>Urban Regenerative Systems</td>
                                        <td><span class="badge bg-success rounded-pill px-3">{{ __('Paid') }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">Selina Akter</div>
                                            <div class="small text-muted">Sylhet, BD</div>
                                        </td>
                                        <td>Ethics of Nature</td>
                                        <td><span class="badge bg-warning text-dark rounded-pill px-3">{{ __('Pending') }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h3 class="card-title fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill me-2"></i>{{ __('Quick Actions') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <Link :href="route('courses.create')" class="btn btn-outline-success text-start d-flex align-items-center justify-content-between py-2">
                                <span><i class="bi bi-plus-circle me-2"></i>{{ __('Add New Course') }}</span>
                                <i class="bi bi-chevron-right small"></i>
                            </Link>
                            <button class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between py-2 disabled opacity-50">
                                <span><i class="bi bi-envelope me-2"></i>{{ __('Send Announcement') }}</span>
                                <i class="bi bi-chevron-right small"></i>
                            </button>
                            <button class="btn btn-outline-dark text-start d-flex align-items-center justify-content-between py-2 disabled opacity-50">
                                <span><i class="bi bi-file-earmark-pdf me-2"></i>{{ __('Export Reports') }}</span>
                                <i class="bi bi-chevron-right small"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
