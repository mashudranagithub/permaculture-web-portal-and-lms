<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    batch: Object,
    courses: Array
});

const activeTab = ref('bn');

const form = useForm({
    course_id: props.batch.course_id,
    title: {
        en: props.batch.title?.en || (typeof props.batch.title === 'string' ? props.batch.title : ''),
        bn: props.batch.title?.bn || (typeof props.batch.title === 'string' ? props.batch.title : '')
    },
    description: {
        en: props.batch.description?.en || '',
        bn: props.batch.description?.bn || ''
    },
    start_date: props.batch.start_date ? props.batch.start_date.substring(0, 10) : null,
    end_date: props.batch.end_date ? props.batch.end_date.substring(0, 10) : null,
    enrollment_deadline: props.batch.enrollment_deadline ? props.batch.enrollment_deadline.substring(0, 10) : null,
    capacity: props.batch.capacity,
    price: props.batch.price,
    discount_amount: props.batch.discount_amount,
    discount_type: props.batch.discount_type || 'fixed',
    status: props.batch.status,
    is_enrollment_open: Boolean(props.batch.is_enrollment_open),
    schedules: props.batch.schedules || [],
});

// Enrollment Deadline Validation
watch([() => form.enrollment_deadline, () => form.start_date], ([newDeadline, newStart]) => {
    if (newDeadline && newStart) {
        const deadline = new Date(newDeadline);
        const start = new Date(newStart);
        // Reset time to midnight for pure date comparison
        deadline.setHours(0, 0, 0, 0);
        start.setHours(0, 0, 0, 0);
        
        if (deadline > start) {
            form.errors.enrollment_deadline = 'Enrollment deadline must be before the start date.';
        } else {
            // Only clear if it was our custom error
            if (form.errors.enrollment_deadline === 'Enrollment deadline must be before the start date.') {
                form.errors.enrollment_deadline = null;
            }
        }
    }
});

const addSchedule = () => {
    form.schedules.push({
        day_of_week: '',
        start_time: '20:00',
        end_time: '22:00',
        platform: '',
        meeting_link: '',
        meeting_id: '',
        is_recurring: true
    });
};

const removeSchedule = (index) => {
    form.schedules.splice(index, 1);
};

const clearSchedule = (index) => {
    form.schedules[index] = {
        day_of_week: '',
        start_time: '',
        end_time: '',
        platform: '',
        meeting_link: '',
        meeting_id: '',
        is_recurring: true
    };
};

const submit = () => {
    // Final check before submission
    if (form.enrollment_deadline && form.start_date) {
        const deadline = new Date(form.enrollment_deadline);
        const start = new Date(form.start_date);
        deadline.setHours(0, 0, 0, 0);
        start.setHours(0, 0, 0, 0);

        if (deadline > start) {
            form.errors.enrollment_deadline = 'Enrollment deadline must be before the start date.';
            return;
        }
    }
    form.put(route('batches.update', props.batch.id));
};
</script>

