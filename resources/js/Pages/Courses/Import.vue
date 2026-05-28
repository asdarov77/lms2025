<template>
    <AppLayout title="Импорт курсов">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-semibold mb-6">Импорт SCORM / GIFT пакетов</h2>

                        <form @submit.prevent="submitImport" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Тип импорта</label>
                                <select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="scorm">SCORM пакет (.zip)</option>
                                    <option value="gift">GIFT формат (.txt/.zip)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Целевой курс</label>
                                <select v-model="form.course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled>Выберите курс</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.title }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Файл</label>
                                <input type="file" @change="form.file = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                            </div>

                            <div v-if="form.type === 'scorm' && form.file" class="bg-blue-50 p-4 rounded-md">
                                <p class="text-sm text-blue-700">После загрузки файл будет распакован, структура курса проанализирована и сохранена в базу данных.</p>
                            </div>

                            <button type="submit" :disabled="processing" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <span v-if="!processing">Загрузить и обработать</span>
                                <span v-else>Обработка...</span>
                            </button>
                        </form>

                        <div v-if="successMessage" class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ successMessage }}
                        </div>
                        <div v-if="errorMessage" class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            {{ errorMessage }}
                        </div>
                    </div>
                </div>

                <!-- Секция назначения групп -->
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-semibold mb-6">Назначить группы на курс</h2>
                        <form @submit.prevent="assignGroups" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Курс</label>
                                <select v-model="assignForm.course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled>Выберите курс</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.title }}
                                    </option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Группы (специальности)</label>
                                <div class="mt-2 space-y-2 max-h-60 overflow-y-auto border rounded-md p-2">
                                    <div v-for="group in groups" :key="group.id" class="flex items-center">
                                        <input type="checkbox" :value="group.id" v-model="assignForm.group_ids" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"/>
                                        <label class="ml-3 block text-sm text-gray-700">
                                            {{ group.name }} 
                                            <span v-if="group.aircraft" class="text-gray-500 text-xs">({{ group.aircraft.name }})</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 p-4 rounded-md">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="partialEnrollment" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"/>
                                    <span class="ml-2 text-sm text-gray-700">Записать только на выбранные модули</span>
                                </label>
                                
                                <div v-if="partialEnrollment" class="mt-4 pl-6">
                                    <p class="text-xs text-gray-500 mb-2">Выберите категории/модули курса:</p>
                                    <div class="space-y-1">
                                        <div v-for="cat in availableCategories" :key="cat.id" class="flex items-center">
                                            <input type="checkbox" :value="cat.id" v-model="assignForm.category_ids" class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"/>
                                            <span class="ml-2 text-sm text-gray-600">{{ cat.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" :disabled="processing" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Назначить группы
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    courses: Array,
    groups: Array,
    categories: Array
});

const processing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const partialEnrollment = ref(false);

const form = useForm({
    file: null,
    course_id: '',
    type: 'scorm'
});

const assignForm = useForm({
    group_ids: [],
    course_id: '',
    category_ids: []
});

const availableCategories = ref(props.categories || []);

const submitImport = () => {
    if (!form.file || !form.course_id) {
        errorMessage.value = 'Выберите файл и курс';
        return;
    }

    processing.value = true;
    const formData = new FormData();
    formData.append('file', form.file);
    formData.append('course_id', form.course_id);
    formData.append('type', form.type);

    axios.post(route('courses.import.store'), formData)
        .then(() => {
            successMessage.value = 'Импорт успешно завершен';
            errorMessage.value = '';
            form.reset();
        })
        .catch(error => {
            errorMessage.value = error.response?.data?.message || 'Ошибка при импорте';
        })
        .finally(() => {
            processing.value = false;
        });
};

const assignGroups = () => {
    if (!assignForm.course_id || assignForm.group_ids.length === 0) {
        errorMessage.value = 'Выберите курс и хотя бы одну группу';
        return;
    }

    processing.value = true;
    axios.post(route('courses.assign.groups'), assignForm)
        .then(() => {
            successMessage.value = 'Группы успешно назначены';
            errorMessage.value = '';
            assignForm.reset();
        })
        .catch(error => {
            errorMessage.value = error.response?.data?.message || 'Ошибка при назначении';
        })
        .finally(() => {
            processing.value = false;
        });
};
</script>
