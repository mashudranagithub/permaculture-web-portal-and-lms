<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    initialTopicId: Number
});

const activeTopic = ref(props.course.topics.find(t => t.id === props.initialTopicId) || props.course.topics[0]);
const quizResult = ref(null);
const selectedAnswers = ref({});

const selectTopic = (topic) => {
    activeTopic.value = topic;
    quizResult.value = null;
    selectedAnswers.value = {};
};

const nextTopic = computed(() => {
    const index = props.course.topics.findIndex(t => t.id === activeTopic.value.id);
    return props.course.topics[index + 1] || null;
});

const prevTopic = computed(() => {
    const index = props.course.topics.findIndex(t => t.id === activeTopic.value.id);
    return props.course.topics[index - 1] || null;
});

const completeLesson = () => {
    router.post(route('topics.complete', activeTopic.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            if (nextTopic.value) {
                selectTopic(nextTopic.value);
            }
        }
    });
};

const selectOption = (qIndex, oIdx) => {
    if (quizResult.value) return; // Prevent changing after submission
    selectedAnswers.value[qIndex] = oIdx;
};

const submitQuiz = () => {
    if (activeTopic.value.quiz_data && Object.keys(selectedAnswers.value).length < activeTopic.value.quiz_data.length) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Quiz',
            text: 'Please answer all questions before submitting.',
            confirmButtonColor: '#198754'
        });
        return;
    }

    // Calculate Score
    let correctCount = 0;
    activeTopic.value.quiz_data.forEach((q, index) => {
        if (selectedAnswers.value[index] === q.correct_option) {
            correctCount++;
        }
    });

    const score = Math.round((correctCount / activeTopic.value.quiz_data.length) * 100);
    quizResult.value = {
        score,
        correctCount,
        total: activeTopic.value.quiz_data.length
    };

    if (score >= 70) {
        completeLesson();
    }
};

const getTypeIcon = (type) => {
    switch(type) {
        case 'video': return 'bi bi-play-btn';
        case 'pdf': return 'bi bi-file-earmark-pdf';
        case 'audio': return 'bi bi-volume-up';
        case 'online_class': return 'bi bi-broadcast';
        case 'quiz': return 'bi bi-patch-question';
        case 'assignment': return 'bi bi-journal-text';
        default: return 'bi bi-file-text';
    }
};

const getPdfUrl = (topic) => {
    const file = topic.pdf_file_bn || topic.pdf_file_en;
    if (!file) return null;
    const url = file.startsWith('http') ? file : `${window.location.origin}/storage/${file}`;
    if (url.startsWith('http') && !url.includes(window.location.hostname)) {
        return `https://docs.google.com/viewer?url=${encodeURIComponent(url)}&embedded=true`;
    }
    return url;
};

const getAudioUrl = (topic) => {
    const file = topic.audio_file_bn || topic.audio_file_en;
    if (!file) return null;
    return file.startsWith('http') ? file : `${window.location.origin}/storage/${file}`;
};

const getYoutubeId = (url) => {
    if (!url) return null;
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
    const match = url.match(regex);
    return match ? match[1] : null;
};
</script>

