<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import draggable from 'vuedraggable';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import Modal from '@/Components/Modal.vue';
import Select2 from '@/Components/Select2.vue';
import Swal from 'sweetalert2';
const props = defineProps({
    course: Object
});

const page = usePage();
const showTopicModal = ref(false);
const editingTopic = ref(null);
const activeTab = ref('bn');

// Sync list with props
const list = ref([]);
watch(() => props.course.topics, (newTopics) => {
    list.value = [...newTopics];
}, { immediate: true });

const form = useForm({
    id: null,
    course_id: props.course.id,
    title: { en: '', bn: '' },
    description: { en: '', bn: '' },
    content_body: { en: '', bn: '' },
    topic_type: 'content',
    video_url: '',
    estimated_duration: '',
    is_published: true,
    is_free_preview: false,
    pdf_file_en: null,
    pdf_file_bn: null,
    audio_file_en: null,
    audio_file_bn: null,
    quiz_data: [],
});

const openCreateModal = () => {
    editingTopic.value = null;
    form.clearErrors();
    form.reset();
    
    // Force deep reset for objects and specific fields
    form.id = null;
    form.title = { en: '', bn: '' };
    form.description = { en: '', bn: '' };
    form.content_body = { en: '', bn: '' };
    form.topic_type = 'content';
    form.video_url = '';
    form.estimated_duration = '';
    form.is_published = true;
    form.is_free_preview = false;
    form.pdf_file_en = null;
    form.pdf_file_bn = null;
    form.audio_file_en = null;
    form.audio_file_bn = null;
    form.quiz_data = [];
    form.course_id = props.course.id;
    
    showTopicModal.value = true;
};

const openEditModal = (topic) => {
    editingTopic.value = topic;
    form.id = topic.id;
    form.course_id = topic.course_id;
    form.title = { ...topic.title };
    form.description = { ...topic.description };
    form.content_body = { ...topic.content_body };
    form.topic_type = topic.topic_type;
    form.video_url = topic.video_url || '';
    form.estimated_duration = topic.estimated_duration || '';
    form.is_published = !!topic.is_published;
    form.is_free_preview = !!topic.is_free_preview;
    form.pdf_file_en = null;
    form.pdf_file_bn = null;
    form.audio_file_en = null;
    form.audio_file_bn = null;
    form.quiz_data = topic.quiz_data || [];
    showTopicModal.value = true;
};

const addQuestion = () => {
    form.quiz_data.push({
        id: Date.now(),
        question: { en: '', bn: '' },
        type: 'mcq',
        options: [
            { en: '', bn: '' },
            { en: '', bn: '' }
        ],
        correct_answer: 0,
        explanation: { en: '', bn: '' },
        points: 1
    });
};

const removeQuestion = (index) => {
    form.quiz_data.splice(index, 1);
};

const addOption = (questionIndex) => {
    form.quiz_data[questionIndex].options.push({ en: '', bn: '' });
};

const removeOption = (questionIndex, optionIndex) => {
    if (form.quiz_data[questionIndex].options.length > 2) {
        form.quiz_data[questionIndex].options.splice(optionIndex, 1);
    }
};

const getMediaUrl = (path) => {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
};

const MAX_AUDIO_SIZE = 5 * 1024 * 1024; // 5MB
const MAX_PDF_SIZE = 10 * 1024 * 1024;  // 10MB

const handleFileSelect = (event, field, maxSize, label) => {
    const file = event.target.files[0];
    if (!file) return;

    const sizeMB = (maxSize / 1024 / 1024).toFixed(0);
    const fileSizeMB = (file.size / 1024 / 1024).toFixed(1);

    // Helper for translations
    const translate = (key) => window.__ ? window.__(key) : key;

    if (file.size > maxSize) {
        const sizeMB = (maxSize / 1024 / 1024).toFixed(0);
        const fileSizeMB = (file.size / 1024 / 1024).toFixed(1);
        
        // Use Inertia's built-in error handling to show the message inline
        form.setError(field, `The ${label} is too large (${fileSizeMB}MB). Maximum allowed size is ${sizeMB}MB.`);
        
        event.target.value = ''; // Reset file input
        form[field] = null;
        return;
    }

    // Clear error if a valid file is selected
    form.clearErrors(field);
    form[field] = file;
};

