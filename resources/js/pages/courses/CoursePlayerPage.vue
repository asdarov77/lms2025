<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useCourseStore } from '@/stores/course.store'
import { useAuthStore } from '@/stores/auth.store'
import $api from '@/services/api.service'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const courseStore = useCourseStore()
const authStore = useAuthStore()
const { t } = useI18n()

// Состояние
const isLoading = ref(false)
const drawer = ref(true)
const showItems = ref(true)
const showSearch = ref(false)
const isFavorite = ref(false)
const searchTerm = ref('')
const matchingFiles = ref([])
const favorites = ref([])
const highlighted = ref([])
const currentHighlight = ref(0)
const activeId = ref(null)

// Данные курса
const aukstructures = ref([])
const filterByCategoryAukstructures = ref([])
const link = ref('')
const titleauk = ref('')
const aircraftCode = ref('')
const coursePath = ref('')

// Iframe ref
const iframeRef = ref(null)
const iframeHeight = ref('auto')

// Вычисляемые
const courseId = computed(() => parseInt(route.params.id))
const categoryId = computed(() => parseInt(route.params.categoryId))

const categoryCode = computed(() => {
  return courseStore.category?.code || null
})

const apiUrl = import.meta.env.VITE_API_URL || '/api'

onMounted(async () => {
  isLoading.value = true
  
  try {
    // Загружаем данные
    await Promise.all([
      courseStore.fetchAircrafts(),
      courseStore.fetchCourse(courseId.value),
      courseStore.fetchCategory(categoryId.value),
      courseStore.fetchCategories()
    ])
    
    // Загружаем структуру курса
    const response = await $api.get(`${apiUrl}/course`, {
      params: {
        course_id: courseId.value,
        category_id: categoryId.value
      }
    })
    
    if (response.data && response.data[0]) {
      const courseData = response.data[0]
      titleauk.value = courseData.title
      aukstructures.value = courseData.aukstructures || []
      aircraftCode.value = courseData.aircraft_id
      coursePath.value = courseData.path
      
      // Фильтруем по категории
      filterByCategoryAukstructures.value = aukstructures.value.filter(auk => {
        return auk.categories 
          ? auk.categories.includes(categoryCode.value?.toString().trim()) 
          : true
      }).sort((a, b) => a.id - b.id)
      
      // Находим первый модуль (type === 3) и загружаем его
      const firstModule = aukstructures.value.find(item => item.type === 3)
      if (firstModule) {
        loadModule(firstModule.id)
      }
    }
    
    // Загружаем избранное
    await loadFavorites()
    
  } catch (error) {
    console.error('Error loading course:', error)
  } finally {
    isLoading.value = false
  }
})

onBeforeUnmount(() => {
  // Очистка слушателей
  if (iframeRef.value) {
    iframeRef.value.removeEventListener('load', handleIframeLoad)
  }
})

// Методы навигации
const loadModule = async (itemId) => {
  if (!itemId) return
  
  activeId.value = itemId
  isLoading.value = true
  
  try {
    const response = await $api.get(`${apiUrl}/getlink/${itemId}`)
    link.value = response.data
    
    // Сброс поиска при загрузке нового модуля
    matchingFiles.value = []
    highlighted.value = []
    currentHighlight.value = 0
  } catch (error) {
    console.error('Error loading module:', error)
  } finally {
    isLoading.value = false
  }
}

const handleIframeLoad = () => {
  if (!iframeRef.value) return
  
  try {
    const iframeDoc = iframeRef.value.contentDocument || iframeRef.value.contentWindow.document
    
    // Подстройка высоты
    const height = iframeDoc.body.scrollHeight + 20
    iframeHeight.value = `${height}px`
    
    // Если есть подсветка от поиска - применяем
    if (matchingFiles.value.length > 0 && highlighted.value.length === 0) {
      applySearchHighlights(iframeDoc)
    }
  } catch (e) {
    console.warn('Cross-origin restriction or iframe not ready:', e)
  }
}

// Поиск
const search = async () => {
  if (searchTerm.value.length < 3) {
    alert(t('search.minThreeChars'))
    return
  }
  
  isLoading.value = true
  
  try {
    const response = await $api.post(`${apiUrl}/search-files/`, {
      query: searchTerm.value,
      path: coursePath.value,
      aircraft: aircraftCode.value
    })
    
    matchingFiles.value = response.data || []
    
    if (matchingFiles.value.length > 0) {
      // Загружаем первый результат
      const firstResult = matchingFiles.value[0]
      await loadModule(firstResult.itemId)
      
      // Сохраняем данные для подсветки
      highlighted.value = firstResult.highlightedNodes || []
    } else {
      alert(t('search.noResults'))
    }
  } catch (error) {
    console.error('Search error:', error)
    alert(t('search.error'))
  } finally {
    isLoading.value = false
  }
}

