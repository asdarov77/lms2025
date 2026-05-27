<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCourseStore } from '@/stores/course.store'
import { useAuthStore } from '@/stores/auth.store'
import $api from '@/services/api.service'
import Treeselect from 'vue3-treeselect'
import 'vue3-treeselect/dist/vue3-treeselect.css'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const router = useRouter()
const courseStore = useCourseStore()
const authStore = useAuthStore()
const { t } = useI18n()

const props = defineProps({
  idEdit: { type: Number, required: true }
})

const group = ref({
  aircrafts: null,
  categories: null,
  courses: [],
  teacher: null,
  typeOfLesson: null,
  study_from: new Date().toISOString().substr(0, 10),
  study_to: ''
})

const errors = ref([])
const isLoading = ref(false)
const alert = ref(false)
const alertType = ref('')
const snackbarText = ref('')

// Фильтры
const catFilter = ref([])
const courseFilter = ref([])
const options = ref([]) // для treeselect

// Вычисляемые
const aircrafts = computed(() => courseStore.aircrafts)
const categories = computed(() => courseStore.categories)
const users = computed(() => authStore.users)

const filteredUserRole = computed(() => 
  users.value.filter(u => u.role === 'Инструктор')
)

onMounted(async () => {
  isLoading.value = true
  try {
    await Promise.all([
      courseStore.fetchAircrafts(),
      courseStore.fetchCategories(),
      courseStore.fetchCourses(),
      authStore.fetchUsers(),
      courseStore.fetchGroup(props.idEdit)
    ])
    // Копируем данные группы
    if (courseStore.group) {
      group.value = { ...courseStore.group }
      // Устанавливаем study_from по умолчанию сегодня
      group.value.study_from = new Date().toISOString().substr(0, 10)
    }
  } catch (error) {
    console.error(error)
    showAlert(t('errors.loadFailed'), 'error')
  } finally {
    isLoading.value = false
  }
})

// Методы
const changeAir = (aircraftId) => {
  // Очищаем treeselect
  group.value.courses = []
  group.value.categories = null
  courseFilter.value = []
  
  // Фильтруем категории по aircraft
  catFilter.value = categories.value.filter(c => c.aircraft_id === aircraftId)
}

const changeCat = async (categoryId) => {
  group.value.courses = []
  courseFilter.value = []
  options.value = []
  
  if (!categoryId) return
  
  try {
    const response = await $api.get(`/api/course`, {
      params: {
        aircraft_id: group.value.aircrafts,
        category_id: categoryId
      }
    })
    
    courseFilter.value = response.data
    
    // Преобразуем плоскую структуру в иерархическую для treeselect
    const coursesWithHierarchy = courseFilter.value.map(course => {
      if (course.aukstructures) {
        const hierarchy = flatToHierarchy(course.aukstructures)
        // Переименовываем поля для treeselect (title -> label)
        const renamed = renameFieldsRecursive(hierarchy, 'title', 'label')
        return {
          id: course.id,
          label: course.title,
          children: renamed
        }
      }
      return {
        id: course.id,
        label: course.title
      }
    })
    
    options.value = coursesWithHierarchy
  } catch (error) {
    console.error(error)
    showAlert(t('errors.loadFailed'), 'error')
  }
}

// Преобразование плоского массива в дерево
const flatToHierarchy = (flat) => {
  const roots = []
  const map = {}
  const ids = []
  
  flat.forEach(item => {
    map[item.id] = { ...item }
    ids.push(item.id)
  })
  
  flat.forEach(item => {
    if (!item.parent_id || !ids.includes(item.parent_id)) {
      roots.push(map[item.id])
      return
    }
    
    if (map[item.parent_id].children) {
      map[item.parent_id].children.push(map[item.id])
    } else {
      map[item.parent_id].children = [map[item.id]]
    }
  })
  
  return roots
}

// Рекурсивное переименование полей
const renameFieldsRecursive = (obj, oldName, newName) => {
  if (typeof obj !== 'object' || obj === null) return obj
  
  const newObj = {}
  for (const [key, value] of Object.entries(obj)) {
    if (key === oldName) {
      newObj[newName] = value
    } else if (key === 'children' && Array.isArray(value)) {
      newObj.children = value.map(child => renameFieldsRecursive(child, oldName, newName))
    } else {
      newObj[key] = renameFieldsRecursive(value, oldName, newName)
    }
  }
  return newObj
}