const submit = () => {
    if (form.id) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('topics.update', form.id), {
            forceFormData: true,
            onSuccess: () => {
                showTopicModal.value = false;
                router.reload({ only: ['course'] });
            },
            onError: (errors) => {
                console.error('Topic update errors:', errors);
            }
        });
    } else {
        form.post(route('topics.store'), {
            forceFormData: true,
            onSuccess: () => {
                showTopicModal.value = false;
                router.reload({ only: ['course'] });
            },
            onError: (errors) => {
                console.error('Topic store errors:', errors);
            }
        });
    }
};

const deleteTopic = (id) => {
    Swal.fire({
        title: __('Delete Topic?'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: __('Yes, Delete'),
        cancelButtonText: __('Cancel')
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('topics.destroy', id), {
                onSuccess: () => router.reload({ only: ['course'] })
            });
        }
    });
};

const handleReorder = () => {
    const reordered = list.value.map((item, index) => ({
        id: item.id,
        order_index: index + 1
    }));
    router.post(route('topics.reorder'), { topics: reordered }, { preserveScroll: true });
};

const getTypeIcon = (type) => {
    switch(type) {
        case 'video': return 'bx bxl-youtube text-danger';
        case 'pdf': return 'bx bxs-file-pdf text-warning';
        case 'audio': return 'bx bx-volume-full text-info';
        case 'online_class': return 'bx bx-video text-primary';
        case 'quiz': return 'bx bx-list-check text-success';
        case 'assignment': return 'bx bx-task text-warning';
        default: return 'bx bx-file-blank text-secondary';
    }
};

const totalDuration = computed(() => {
    return props.course.topics.reduce((acc, topic) => {
        const match = topic.estimated_duration?.match(/\d+/);
        return acc + (match ? parseInt(match[0]) : 0);
    }, 0);
});
</script>

