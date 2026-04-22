<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    courses: Array
});

const activeTab = ref('bn');

const form = useForm({
    course_id: '',
    title: { en: '', bn: '' },
    description: { en: '', bn: '' },
    start_date: null,
    end_date: null,
    enrollment_deadline: null,
    capacity: 0,
    price: 0,
    discount_amount: 0,
    discount_type: 'fixed',
    status: 'upcoming',
    is_enrollment_open: true,
    schedules: [], // New Schedule/Routine
});

// Auto-fill price when course changes
watch(() => form.course_id, (newId) => {
    const course = props.courses.find(c => c.id == newId);
    if (course) {
        form.price = course.price;
        // Auto-generate a title snippet if empty
        if (!form.title.en) {
            form.title.en = `Batch ${new Date().getFullYear()}-${new Date().getMonth() + 1}`;
        }
        if (!form.title.bn) {
            form.title.bn = `ব্যাচ ${new Date().getFullYear()}-${new Date().getMonth() + 1}`;
        }
    }
});

const addSchedule = () => {
    form.schedules.push({
        day_of_week: 'sat',
        start_time: '20:00',
        end_time: '22:00',
        platform: 'Google Meet',
        is_recurring: true
    });
};

const removeSchedule = (index) => {
    form.schedules.splice(index, 1);
};

const submit = () => {
    form.post(route('batches.store'));
};
</script>