const applySearchHighlights = (doc) => {
  if (!doc || !highlighted.value.length) return
  
  try {
    highlighted.value.forEach((hl, index) => {
      if (hl.originalXpath) {
        try {
          const result = doc.evaluate(
            hl.originalXpath,
            doc,
            null,
            XPathResult.FIRST_ORDERED_NODE_TYPE,
            null
          )
          const node = result.singleNodeValue
          
          if (node && node.parentNode) {
            const span = doc.createElement('span')
            span.className = 'highlighted'
            span.style.backgroundColor = 'yellow'
            span.style.display = 'inline-block'
            span.textContent = node.textContent
            
            node.parentNode.replaceChild(span, node)
          }
        } catch (e) {
          console.warn('XPath evaluation failed:', e)
        }
      }
    })
    
    // Скролл к первому элементу
    scrollToHighlight(0)
  } catch (e) {
    console.error('Error applying highlights:', e)
  }
}

// Навигация по результатам поиска
const scrollToNext = () => {
  if (highlighted.value.length === 0) return
  
  currentHighlight.value = (currentHighlight.value + 1) % highlighted.value.length
  scrollToHighlight(currentHighlight.value)
}

const scrollToPrev = () => {
  if (highlighted.value.length === 0) return
  
  currentHighlight.value = (currentHighlight.value - 1 + highlighted.value.length) % highlighted.value.length
  scrollToHighlight(currentHighlight.value)
}

const scrollToHighlight = (index) => {
  if (!iframeRef.value) return
  
  try {
    const iframeDoc = iframeRef.value.contentDocument || iframeRef.value.contentWindow.document
    const highlightedElements = iframeDoc.querySelectorAll('.highlighted')
    
    if (highlightedElements[index]) {
      const element = highlightedElements[index]
      const iframeRect = iframeRef.value.getBoundingClientRect()
      const elementRect = element.getBoundingClientRect()
      
      const offsetTop = elementRect.top - iframeRect.top + iframeDoc.documentElement.scrollTop - 50
      
      iframeDoc.documentElement.scrollTo({
        top: offsetTop,
        behavior: 'smooth'
      })
    }
  } catch (e) {
    console.warn('Scroll error:', e)
  }
}

// Избранное
const loadFavorites = async () => {
  try {
    const response = await $api.get(`${apiUrl}/favorites/`)
    favorites.value = response.data.favorites || []
  } catch (error) {
    console.error('Error loading favorites:', error)
  }
}

const toggleFavorite = async () => {
  isFavorite.value = !isFavorite.value
  showItems.value = isFavorite.value
  showSearch.value = false
}

const addToFavorites = async () => {
  if (!activeId.value) return
  
  const activeItem = aukstructures.value.find(item => item.id === activeId.value)
  if (!activeItem) return
  
  try {
    await $api.post(`${apiUrl}/favorites/add`, {
      course_id: activeId.value,
      title: activeItem.title
    })
    await loadFavorites()
  } catch (error) {
    console.error('Error adding to favorites:', error)
  }
}

const removeFavorite = async (id) => {
  try {
    await $api.delete(`${apiUrl}/favorites/${id}`)
    await loadFavorites()
  } catch (error) {
    console.error('Error removing favorite:', error)
  }
}

// UI методы
const toggleList = () => {
  showItems.value = !showItems.value
  isFavorite.value = false
  showSearch.value = false
}

const toggleSearch = () => {
  showSearch.value = !showSearch.value
  isFavorite.value = false
  
  if (!showSearch.value) {
    showItems.value = true
  }
}

const showthumb = (itemId) => {
  const el = document.getElementById(itemId)
  if (el) {
    el.style.border = '2px dotted grey'
    el.style.borderRadius = '4px'
    if (itemId !== activeId.value) {
      el.style.background = '#D3D3D3'
    }
    el.style.transform = 'scale(1.03)'
  }
}

const hidethumb = (itemId) => {
  const el = document.getElementById(itemId)
  if (el) {
    el.style.border = 'none'
    if (itemId !== activeId.value) {
      el.style.background = 'none'
    }
    el.style.transform = 'scale(1.0)'
  }
}

// Вычисление стиля для элемента меню
const getItemStyle = (item) => {
  const baseStyle = {
    cursor: item.type === 3 ? 'pointer' : 'default',
    opacity: item.type !== 3 ? 0.7 : 1,
    color: item.type !== 3 ? 'green' : 'inherit',
    fontSize: `${-5 * item.type + 30}px`,
    paddingLeft: `${(item.type - 1) * 10}px`,
    display: 'inline-block',
    wordWrap: 'break-word'
  }
  
  if (item.id === activeId.value) {
    baseStyle.background = 'lightgreen'
    baseStyle.fontWeight = 'bold'
  }
  
  return baseStyle
}
</script>