<template>
    <Head :title="activeTopic.title.bn || activeTopic.title.en" />

    <AuthenticatedLayout>
        <div class="learn-container d-flex">
            <!-- Sidebar Navigation -->
            <div class="curriculum-sidebar glass-morphism border-end shadow-sm">
                <div class="p-4 border-bottom glass-morphism-light">
                    <h6 class="fw-bold mb-1 text-dark">{{ course.title }}</h6>
                    <div class="progress mt-2" style="height: 6px; background-color: rgba(25, 135, 84, 0.1);">
                        <div class="progress-bar bg-success progress-bar-animated progress-bar-striped" role="progressbar" :style="{ width: (course.topics.filter(t => t.is_completed).length / course.topics.length * 100) + '%' }"></div>
                    </div>
                    <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">
                        {{ Math.round(course.topics.filter(t => t.is_completed).length / course.topics.length * 100) }}% Complete ({{ course.topics.filter(t => t.is_completed).length }}/{{ course.topics.length }})
                    </small>
                </div>
                <div class="topic-list scrollbar-simple">
                    <div v-for="(topic, index) in course.topics" :key="topic.id" 
                        @click="selectTopic(topic)"
                        class="topic-nav-item p-3 border-bottom cursor-pointer transition-all"
                        :class="{ 'active active-topic-gradient border-start border-4 border-success': activeTopic.id === topic.id }">
                        <div class="d-flex align-items-center gap-3">
                            <div class="topic-status-icon">
                                <i v-if="topic.is_completed" class="bi bi-check-circle-fill text-success scale-up"></i>
                                <i v-else class="bi bi-circle text-muted opacity-50"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="small fw-bold text-dark text-truncate">{{ topic.title.bn || topic.title.en }}</div>
                                <div class="x-small text-muted mt-1 d-flex align-items-center gap-2">
                                    <i :class="getTypeIcon(topic.topic_type)"></i>
                                    <span>{{ topic.estimated_duration }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area flex-grow-1 bg-light-soft">
                <div class="content-header glass-morphism border-bottom p-4 d-flex justify-content-between align-items-center sticky-top z-3">
                    <h4 class="fw-bold mb-0 text-dark font-outfit">{{ activeTopic.title.bn || activeTopic.title.en }}</h4>
                    <div class="d-flex gap-2">
                        <button v-if="prevTopic" @click="selectTopic(prevTopic)" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold transition-all hover-lift">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Previous') }}
                        </button>
                        <button v-if="nextTopic" @click="completeLesson" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
                            {{ __('Next Lesson') }} <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        <button v-else-if="!activeTopic.is_completed" @click="completeLesson" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
                            <i class="bi bi-check2-circle me-1"></i> {{ __('Mark as Complete') }}
                        </button>
                    </div>
                </div>

                <div class="p-4 p-md-5 overflow-auto custom-scrollbar" style="height: calc(100vh - 160px);">
                    <div class="max-w-4xl mx-auto">
                        <Transition name="fade" mode="out-in">
                            <div :key="activeTopic.id">
                                <!-- Video Player -->
                                <div v-if="activeTopic.topic_type === 'video'" class="mb-5 shadow-lg rounded-4 overflow-hidden bg-black border border-white-10">
                                    <div v-if="activeTopic.video_url && getYoutubeId(activeTopic.video_url)" class="ratio ratio-16x9">
                                        <iframe :src="`https://www.youtube.com/embed/${getYoutubeId(activeTopic.video_url)}`" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    </div>
                                    <div v-if="activeTopic.video_url" class="p-3 bg-dark-soft border-top d-flex justify-content-between align-items-center">
                                        <span class="text-white-50 small"><i class="bi bi-info-circle me-1"></i> {{ __('Video not playing?') }}</span>
                                        <a :href="activeTopic.video_url" target="_blank" class="btn btn-outline-light btn-xs rounded-pill px-3">
                                            <i class="bi bi-youtube me-1"></i> {{ __('Watch on YouTube') }}
                                        </a>
                                    </div>
                                    <div v-else class="p-5 text-center text-white">
                                        <i class="bi bi-play-circle fs-1 text-muted mb-3 d-block"></i>
                                        <h5>{{ __('Video Lesson') }}</h5>
                                        <p class="text-muted small">{{ __('Video URL:') }} {{ activeTopic.video_url || 'None provided' }}</p>
                                    </div>
                                </div>

                                <!-- Rich Content -->
                                <div v-if="activeTopic.content_body" class="bg-white p-5 rounded-4 shadow-sm border-0 mb-5 topic-content glass-card">
                                    <div v-html="activeTopic.content_body.bn || activeTopic.content_body.en"></div>
                                </div>

                                <!-- PDF Viewer -->
                                <div v-if="activeTopic.topic_type === 'pdf'" class="mb-5 shadow-lg rounded-4 overflow-hidden bg-white border-0 glass-card">
                                    <div v-if="getPdfUrl(activeTopic)" class="pdf-container">
                                        <iframe :src="getPdfUrl(activeTopic)" width="100%" height="800px" style="border: none;"></iframe>
                                        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                                            <span class="text-muted small"><i class="bi bi-file-earmark-pdf me-1"></i> {{ __('Learning Resource PDF') }}</span>
                                            <a :href="getPdfUrl(activeTopic)" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-4">
                                                <i class="bi bi-download me-1"></i> {{ __('Download for Offline') }}
                                            </a>
                                        </div>
                                    </div>
                                    <div v-else class="text-center p-5">
                                        <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-4 d-block"></i>
                                        <h5 class="fw-bold">{{ __('Download Learning Material') }}</h5>
                                        <p class="text-muted">{{ __('This lesson includes a PDF guide.') }}</p>
                                    </div>
                                </div>

                                <!-- Quiz Interface -->
                                <div v-if="activeTopic.topic_type === 'quiz'" class="bg-white p-5 rounded-4 shadow-sm border-0 mb-5 glass-card">
                                    <div class="text-center">
                                        <div class="bg-success-subtle d-inline-block p-4 rounded-circle mb-4">
                                            <i class="bi bi-patch-question fs-1 text-success"></i>
                                        </div>
                                        <h4 class="fw-bold">{{ __('Lesson Quiz') }}</h4>
                                        <p class="text-muted mb-5">{{ __('Test your knowledge on what you just learned.') }}</p>
                                        
                                        <div v-if="activeTopic.quiz_data && !quizResult" class="text-start">
                                            <div v-for="(q, index) in activeTopic.quiz_data" :key="index" class="p-4 border-0 rounded-4 mb-4 bg-light-soft shadow-sm">
                                                <h6 class="fw-bold mb-3 d-flex justify-content-between">
                                                    <span>{{ index + 1 }}. {{ q.question.bn || q.question.en }}</span>
                                                    <span class="badge bg-white text-dark border shadow-sm small">{{ q.points }} pts</span>
                                                </h6>
                                                <div class="d-grid gap-2">
                                                    <div v-for="(opt, oIdx) in q.options" :key="oIdx" 
                                                        @click="selectOption(index, oIdx)"
                                                        class="form-check p-3 border rounded-3 bg-white transition-all cursor-pointer d-flex align-items-center hover-bg-success-light"
                                                        :class="{ 'border-success bg-success-subtle shadow-sm': selectedAnswers[index] === oIdx }">
                                                        <input class="form-check-input ms-0 me-3 mt-0" type="radio" 
                                                            :name="'q_'+index" 
                                                            :id="'q_'+index+'_'+oIdx"
                                                            :checked="selectedAnswers[index] === oIdx">
                                                        <label class="form-check-label fw-medium w-100 cursor-pointer" :for="'q_'+index+'_'+oIdx">
                                                            {{ opt.bn || opt.en }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <button @click="submitQuiz" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-lg mt-4 transition-all hover-lift">
                                                <i class="bi bi-send me-2"></i> {{ __('Submit Quiz Answers') }}
                                            </button>
                                        </div>

                                        <!-- Quiz Results -->
                                        <div v-if="quizResult" class="quiz-result-view animate__animated animate__fadeIn">
                                            <div class="display-4 fw-bold mb-2" :class="quizResult.score >= 70 ? 'text-success' : 'text-danger'">
                                                {{ quizResult.score }}%
                                            </div>
                                            <p class="lead fw-bold mb-4">
                                                {{ quizResult.score >= 70 ? __('Excellent! You passed.') : __('Keep trying! You need 70% to pass.') }}
                                            </p>
                                            <div class="p-4 bg-light rounded-4 mb-5">
                                                <div class="d-flex justify-content-around">
                                                    <div class="text-center">
                                                        <div class="h3 mb-0">{{ quizResult.correctCount }}</div>
                                                        <small class="text-muted">{{ __('Correct') }}</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="h3 mb-0">{{ quizResult.total }}</div>
                                                        <small class="text-muted">{{ __('Total') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="quizResult.score < 70" @click="quizResult = null" class="btn btn-outline-success rounded-pill px-5 py-2 fw-bold">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i> {{ __('Try Again') }}
                                            </div>
                                            <div v-else class="alert alert-success rounded-pill py-3 fw-bold">
                                                <i class="bi bi-check-circle me-2"></i> {{ __('Lesson marked as complete!') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Audio Player -->
                                <div v-if="activeTopic.topic_type === 'audio'" class="p-5 bg-white rounded-4 border-0 shadow-sm mb-5 text-center glass-card">
                                    <div class="bg-info-subtle d-inline-block p-4 rounded-circle mb-4">
                                        <i class="bi bi-volume-up fs-1 text-info"></i>
                                    </div>
                                    <h5 class="fw-bold mb-4">{{ __('Listen to Audio Lecture') }}</h5>
                                    <div v-if="getAudioUrl(activeTopic)" class="max-w-md mx-auto p-4 bg-light rounded-pill shadow-inner">
                                        <audio controls class="w-100 custom-audio-player">
                                            <source :src="getAudioUrl(activeTopic)" type="audio/mpeg">
                                        </audio>
                                    </div>
                                    <div v-else class="alert alert-warning d-inline-block rounded-pill px-4">
                                        <i class="bi bi-exclamation-triangle me-2"></i> {{ __('Audio file not found.') }}
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.learn-container {
    height: calc(100vh - 65px);
    overflow: hidden;
}
.curriculum-sidebar {
    width: 320px;
    height: 100%;
    display: flex;
    flex-direction: column;
    z-index: 10;
}

/* Glassmorphism Effect */
.glass-morphism {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.glass-morphism-light {
    background: rgba(255, 255, 255, 0.4) !important;
}
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.bg-light-soft {
    background-color: #fcfdfe;
}

.topic-list {
    flex-grow: 1;
    overflow-y: auto;
}
.topic-nav-item:hover:not(.active) {
    background-color: rgba(25, 135, 84, 0.05);
}
.active-topic-gradient {
    background: linear-gradient(90deg, rgba(25, 135, 84, 0.08) 0%, rgba(255, 255, 255, 0) 100%);
}

.content-area {
    height: 100%;
    position: relative;
}

.topic-content :deep(p) { margin-bottom: 1.5rem; line-height: 1.8; color: #333; font-size: 1.15rem; }
.topic-content :deep(h1), .topic-content :deep(h2), .topic-content :deep(h3) { margin-top: 2.5rem; margin-bottom: 1.25rem; font-weight: 800; color: #111; font-family: 'Outfit', sans-serif; }

.x-small { font-size: 0.7rem; }
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.hover-bg-success-light:hover {
    background-color: rgba(25, 135, 84, 0.03) !important;
    border-color: #198754 !important;
}

/* Animations */
.scale-up {
    animation: scaleUp 0.3s ease-out;
}
@keyframes scaleUp {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.shadow-inner {
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
}

.custom-audio-player::-webkit-media-controls-panel {
    background-color: #f8f9fa;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.2);
}

.z-3 { z-index: 1000; }
</style>