<template>
    <Head :title="__('Edit Batch')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center">
                <i class="bx bx-layer me-2 text-success"></i>
                {{ __('Batch Management') }}
            </div>
        </template>

        <div class="row pb-5 mt-4">
            <div class="col-12">
                <form @submit.prevent="submit">
                    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success-subtle text-success p-2 rounded-2 me-3">
                                        <i class="bx bx-pencil fs-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-dark">{{ __('Edit Course Batch') }}</h5>
                                        <p class="mb-0 text-muted small">{{ __('Batch ID') }}: #{{ batch.id }} | {{ __('Manage details and schedule.') }}</p>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold small border border-success-subtle">{{ batch.status.toUpperCase() }}</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-4 p-lg-5">
                            <!-- Section 1: Basic Information -->
                            <fieldset class="premium-fieldset mb-5 border rounded-3 p-4 animate__animated animate__fadeIn">
                                <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                    <i class="bx bx-info-circle me-1"></i> {{ __('BASIC INFORMATION') }}
                                </legend>
                                
                                <div class="row g-4 mt-1">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Target Course') }}</label>
                                        <Select2 v-model="form.course_id" :placeholder="__('Choose a Course')" required disabled>
                                            <option v-for="course in courses" :key="course.id" :value="course.id">
                                                {{ course.title }}
                                            </option>
                                        </Select2>
                                        <div v-if="form.errors.course_id" class="text-danger small mt-1">{{ form.errors.course_id }}</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">{{ __('Batch Content (Bilingual)') }}</label>
                                        <div class="bilingual-tabs-wrapper rounded-2 border p-1 bg-light mb-3 d-flex">
                                            <button @click="activeTab = 'bn'" type="button" 
                                                class="btn flex-fill border-0 py-2 fw-bold small transition-all" 
                                                :class="activeTab === 'bn' ? 'bg-success text-white shadow-sm' : 'text-muted'">
                                                {{ __('Bangla') }}
                                            </button>
                                            <button @click="activeTab = 'en'" type="button" 
                                                class="btn flex-fill border-0 py-2 fw-bold small transition-all" 
                                                :class="activeTab === 'en' ? 'bg-success text-white shadow-sm' : 'text-muted'">
                                                {{ __('English') }}
                                            </button>
                                        </div>

                                        <div class="tab-content border rounded-2 p-4 bg-white shadow-sm">
                                            <!-- Bangla Section -->
                                            <div v-show="activeTab === 'bn'" class="animate__animated animate__fadeIn">
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Batch Title (Bangla)') }}</label>
                                                    <input v-model="form.title.bn" type="text" class="form-control standard-height border-2 shadow-none rounded-2" placeholder="উদাঃ জানুয়ারি ২০২৬ ব্যাচ" required>
                                                    <div v-if="form.errors['title.bn']" class="text-danger small mt-1">{{ form.errors['title.bn'] }}</div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Batch Description (Bangla)') }}</label>
                                                    <RichTextEditor v-model="form.description.bn" :placeholder="__('ব্যাচের বিস্তারিত তথ্য বাংলায় লিখুন...')" />
                                                    <div v-if="form.errors['description.bn']" class="text-danger small mt-1">{{ form.errors['description.bn'] }}</div>
                                                </div>
                                            </div>

                                            <!-- English Section -->
                                            <div v-show="activeTab === 'en'" class="animate__animated animate__fadeIn">
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Batch Title (English)') }}</label>
                                                    <input v-model="form.title.en" type="text" class="form-control standard-height border-2 shadow-none rounded-2" placeholder="e.g. January 2026 Batch" required>
                                                    <div v-if="form.errors['title.en']" class="text-danger small mt-1">{{ form.errors['title.en'] }}</div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-dark">{{ __('Batch Description (English)') }}</label>
                                                    <RichTextEditor v-model="form.description.en" :placeholder="__('Enter batch details in English...')" />
                                                    <div v-if="form.errors['description.en']" class="text-danger small mt-1">{{ form.errors['description.en'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Section 2: Scheduling -->
                            <fieldset class="premium-fieldset mb-5 border rounded-3 p-4 animate__animated animate__fadeIn">
                                <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                    <i class="bx bx-calendar me-1"></i> {{ __('SCHEDULE & TIMELINE') }}
                                </legend>
                                
                                <div class="row g-4 mt-1">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Start Date') }}</label>
                                        <BilingualDateInput v-model="form.start_date" :error="form.errors.start_date" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('End Date') }}</label>
                                        <BilingualDateInput v-model="form.end_date" :error="form.errors.end_date" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Enrollment Deadline') }}</label>
                                        <BilingualDateInput v-model="form.enrollment_deadline" :error="form.errors.enrollment_deadline" />
                                    </div>

                                    <div class="col-12">
                                        <div class="schedule-builder-bg rounded-3 p-4 border bg-light mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                    <i class="bx bx-time-five me-2 text-success"></i>{{ __('Weekly Routine / Schedule') }}
                                                </h6>
                                                <button @click="addSchedule" type="button" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm border-0 d-inline-flex align-items-center">
                                                    <i class="bx bx-plus me-1"></i>{{ __('Add Day') }}
                                                </button>
                                            </div>
                                            
                                            <div v-if="form.schedules.length === 0" class="alert alert-white border-dashed py-5 text-center text-muted rounded-3 bg-white">
                                                <i class="bx bx-calendar-x fs-1 d-block mb-3 opacity-25"></i>
                                                <span class="small fw-bold">{{ __('No weekly routine defined yet.') }}</span>
                                            </div>

                                            <div v-for="(sch, index) in form.schedules" :key="index" class="schedule-card animate__animated animate__fadeInUp mb-4 border rounded-3 bg-white shadow-sm overflow-hidden">
                                                <!-- Schedule Card Header -->
                                                <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                                    <span class="small fw-bold text-success text-uppercase letter-spacing-1">
                                                        <i class="bx bx-calendar-event me-1"></i> {{ __('Session') }} #{{ index + 1 }}
                                                    </span>
                                                    <div class="d-flex gap-2">
                                                        <button @click="clearSchedule(index)" type="button" class="btn btn-xs btn-row-action btn-clear-action px-2 d-inline-flex align-items-center" :title="__('Clear Fields')">
                                                            <i class="bx bx-eraser me-1"></i><span>{{ __('Clear') }}</span>
                                                        </button>
                                                        <button @click="removeSchedule(index)" type="button" class="btn btn-xs btn-row-action btn-remove-action px-2 d-inline-flex align-items-center" :title="__('Remove Row')">
                                                            <i class="bx bx-trash me-1"></i><span>{{ __('Remove') }}</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="p-3">
                                                    <div class="row g-3">
                                                        <div class="col-md-3">
                                                            <label class="small fw-bold text-muted mb-2 d-block text-uppercase">{{ __('Day') }}</label>
                                                            <Select2 v-model="sch.day_of_week" :placeholder="__('Select Day')" allowClear>
                                                                <option value="sat">{{ __('Saturday') }}</option>
                                                                <option value="sun">{{ __('Sunday') }}</option>
                                                                <option value="mon">{{ __('Monday') }}</option>
                                                                <option value="tue">{{ __('Tuesday') }}</option>
                                                                <option value="wed">{{ __('Wednesday') }}</option>
                                                                <option value="thu">{{ __('Thursday') }}</option>
                                                                <option value="fri">{{ __('Friday') }}</option>
                                                            </Select2>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small fw-bold text-muted mb-2 d-block text-uppercase">{{ __('Start Time') }}</label>
                                                            <BilingualDateInput v-model="sch.start_time" type="time" />
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small fw-bold text-muted mb-2 d-block text-uppercase">{{ __('End Time') }}</label>
                                                            <BilingualDateInput v-model="sch.end_time" type="time" />
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small fw-bold text-muted mb-2 d-block text-uppercase">{{ __('Platform') }}</label>
                                                            <Select2 v-model="sch.platform" :placeholder="__('Select Platform')" allowClear>
                                                                <option value="Google Meet">Google Meet</option>
                                                                <option value="Zoom">Zoom</option>
                                                                <option value="Physical Class">{{ __('Physical Class') }}</option>
                                                                <option value="LMS Live">{{ __('LMS Live') }}</option>
                                                            </Select2>
                                                        </div>

                                                        <!-- Row 2: Link / Location (Full Row) -->
                                                        <div v-if="sch.platform" class="col-12 animate__animated animate__fadeIn">
                                                            <hr class="my-2 opacity-5">
                                                            <label class="small fw-bold text-muted mb-2 d-block text-uppercase">
                                                                {{ sch.platform === 'Physical Class' ? __('Classroom / Location') : __('Meeting Link / URL') }}
                                                            </label>
                                                            <div class="input-group standard-height">
                                                                <span class="input-group-text bg-light border-2 border-end-0 px-3">
                                                                    <i :class="sch.platform === 'Physical Class' ? 'bx bx-map-pin' : 'bx bx-link-external'"></i>
                                                                </span>
                                                                <input v-model="sch.meeting_link" type="text" class="form-control border-2 border-start-0 shadow-none" 
                                                                    :placeholder="sch.platform === 'Physical Class' ? __('e.g. Room 402, Building A') : __('e.g. https://meet.google.com/xxx')">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Section 3: Capacity & Pricing -->
                            <fieldset class="premium-fieldset border rounded-3 p-4 animate__animated animate__fadeIn">
                                <legend class="float-none w-auto px-3 bg-white fs-6 fw-bold text-success mb-0 mx-3">
                                    <i class="bx bx-wallet me-1"></i> {{ __('CAPACITY & PRICING') }}
                                </legend>
                                
                                <div class="row g-4 mt-1">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Total Capacity (Seats)') }}</label>
                                        <input v-model="form.capacity" type="number" class="form-control standard-height border-2" min="0">
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">{{ __('Set 0 for unlimited seats.') }}</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Batch Price (BDT)') }}</label>
                                        <div class="input-group standard-height">
                                            <span class="input-group-text bg-light border-2 border-end-0 fw-bold px-3">৳</span>
                                            <input v-model="form.price" type="number" class="form-control border-2 border-start-0 shadow-none" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">{{ __('Status') }}</label>
                                        <Select2 v-model="form.status" :placeholder="__('Select Status')" allowClear>
                                            <option value="upcoming">{{ __('Upcoming') }}</option>
                                            <option value="ongoing">{{ __('Ongoing') }}</option>
                                            <option value="completed">{{ __('Completed') }}</option>
                                            <option value="cancelled">{{ __('Cancelled') }}</option>
                                        </Select2>
                                    </div>

                                    <div class="col-12">
                                        <div class="enrollment-switch-card p-3 rounded-2 border-2 border-dashed bg-light d-flex align-items-center justify-content-between mt-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white p-2 rounded-2 border shadow-sm me-3">
                                                    <i class="bx bx-user-plus text-success fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark small">{{ __('Open Enrollment') }}</h6>
                                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">{{ __('Allow students to register for this batch.') }}</p>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input v-model="form.is_enrollment_open" class="form-check-input custom-switch-large shadow-none" type="checkbox">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        
                        <div class="card-footer bg-light border-top p-4 d-flex justify-content-end gap-3">
                            <Link :href="route('batches.index')" class="btn btn-link text-muted fw-bold text-decoration-none px-4">{{ __('Cancel') }}</Link>
                            <button @click="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="bx bx-save me-2"></i>{{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.premium-fieldset { border-color: #e9ecef !important; }
.standard-height { height: 42px !important; }
.form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1); }
.border-dashed { border-style: dashed !important; border-width: 2px !important; }

.schedule-card {
    border-color: #dee2e6 !important;
    transition: all 0.2s ease;
}

/* Row Action Buttons Styling */
.btn-row-action {
    border: 0;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s ease;
    background: transparent;
}

.btn-clear-action {
    color: #6c757d;
}

.btn-clear-action:hover {
    background-color: #198754 !important;
    color: white !important;
}

.btn-remove-action {
    color: #dc3545;
}

.btn-remove-action:hover {
    background-color: #dc3545 !important;
    color: white !important;
}

.custom-switch-large { width: 3rem; height: 1.5rem; cursor: pointer; }
.transition-all { transition: all 0.25s ease; }
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }

.icon-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

legend { font-size: 0.8rem !important; letter-spacing: 0.5px; transform: translateY(-5px); }
.letter-spacing-1 { letter-spacing: 1px; }
</style>
