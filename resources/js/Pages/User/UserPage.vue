<template>
  <h1 class="h1-title">Добро пожаловать, {{ user.fio }} </h1>
  <v-table class="elevation-1 cursor-pointer ">
    <thead>
      <tr>
        <th class="text-left">Даты обучения</th>
        <th class="text-left">Группа</th>
        <th class="text-left">Курс</th>
        <th class="text-left">Специальность</th>
        <th class="text-left">Инструктор</th>
        <th class="text-left">Тип занятия</th>
        <!-- <th class="text-left" v-if="user.role==='Администратор'">Сменить пароль</th> -->
        <th class="text-left">Учебные материалы</th>
        <th class="text-left">Тесты</th>
        <th class="text-left">Действия</th>
      </tr>
    </thead>
    <tbody v-for="(topic, index) in lessons" :key="topic.id">


      <!-- <td>  {{getGroupName(topic.group_id) }} </td> -->
      <td>{{ topic.study_from }}-{{ topic.study_to }}</td>
      <td> {{ getGroupName(topic.group_id) }} </td>
      <td>{{ getCourseName(topic.course_id) }}</td>
      <td>{{ getCategoryName(topic.category_id) }}</td>
      <td>{{ topic.teacher }}</td>
      <td>{{ topic.typeOfLesson }}</td>

      <td> <v-btn color="success" flat :to="{
        name: 'courses.itemmani',
        query: { idEdit: topic.course_id, idCategory: topic.category_id },
      }" target="_blank">Открыть{{ topic.course_id }}</v-btn></td>

      <!-- <td> <v-btn color="success" flat :to="{
        name: 'questions.main',
        query: { idEdit: topic.course_id, idCategory: topic.category_id },
      }" target="_blank">Тесты{{ }}
        </v-btn></td> -->

      <!-- нужно передать category.id и aukstructure.id в questions.item -->
      <td> <v-btn color="success" flat :to="{
        name: 'questions',
        query: { idEdit: topic.course_id, idCategory: topic.category_id },
      }" target="_blank">Тесты {{ topic.id }}
        </v-btn></td>

      <td align="center"> <v-icon small color="green" class="mr-2" @click="
        this.$router.push({
          //name: 'question.edit',
          params: { idEdit: topic.id },
        })
        ">
          mdi-pencil
        </v-icon>
        <v-icon small @click="deleteItem(topic.id)" color="red">
          mdi-delete
        </v-icon>
      </td>


    </tbody>
  </v-table>

  <h1 class="h1-title">Добро пожаловать, {{ user.fio }} </h1>

  <!-- <tr  v-for="chapter in topic.chapters" :key="chapter.chapterId"></tr> -->
  <recursive-table :items="items" :is-root="true" />
</template>