// Валидация дат
const validateDates = () => {
  errors.value = []
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  const studyFrom = new Date(group.value.study_from)
  const studyTo = new Date(group.value.study_to)
  
  if (!group.value.study_from) {
    errors.value.push(t('validation.dateFromRequired'))
    return false
  }
  
  if (!group.value.study_to) {
    errors.value.push(t('validation.dateToRequired'))
    return false
  }
  
  if (studyFrom < today) {
    errors.value.push(t('validation.dateFromPast'))
    return false
  }
  
  if (studyFrom > studyTo) {
    errors.value.push(t('validation.dateFromAfterTo'))
    return false
  }
  
  return true
}

const submitForm = async () => {
  if (!validateDates()) {
    showAlert(errors.value[0], 'error')
    return
  }
  
  const formData = {
    course_id: group.value.courses,
    group_id: props.idEdit,
    category_id: group.value.categories,
    study_from: group.value.study_from,
    study_to: group.value.study_to,
    teacher: group.value.teacher,
    typeOfLesson: group.value.typeOfLesson
  }
  
  try {
    await courseStore.enrollGroup(formData)
    router.push('/groups/list')
  } catch (error) {
    console.error(error)
    showAlert(t('errors.saveFailed'), 'error')
  }
}

const cancelBtn = () => {
  router.go(-1)
}

const showAlert = (text, type) => {
  snackbarText.value = text
  alertType.value = type
  alert.value = true
}

const closeAlert = () => {
  alert.value = false
}
</script>

<template>
  <v-card class="elevation-12 mx-auto" style="width: 600px; overflow: visible">
    <v-toolbar color="primary">
      <v-toolbar-title>{{ t('group.enrollmentTitle') }}</v-toolbar-title>
    </v-toolbar>
    
    <v-card-text>
      <v-form @submit.prevent="submitForm">
        <!-- Класс ВС -->
        <v-select
          :label="t('group.aircraft')"
          :items="aircrafts"
          v-model="group.aircrafts"
          item-value="id"
          item-title="path"
          clearable
          @update:model-value="changeAir"
        />
        
        <!-- Категория -->
        <v-select
          :label="t('group.category')"
          :items="catFilter"
          v-model="group.categories"
          item-value="id"
          item-title="title"
          clearable
          @update:model-value="changeCat"
        />
        
        <v-divider class="my-4" />
        
        <!-- Курсы (дерево) -->
        <Treeselect
          :placeholder="t('group.courses')"
          :default-expand-level="1"
          v-model="group.courses"
          :multiple="true"
          :options="options"
          class="mb-4"
        />
        
        <!-- Инструктор -->
        <v-select
          :label="t('group.instructor')"
          :items="filteredUserRole"
          v-model="group.teacher"
          item-title="fio"
        />
        
        <!-- Вид занятия -->
        <v-select
          :label="t('group.lessonType')"
          :items="[
            { id: 1, title: t('lessonTypes.lecture') },
            { id: 2, title: t('lessonTypes.practice') },
            { id: 3, title: t('lessonTypes.self') }
          ]"
          v-model="group.typeOfLesson"
          item-value="id"
          item-title="title"
        />
        
        <v-divider class="my-4" />
        
        <!-- Даты -->
        <v-row>
          <v-col cols="6">
            <v-text-field
              :label="t('group.dateFrom')"
              type="date"
              v-model="group.study_from"
              variant="outlined"
            />
          </v-col>
          <v-col cols="6">
            <v-text-field
              :label="t('group.dateTo')"
              type="date"
              v-model="group.study_to"
              variant="outlined"
            />
          </v-col>
        </v-row>
        
        <!-- Ошибки -->
        <v-alert
          v-if="errors.length"
          type="error"
          class="mt-4"
          density="compact"
        >
          <ul class="mb-0 pl-4">
            <li v-for="(error, idx) in errors" :key="idx">{{ error }}</li>
          </ul>
        </v-alert>
      </v-form>
    </v-card-text>
    
    <v-card-actions>
      <v-spacer />
      <v-btn color="error" variant="text" @click="cancelBtn">
        {{ t('common.cancel') }}
      </v-btn>
      <v-btn color="primary" @click="submitForm">
        {{ t('common.save') }}
      </v-btn>
    </v-card-actions>
  </v-card>
  
  <!-- Snackbar -->
  <v-snackbar
    v-model="alert"
    :color="alertType"
    timeout="3000"
  >
    {{ snackbarText }}
    <template #actions>
      <v-btn icon size="small" @click="closeAlert">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </template>
  </v-snackbar>
</template>

<style scoped>
:deep(.vue-treeselect__control) {
  height: 56px;
  border-radius: 4px;
  background: #f5f5f5;
}

:deep(.vue-treeselect__placeholder),
:deep(.vue-treeselect__single-value) {
  padding-left: 12px;
  line-height: 56px;
  color: #848484;
}

:deep(.vue-treeselect__multi-value-item) {
  cursor: pointer;
  color: #7a7a7a;
  background-color: #f2efef;
}
</style>