<template>
    <Head :title="__('Create Batch')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Batch Management') }}
        </template>

        <div class="row">
            <div class="col-xl-10 mx-auto">
                <form @submit.prevent="submit">
                    <div class="card border-0 shadow-sm rounded-1 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-plus-circle-fill text-success me-2"></i>{{ __('New Course Batch') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Course Selection -->
                                <div class="col-md-12">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Select Course') }}</label>
                                    <select v-model="form.course_id" class="form-select rounded-1" required>
                                        <option value="">{{ __('Choose a course...') }}</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.course_id" class="text-danger small mt-1">{{ form.errors.course_id }}</div>
                                </div>

                                <div class="col-12"><hr class="my-0 opacity-5"></div>

                                <!-- Bilingual Content Tabs -->
                                <div class="col-12">
                                    <label class="form-label text-uppercase small fw-bold text-muted mb-3">{{ __('Batch Information (Bilingual)') }}</label>
                                    <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-1" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link rounded-1 py-2" :class="{ 'active bg-success text-white': activeTab === 'bn' }" @click="activeTab = 'bn'" type="button">
                                                {{ __('Bangla') }}
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link rounded-1 py-2" :class="{ 'active bg-success text-white': activeTab === 'en' }" @click="activeTab = 'en'" type="button">
                                                {{ __('English') }}
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content border p-4 rounded-1 bg-white">
                                        <!-- Bangla Section -->
                                        <div v-show="activeTab === 'bn'" class="animate__animated animate__fadeIn">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">{{ __('Batch Title (Bangla)') }}</label>
                                                <input v-model="form.title.bn" type="text" class="form-control rounded-1" placeholder="উদাঃ জানুয়ারি ২০২৬ ব্যাচ" required>
                                                <div v-if="form.errors['title.bn']" class="text-danger small mt-1">{{ form.errors['title.bn'] }}</div>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold">{{ __('Batch Description (Bangla)') }}</label>
                                                <RichTextEditor v-model="form.description.bn" :placeholder="__('ব্যাচের বিস্তারিত তথ্য বাংলায় লিখুন...')" />
                                                <div v-if="form.errors['description.bn']" class="text-danger small mt-1">{{ form.errors['description.bn'] }}</div>
                                            </div>
                                        </div>

                                        <!-- English Section -->
                                        <div v-show="activeTab === 'en'" class="animate__animated animate__fadeIn">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">{{ __('Batch Title (English)') }}</label>
                                                <input v-model="form.title.en" type="text" class="form-control rounded-1" placeholder="e.g. January 2026 Batch" required>
                                                <div v-if="form.errors['title.en']" class="text-danger small mt-1">{{ form.errors['title.en'] }}</div>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold">{{ __('Batch Description (English)') }}</label>
                                                <RichTextEditor v-model="form.description.en" :placeholder="__('Enter batch details in English...')" />
                                                <div v-if="form.errors['description.en']" class="text-danger small mt-1">{{ form.errors['description.en'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12"><hr class="my-0 opacity-5"></div>

                                <!-- Dates -->
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Start Date') }}</label>
                                    <DateInput v-model="form.start_date" :error="form.errors.start_date" required />
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('End Date') }}</label>
                                    <DateInput v-model="form.end_date" :error="form.errors.end_date" required />
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Enrollment Deadline') }}</label>
                                    <DateInput v-model="form.enrollment_deadline" :error="form.errors.enrollment_deadline" />
                                </div>

                                <div class="col-12"><hr class="my-0 opacity-5"></div>

                                <!-- Class Schedule / Routine Section -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label text-uppercase small fw-bold text-muted mb-0">{{ __('Class Schedule / Routine') }}</label>
                                        <button @click="addSchedule" type="button" class="btn btn-xs btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-plus-lg me-1"></i>{{ __('Add Day') }}
                                        </button>
                                    </div>
                                    
                                    <div v-if="form.schedules.length === 0" class="alert alert-light border border-dashed py-4 text-center text-muted">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                        {{ __('No class schedule defined yet.') }}
                                    </div>

                                    <div v-for="(sch, index) in form.schedules" :key="index" class="row g-2 mb-3 align-items-end animate__animated animate__fadeInUp">
                                        <div class="col-md-3">
                                            <label class="small text-muted fw-bold">{{ __('Day') }}</label>
                                            <select v-model="sch.day_of_week" class="form-select form-select-sm rounded-1">
                                                <option value="sat">{{ __('Saturday') }}</option>
                                                <option value="sun">{{ __('Sunday') }}</option>
                                                <option value="mon">{{ __('Monday') }}</option>
                                                <option value="tue">{{ __('Tuesday') }}</option>
                                                <option value="wed">{{ __('Wednesday') }}</option>
                                                <option value="thu">{{ __('Thursday') }}</option>
                                                <option value="fri">{{ __('Friday') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted fw-bold">{{ __('Start Time') }}</label>
                                            <DateInput v-model="sch.start_time" type="time" placeholder="00:00" />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted fw-bold">{{ __('End Time') }}</label>
                                            <DateInput v-model="sch.end_time" type="time" placeholder="00:00" />
                                        </div>
                                        <div class="col-md-2">
                                            <label class="small text-muted fw-bold">{{ __('Platform') }}</label>
                                            <input v-model="sch.platform" type="text" class="form-control form-control-sm rounded-1" placeholder="e.g. Zoom">
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button @click="removeSchedule(index)" type="button" class="btn btn-link text-danger p-0 mb-1" title="Remove">
                                                <i class="bi bi-trash fs-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.schedules" class="text-danger small mt-1">{{ form.errors.schedules }}</div>
                                </div>

                                <div class="col-12"><hr class="my-0 opacity-5"></div>

                                <!-- Seat capacity -->
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Total Seats') }}</label>
                                    <input v-model="form.capacity" type="number" class="form-control rounded-1" min="0">
                                    <div class="small text-muted mt-1">{{ __('Set 0 for unlimited') }}</div>
                                </div>

                                <!-- Pricing -->
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Batch Price (BDT)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">৳</span>
                                        <input v-model="form.price" type="number" class="form-control rounded-1" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Status') }}</label>
                                    <select v-model="form.status" class="form-select rounded-1">
                                        <option value="upcoming">{{ __('Upcoming') }}</option>
                                        <option value="ongoing">{{ __('Ongoing') }}</option>
                                        <option value="completed">{{ __('Completed') }}</option>
                                        <option value="cancelled">{{ __('Cancelled') }}</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-check form-switch bg-light p-3 rounded-1 border d-flex align-items-center justify-content-between px-4">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ __('Open Enrollment') }}</h6>
                                            <p class="small text-muted mb-0">{{ __('Allow students to register for this batch immediately') }}</p>
                                        </div>
                                        <input v-model="form.is_enrollment_open" class="form-check-input ms-0" type="checkbox" style="width: 3rem; height: 1.5rem;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top p-4 d-flex gap-3">
                            <button @click="submit" class="btn btn-success btn-lg px-5 rounded-1 fw-bold shadow-sm" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                <i class="bi bi-cloud-upload me-2"></i>{{ __('Create Batch') }}
                            </button>
                            <Link :href="route('batches.index')" class="btn btn-outline-secondary btn-lg px-4 rounded-1 fw-bold">{{ __('Cancel') }}</Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-control:focus, .form-select:focus { border-color: #28a745; box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.1); }
.btn-lg { font-size: 1rem; padding: 0.75rem 1.5rem; }
hr { border-top: 1px solid #000; }
.nav-pills .nav-link { color: #555; transition: all 0.2s; border: 1px solid transparent; }
.nav-pills .nav-link.active { border-color: #198754; }
.btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
.border-dashed { border-style: dashed !important; }
</style>