<script>
import $api from "../../api/httpClient";
const apiUrl = import.meta.env.VITE_APP_URL;
import { mapState } from "vuex";
import popup from '../Popup.vue';
import RecursiveTable from './RecursiveTable.vue';
export default {
  name: "UserPage", popup,
  components: { RecursiveTable },




  computed: {
    //...mapState('UserPage', ['courses',]),    
    ...mapState('Course', ['group2learnings', 'courses', 'aircrafts', "categories"]),
    ...mapState('Auth', ['user',]),
    ...mapState('User', ['allGroups', "group"]),

    filterCourse() {
      return this.courses.filter(item => item.role === 'Инструктор')
    },
    getGroup(group_id) {
      return this.allGroups.filter(item => item.id === group_id)
    },

    // getGroupName1 () {
    //   console.log("group_id")
    //   return this.allGroups.filter(item => item.id === this.group_id).groupname
    // },
    aircraftPath() {
      const filteredAircrafts = this.aircrafts.filter(a => a.id === this.aircraftId)
      return filteredAircrafts.length > 0 ? filteredAircrafts[0].path : ''
    },

  },
  async mounted() {
    // получаем id группы залогиненного пользователя из local storage. 
    // Далее делаем запрос данных из таблицы Group2learning с query Param group_id  = 
    // в идеале надо чтобы отдавал бэк сразу то что нужно
    //console.log(this.user.category_id,"this.user.category_id,")
    // if (this.user.group_id)
    //   $api.get(apiUrl + "/api/learning/?group_id=" + this.user.group_id).then((response) => {
    //     this.lessons = response.data;
    //     console.log("+++")
    //   });
    // // если Администратор, то все данные из таблицы обучаемых
    // else if (this.user.role === "Администратор")
    //   $api.get(apiUrl + "/api/learning").then((response) => {
    //     console.log(response.data)
    //     this.lessons = response.data;
    //   });


    // если Администратор, то все данные из таблицы обучаемых
    if (this.user.role === "Администратор")
      $api.get(apiUrl + "/api/learning").then((response) => {
        console.log(response.data)
        this.lessons = response.data;
      });
    else if (this.user.group_id)
      $api.get(apiUrl + "/api/learning/?group_id=" + this.user.group_id).then((response) => {
        this.lessons = response.data;
        console.log("+++")
      });

    else {
      this.$router.push('/500')
    }
    this.$store.dispatch("Course/fetchCourses");
    if (this.aircrafts.length === 0) {
      this.$store.dispatch("Course/fetchAircrafts");
    }
    if (this.allGroups.length === 0) {
      this.$store.dispatch("User/fetchGroups");
    }
    if (this.courses.length === 0) {
      this.$store.dispatch("Course/fetchCategories");
    }

    //------------загружаем chapters с lessons
    $api.get(apiUrl + "/api/lessons").then((response) => {
      this.items = response.data;
    });
  },


  data() {
    return {
      lessons: [],
      //-------Димон------------

      items: [],
      //-------Димон конец-------
      // alert: false,
      //   alertType: "",
      //   overlay: false,      
      //   snackbarText: "", 



    }
  },
  methods: {
    async deleteItem(id) {
      // Логика удаления занятий      
      try {
        await $api.delete(`/api/learning/${id}`)
        await this.getLessons()
      } catch (error) {
        console.log(error)
      }
    },
    //-------------удаляем занятия-----------------
    async deleteLesson(lesson_id) {
      // $api.delete(apiUrl + "/api/learning/"+lesson_id).then((response) => {
      //   this.lessons = response.data;
      // });
      //console.log(course_id, 'текущий курс');
      this.$store
        .dispatch("Course/deleteGroup2learnings", lesson_id)
      this.getLessons();
    },
    //-------------конец удаляем занятия-------------
    async getLessons() {
      $api.get(apiUrl + "/api/learning/")
        //.dispatch("Course/fetchLearining")
        .then((response) => {
          this.lessons = response.data;
        })
        .catch((error) => console.error(error));
    },

    formatDate(date) {
      const options = {
        month: "numeric",
        day: "numeric",
      }
      return `${date.dateChapterStart.toLocaleDateString('ru', options)} - ${date.dateChapterStop.toLocaleDateString('ru', options)}`
    },
    getClassProgress(progress) {
      console.log(progress, 'progress')
      if (progress === 100) return 'green-100 center-text'
      if (progress > 70 && progress < 100) return 'green-70 center-text'
      if (progress > 50 && progress < 70) return 'yellow-50 center-text'
      if (progress && progress < 50) return 'red-50 center-text'
      else return 'center-text'
    },
    getClassScore(score) {
      if (score === 5) return 'green-100 center-text'
      if (score === 4) return 'green-70 center-text'
      if (score === 3) return 'yellow-50 center-text'
      if (score === 2) return 'red-50 center-text'
      else return 'center-text'
    },



    localeDate(date) {
      {
        return new Date(date).toLocaleString('ru', {
          hour: "numeric",
          minute: "numeric",
          year: "numeric",
          month: "long",
          day: "numeric",
        })
      }
    },
    nextLessonCalculate() {
      if (this.courses.length > 0) {
        const tempCourses = Array.from(this.courses)
        const next = tempCourses.sort((a, b) => b.dateStart.getTime() - a.dateStart.getTime())[0]
        const day = next.dateStart.toLocaleDateString()
        const hours = next.dateStart.getHours()
        const minutes = next.dateStart.getMinutes()
        return `Ближайшее занятие состоится ${day} в ${hours} часов ${minutes} минут`
      } else {
        return 'Нет предстоящих занятий'
      }
    },
    // .join() -> array to string, .map() -> property from array, .filter() -> get filtered array

    getGroupName(group_id) {
      return this.allGroups.filter(item => item.id === group_id).map(item => item.groupname).join();
    },
    getCourseName(course_id) {
      return this.courses.filter(item => item.id === course_id).map(item => item.title).join();
    },
    getCategoryName(category_id) {
      return this.categories.filter(item => item.id === category_id).map(item => item.title).join();
    },


    //-------------------------------------------------------------
    flatToHierarchy(flat) {
      const roots = [],
        map = [],
        id = [];
      flat.forEach(item => {
        map.push(Object.assign({}, item)); // копируем
        id.push(item.id);
      });
      let i;
      map.forEach(item => {
        // Не пойму зачем вы вставили сюда null !?
        if ( /*item.parent_id === null ||*/ !item.parent_id || (i = id.indexOf(item.parent_id)) === -1) {
          roots.push(item);
          return;
        }
        if (map[i].children) {
          map[i].children.push(item);
        }
        else {
          map[i].children = [item];
        }
      });
      return roots;
    },

    //-------------------------------------------------------------

  }
}
</script>

<style>
@import "../../../css/app.css";

.center-text {
  text-align: center;
}

.bg-lblue {
  background-color: #a4ccff;
}

.green-100 {
  background-color: #50c950;
}

.green-70 {
  background-color: rgba(143, 215, 139, 0.94);
}

.yellow-50 {
  background-color: rgba(236, 236, 155, 0.87);
}

.red-50 {
  background-color: rgba(236, 201, 155, 0.87);
}
</style>