<template>
    <Head :title="__('Curriculum Builder')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <Link :href="route('courses.index')" class="btn btn-sm btn-light border rounded-3 me-3 px-2 shadow-sm" :title="__('Back to Courses')">
                        <i class="bx bx-chevron-left fs-4"></i>
                    </Link>
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="mb-0 fw-bold text-dark lh-1">{{ course.title }}</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 x-small fw-bold">
                                <i class="bx bx-layer me-1"></i>{{ course.topics.length }} {{ __('Topics') }}
                            </span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1 x-small fw-bold">
                                <i class="bx bx-time-five me-1"></i>{{ totalDuration }} {{ __('Minutes') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                        <!-- THE FIXED CARD HEADER WITH PSEUDO-ELEMENT DISABLE -->
                        <div class="card-header bg-white border-bottom py-4 px-4 d-flex justify-content-between align-items-center curriculum-header">
                            <div class="header-info">
                                <h5 class="mb-1 fw-bold text-dark">{{ __('Course Curriculum') }}</h5>
                                <p class="mb-0 text-muted small">{{ __('Organize and manage your lesson topics below.') }}</p>
                            </div>
                            <div class="header-actions">
                                <button @click="openCreateModal" class="btn btn-success rounded-3 px-4 py-2 fw-bold d-flex align-items-center shadow-sm">
                                    <i class="bx bx-plus me-2 fs-5"></i>{{ __('Add New Topic') }}
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <!-- Empty State -->
                            <div v-if="course.topics.length === 0" class="p-5 text-center">
                                <div class="bg-light rounded-circle p-4 d-inline-block mb-4">
                                    <i class="bx bx-layer-plus fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-bold">{{ __('No topics added yet') }}</h5>
                                <p class="text-muted small">{{ __('Your curriculum is empty. Add your first topic to get started.') }}</p>
                                <button @click="openCreateModal" class="btn btn-outline-success rounded-3 mt-2 px-4">{{ __('Add First Topic') }}</button>
                            </div>

                            <!-- List -->
                            <div v-else class="p-4">
                                <draggable v-model="list" item-key="id" handle=".drag-handle" @end="handleReorder" ghost-class="ghost-item">
                                    <template #item="{ element, index }">
                                        <div class="topic-item mb-3 p-3 rounded-3 border bg-white shadow-sm-hover d-flex align-items-center transition-all">
                                            <div class="drag-handle p-2 text-muted cursor-move me-3 hover-text-success">
                                                <i class="bx bx-grid-vertical fs-3"></i>
                                            </div>
                                            
                                            <div class="topic-counter fw-bold text-muted opacity-50 me-3">{{ index + 1 }}</div>

                                            <div class="topic-icon-box bg-light rounded-3 me-3 d-flex align-items-center justify-content-center">
                                                <i :class="getTypeIcon(element.topic_type) + ' fs-4'"></i>
                                            </div>

                                            <div class="flex-grow-1 overflow-hidden me-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="mb-0 fw-bold text-dark text-truncate">{{ element.title.bn || element.title.en }}</h6>
                                                    <span v-if="!element.is_published" class="badge bg-light text-muted border border-secondary-subtle x-small fw-normal rounded-1">{{ __('Draft') }}</span>
                                                    <span v-if="element.is_free_preview" class="text-success small" title="Free Preview"><i class="bx bx-lock-open-alt"></i></span>
                                                </div>
                                                <div class="x-small text-muted mt-1 fw-medium text-capitalize">
                                                    <i class="bx bx-time-five me-1"></i>{{ element.estimated_duration || '0m' }} &bull; {{ element.topic_type.replace('_', ' ') }}
                                                </div>
                                            </div>

                                            <div class="topic-item-actions d-flex gap-2">
                                                <button @click="openEditModal(element)" class="btn btn-sm btn-outline-primary border-0 bg-primary-subtle text-primary rounded-2 d-inline-flex align-items-center justify-content-center shadow-sm transition-all hover-scale" style="width: 32px; height: 32px;" :title="__('Edit Lesson')">
                                                    <i class="bx bx-edit-alt fs-6"></i>
                                                </button>
                                                <button @click="deleteTopic(element.id)" class="btn btn-sm btn-outline-danger border-0 bg-danger-subtle text-danger rounded-2 d-inline-flex align-items-center justify-content-center shadow-sm transition-all hover-scale" style="width: 32px; height: 32px;" :title="__('Delete Lesson')">
                                                    <i class="bx bx-trash fs-6"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vertically Centered Modal -->
        <Modal :show="showTopicModal" @close="showTopicModal = false" maxWidth="70%">
            <div v-if="showTopicModal" class="modal-content border-0 rounded-4 overflow-hidden shadow-lg bg-white">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">{{ editingTopic ? __('Edit Topic') : __('Create New Topic') }}</h5>
                    <button type="button" @click="showTopicModal = false" class="btn-close shadow-none"></button>
                </div>
                <div class="modal-body p-4 scrollable-body">
                    <form @submit.prevent="submit">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">{{ __('Topic Type') }}</label>
                                <Select2 v-model="form.topic_type" :placeholder="__('Select Type')" required>
                                    <option value="content">{{ __('Rich Content Lesson') }}</option>
                                    <option value="pdf">{{ __('PDF / Document Handout') }}</option>
                                    <option value="audio">{{ __('Audio Class / Podcast') }}</option>
                                    <option value="video">{{ __('YouTube Video Lesson') }}</option>
                                    <option value="online_class">{{ __('Live Class Session') }}</option>
                                    <option value="assignment">{{ __('Student Assignment') }}</option>
                                    <option value="quiz">{{ __('Topic Quiz') }}</option>
                                </Select2>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">{{ __('Duration') }}</label>
                                <input v-model="form.estimated_duration" type="text" class="form-control standard-height border-2" :placeholder="__('e.g. 45 mins')">
                            </div>

                            <!-- Common Title Section -->
                            <div class="col-12 mt-2">
                                <div class="p-3 bg-white rounded-3 border">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">{{ __('Topic Title (Bangla)') }}</label>
                                            <input v-model="form.title.bn" type="text" class="form-control standard-height border-2" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">{{ __('Topic Title (English)') }}</label>
                                            <input v-model="form.title.en" type="text" class="form-control standard-height border-2" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.topic_type !== 'quiz'" class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">{{ __('Bilingual Lesson Content') }}</label>
                                    <div class="btn-group btn-group-sm p-1 bg-light rounded-3 border">
                                        <button @click="activeTab = 'bn'" type="button" class="btn px-4 fw-bold" :class="activeTab === 'bn' ? 'btn-success shadow-sm' : 'btn-light border-0 text-muted'">{{ __('Bangla') }}</button>
                                        <button @click="activeTab = 'en'" type="button" class="btn px-4 fw-bold" :class="activeTab === 'en' ? 'btn-success shadow-sm' : 'btn-light border-0 text-muted'">{{ __('English') }}</button>
                                    </div>
                                </div>

                                <div class="p-3 bg-light rounded-3 border">
                                    <div v-show="activeTab === 'bn'" class="animate__animated animate__fadeIn">
                                        <RichTextEditor v-model="form.content_body.bn" :height="250" />
                                    </div>
                                    <div v-show="activeTab === 'en'" class="animate__animated animate__fadeIn">
                                        <RichTextEditor v-model="form.content_body.en" :height="250" />
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Builder -->
                            <div v-if="form.topic_type === 'quiz'" class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-dark mb-0"><i class="bx bx-list-check me-2 text-success"></i>{{ __('Quiz Questions Builder') }}</h6>
                                    <button type="button" @click="addQuestion" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                        <i class="bx bx-plus me-1"></i>{{ __('Add Question') }}
                                    </button>
                                </div>

                                <div class="quiz-questions-container">
                                    <div v-for="(q, qIdx) in form.quiz_data" :key="q.id" class="question-card border rounded-4 p-4 mb-4 bg-white shadow-sm transition-all hover-border-success">
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">{{ __('Question') }} {{ qIdx + 1 }}</span>
                                            <button type="button" @click="removeQuestion(qIdx)" class="btn btn-sm btn-outline-danger border-0 rounded-circle" :title="__('Remove Question')">
                                                <i class="bx bx-trash fs-5"></i>
                                            </button>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-8">
                                                <div class="mb-4">
                                                    <label class="form-label x-small fw-bold text-muted text-uppercase">{{ __('Question Text (English)') }}</label>
                                                    <input v-model="q.question.en" type="text" class="form-control border-2" required>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label x-small fw-bold text-muted text-uppercase">{{ __('Question Text (Bangla)') }}</label>
                                                    <input v-model="q.question.bn" type="text" class="form-control border-2" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label x-small fw-bold text-muted text-uppercase">{{ __('Question Type') }}</label>
                                                    <select v-model="q.type" class="form-select border-2">
                                                        <option value="mcq">{{ __('Multiple Choice') }}</option>
                                                        <option value="true_false">{{ __('True / False') }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label x-small fw-bold text-muted text-uppercase">{{ __('Points') }}</label>
                                                    <input v-model="q.points" type="number" class="form-control border-2">
                                                </div>
                                            </div>

                                            <div v-if="q.type === 'mcq'" class="col-12 mt-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-3 d-block border-bottom pb-2">{{ __('Answer Options') }}</label>
                                                <div v-for="(opt, oIdx) in q.options" :key="oIdx" class="option-row d-flex gap-3 align-items-start mb-3 animate__animated animate__fadeIn">
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input" type="radio" :name="'correct_'+qIdx" :value="oIdx" v-model="q.correct_answer">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input v-model="opt.en" type="text" class="form-control form-control-sm border-2 mb-1" :placeholder="__('Option EN')">
                                                        <input v-model="opt.bn" type="text" class="form-control form-control-sm border-2" :placeholder="__('Option BN')">
                                                    </div>
                                                    <button type="button" @click="removeOption(qIdx, oIdx)" class="btn btn-sm text-danger border-0 pt-2" :disabled="q.options.length <= 2">
                                                        <i class="bx bx-x fs-4"></i>
                                                    </button>
                                                </div>
                                                <button type="button" @click="addOption(qIdx)" class="btn btn-sm btn-link text-success text-decoration-none fw-bold p-0 mt-2">
                                                    <i class="bx bx-plus me-1"></i>{{ __('Add Option') }}
                                                </button>
                                            </div>

                                            <div v-if="q.type === 'true_false'" class="col-12 mt-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-3 d-block border-bottom pb-2">{{ __('Correct Answer') }}</label>
                                                <div class="d-flex gap-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" :name="'tf_'+qIdx" :value="0" v-model="q.correct_answer" :id="'t_'+qIdx">
                                                        <label class="form-check-label fw-bold" :for="'t_'+qIdx">{{ __('True') }}</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" :name="'tf_'+qIdx" :value="1" v-model="q.correct_answer" :id="'f_'+qIdx">
                                                        <label class="form-check-label fw-bold" :for="'f_'+qIdx">{{ __('False') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">{{ __('Explanation / Feedback') }}</label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <textarea v-model="q.explanation.en" class="form-control border-2" rows="2" :placeholder="__('Explain the answer in English...')"></textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <textarea v-model="q.explanation.bn" class="form-control border-2" rows="2" :placeholder="__('Explain the answer in Bangla...')"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="form.quiz_data.length === 0" class="p-5 text-center border rounded-4 bg-light border-dashed">
                                    <i class="bx bx-question-mark fs-1 text-muted opacity-50 mb-3"></i>
                                    <p class="text-muted small mb-0">{{ __('No questions added. Click "Add Question" to start building your quiz.') }}</p>
                                </div>
                            </div>

                            <!-- Media Fields -->
                            <div v-if="form.topic_type === 'video'" class="col-12">
                                <div class="p-3 bg-danger-subtle rounded-3 border border-danger border-opacity-10">
                                    <label class="form-label x-small fw-bold text-danger">{{ __('YouTube Link') }}</label>
                                    <input v-model="form.video_url" type="url" class="form-control border-2" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                            </div>

                            <div v-if="form.topic_type === 'pdf'" class="col-12 row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Bangla PDF') }}</label>
                                    <input type="file" @change="handleFileSelect($event, 'pdf_file_bn', MAX_PDF_SIZE, 'Bangla PDF')" class="form-control border-2 bg-white" accept=".pdf">
                                    <div class="x-small text-muted mt-1">{{ __('Max 10MB') }} &bull; PDF</div>
                                    <div v-if="form.errors.pdf_file_bn" class="text-danger x-small mt-1">{{ form.errors.pdf_file_bn }}</div>
                                    <div v-if="editingTopic?.pdf_file_bn" class="mt-2">
                                        <a :href="getMediaUrl(editingTopic.pdf_file_bn)" target="_blank" class="btn btn-sm btn-outline-secondary w-100 py-1">
                                            <i class="bx bxs-file-pdf me-1"></i>{{ __('View Current PDF') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('English PDF') }}</label>
                                    <input type="file" @change="handleFileSelect($event, 'pdf_file_en', MAX_PDF_SIZE, 'English PDF')" class="form-control border-2 bg-white" accept=".pdf">
                                    <div class="x-small text-muted mt-1">{{ __('Max 10MB') }} &bull; PDF</div>
                                    <div v-if="form.errors.pdf_file_en" class="text-danger x-small mt-1">{{ form.errors.pdf_file_en }}</div>
                                    <div v-if="editingTopic?.pdf_file_en" class="mt-2">
                                        <a :href="getMediaUrl(editingTopic.pdf_file_en)" target="_blank" class="btn btn-sm btn-outline-secondary w-100 py-1">
                                            <i class="bx bxs-file-pdf me-1"></i>{{ __('View Current PDF') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.topic_type === 'audio'" class="col-12 row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Bangla Audio') }}</label>
                                    <input type="file" @change="handleFileSelect($event, 'audio_file_bn', MAX_AUDIO_SIZE, 'Bangla Audio')" class="form-control border-2 bg-white" accept="audio/*">
                                    <div class="x-small text-muted mt-1">{{ __('Max 5MB') }} &bull; MP3, WAV, OGG</div>
                                    <div v-if="form.errors.audio_file_bn" class="text-danger x-small mt-1">{{ form.errors.audio_file_bn }}</div>
                                    <div v-if="editingTopic?.audio_file_bn" class="mt-2 p-2 bg-light rounded border">
                                        <div class="x-small text-muted mb-1">{{ __('Current Audio:') }}</div>
                                        <audio controls class="w-100" style="height: 35px;">
                                            <source :src="getMediaUrl(editingTopic.audio_file_bn)" type="audio/mpeg">
                                        </audio>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('English Audio') }}</label>
                                    <input type="file" @change="handleFileSelect($event, 'audio_file_en', MAX_AUDIO_SIZE, 'English Audio')" class="form-control border-2 bg-white" accept="audio/*">
                                    <div class="x-small text-muted mt-1">{{ __('Max 5MB') }} &bull; MP3, WAV, OGG</div>
                                    <div v-if="form.errors.audio_file_en" class="text-danger x-small mt-1">{{ form.errors.audio_file_en }}</div>
                                    <div v-if="editingTopic?.audio_file_en" class="mt-2 p-2 bg-light rounded border">
                                        <div class="x-small text-muted mb-1">{{ __('Current Audio:') }}</div>
                                        <audio controls class="w-100" style="height: 35px;">
                                            <source :src="getMediaUrl(editingTopic.audio_file_en)" type="audio/mpeg">
                                        </audio>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-5 justify-content-center p-3 border rounded-3 bg-white shadow-sm mt-4">
                                <div class="form-check form-switch">
                                    <input v-model="form.is_published" class="form-check-input custom-switch" type="checkbox" id="pubCheck">
                                    <label class="form-check-label fw-bold small ms-2" for="pubCheck">{{ __('Published') }}</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input v-model="form.is_free_preview" class="form-check-input custom-switch" type="checkbox" id="freeCheck">
                                    <label class="form-check-label fw-bold small ms-2" for="freeCheck">{{ __('Free Preview') }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light p-4 d-flex justify-content-between">
                    <button @click="showTopicModal = false" class="btn btn-link text-muted fw-bold text-decoration-none px-0">{{ __('Cancel') }}</button>
                    <button @click="submit" class="btn btn-success btn-lg px-5 rounded-3 fw-bold shadow-sm" :disabled="form.processing">
                        {{ editingTopic ? __('Update Lesson') : __('Save Lesson') }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.standard-height { height: 42px !important; }
.x-small { font-size: 0.72rem; }
.transition-all { transition: all 0.2s ease-in-out; }

/* FIX FOR THE CENTERED BUTTON ISSUE */
.curriculum-header::after {
    display: none !important;
}

.shadow-sm-hover:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important; transform: translateY(-2px); border-color: #198754 !important; }

.topic-icon-box { width: 42px; height: 42px; flex-shrink: 0; }
.drag-handle { cursor: grab; }
.drag-handle:active { cursor: grabbing; }

.ghost-item { opacity: 0.2; background: #e8f5e9 !important; border: 2px dashed #198754 !important; }

.modal-content { min-width: 100% !important; }
.scrollable-body { max-height: 65vh; overflow-y: auto; }
.scrollable-body::-webkit-scrollbar { width: 5px; }
.scrollable-body::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }

.custom-switch { width: 2.8rem; height: 1.4rem; cursor: pointer; }
.hover-text-primary:hover { color: #0d6efd !important; }
.hover-text-danger:hover { color: #dc3545 !important; }
.hover-text-success:hover { color: #198754 !important; }
.hover-border-success:hover { border-color: #198754 !important; }
.border-dashed { border-style: dashed !important; }
.question-card { transition: border-color 0.2s ease; }

</style>