<template>
  <div class="course-player h-100">
    <!-- Progress bar -->
    <v-progress-linear
      v-if="isLoading"
      color="primary"
      indeterminate
      location="top"
    />
    
    <v-card class="h-100" flat>
      <v-row no-gutters class="h-100">
        <!-- Левое меню -->
        <v-col cols="3" class="border-e">
          <v-sheet class="h-100" style="overflow-y: auto; background: #f5f5f5;">
            <!-- Заголовок -->
            <v-sheet class="pa-4 mb-2" elevation="2" rounded="lg">
              <div class="text-h6 text-center">{{ titleauk.toUpperCase() }}</div>
            </v-sheet>
            
            <!-- Кнопки управления -->
            <v-row no-gutters align="center" class="mb-4">
              <v-col cols="1" />
              <v-col cols="5" class="d-flex align-center ga-2">
                <v-icon
                  size="x-large"
                  :color="showItems ? 'green' : ''"
                  class="cursor-pointer"
                  @click="toggleList"
                >
                  {{ showItems ? 'mdi-view-list' : 'mdi-view-list-outline' }}
                </v-icon>
                
                <v-icon
                  size="x-large"
                  :color="isFavorite ? 'red' : ''"
                  class="cursor-pointer"
                  @click="toggleFavorite"
                >
                  {{ isFavorite ? 'mdi-heart' : 'mdi-heart-outline' }}
                </v-icon>
                
                <v-icon
                  size="x-large"
                  :color="showSearch ? 'green' : ''"
                  class="cursor-pointer"
                  @click="toggleSearch"
                >
                  {{ showSearch ? 'mdi-magnify-minus-outline' : 'mdi-magnify' }}
                </v-icon>
              </v-col>
              
              <v-col cols="5" />
              <v-col cols="1">
                <v-icon
                  size="large"
                  color="green"
                  class="cursor-pointer"
                  @click="addToFavorites"
                >
                  mdi-playlist-star
                </v-icon>
              </v-col>
            </v-row>
            
            <!-- Избранное -->
            <div v-if="isFavorite" class="px-4 pb-4">
              <v-list density="compact">
                <v-list-item
                  v-for="fav in favorites"
                  :key="fav.id"
                  :title="fav.title"
                >
                  <template #append>
                    <v-icon
                      size="small"
                      color="error"
                      @click.stop="removeFavorite(fav.course_id)"
                    >
                      mdi-close
                    </v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </div>
            
            <!-- Поиск -->
            <div v-if="showSearch" class="px-4 pb-4">
              <v-text-field
                v-model="searchTerm"
                :label="t('search.label')"
                density="compact"
                variant="outlined"
                rounded
                append-inner-icon="mdi-magnify"
                clearable
                single-line
                @click:append-inner="search"
                @keyup.enter="search"
              />
              
              <!-- Результаты поиска -->
              <div v-if="matchingFiles.length > 0" class="mt-2">
                <div class="text-caption mb-2">
                  {{ t('search.totalFound', { count: matchingFiles.length }) }}
                  
                  <v-btn-group size="x-small" class="ml-2">
                    <v-btn @click="scrollToPrev">▲</v-btn>
                    <v-btn @click="scrollToNext">▼</v-btn>
                  </v-btn-group>
                </div>
                
                <v-divider class="mb-2" />
                
                <v-list density="compact">
                  <v-list-item
                    v-for="result in matchingFiles"
                    :key="result.file"
                    :title="result.title"
                    @click="loadModule(result.itemId)"
                    class="text-truncate"
                    style="max-width: 100%; overflow: hidden;"
                  />
                </v-list>
              </div>
              <div v-else-if="searchTerm.length >= 3" class="text-caption text-medium-emphasis mt-2">
                {{ t('search.noResults') }}
              </div>
            </div>
            
            <!-- Структура курса -->
            <div v-if="showItems" class="px-4 pb-4">
              <div
                v-for="(item, index) in aukstructures"
                :key="item.id"
                :id="item.id"
                :style="getItemStyle(item)"
                class="mt-2"
                @click="item.type === 3 ? loadModule(item.id) : null"
                @mouseover="item.type === 3 ? showthumb(item.id) : null"
                @mouseleave="item.type === 3 ? hidethumb(item.id) : null"
              >
                {{ item.title }}
              </div>
            </div>
          </v-sheet>
        </v-col>
        
        <!-- Правая часть - iframe -->
        <v-col cols="9">
          <v-sheet class="h-100 pa-4" style="overflow: auto; background: white;">
            <iframe
              ref="iframeRef"
              :src="link"
              width="100%"
              :style="{ height: iframeHeight, border: 'none' }"
              @load="handleIframeLoad"
              name="contentFrame"
            />
          </v-sheet>
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>

<style scoped>
.h-100 {
  height: calc(100vh - 120px);
}

.border-e {
  border-right: 1px solid rgba(0, 0, 0, 0.12);
}

.cursor-pointer {
  cursor: pointer;
}

.highlighted {
  background-color: yellow !important;
  display: inline-block;
  padding: 2px 4px;
  border-radius: 2px;
}

:deep(.v-list-item-title) {
  font-size: 0.875rem;
}
</style>
