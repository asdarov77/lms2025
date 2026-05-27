import { createI18n } from 'vue-i18n';

const messages = {
  ru: {
    app: {
      title: 'LMS - Система управления обучением',
      menu: {
        home: 'Главная',
        files: 'Файлы',
        categories: 'Категории',
        courses: 'Курсы',
        classes: 'Классы ВС',
        calendar: 'Календарь',
        users: 'Пользователи',
        groups: 'Группы',
        reguser: 'Регистрация пользователя',
        questions: 'Тестирование',
        questionsBank: 'Банк вопросов',
        settings: 'Настройки',
        profile: 'Профиль',
        logout: 'Выход'
      },
      common: {
        save: 'Сохранить',
        cancel: 'Отмена',
        delete: 'Удалить',
        edit: 'Редактировать',
        add: 'Добавить',
        search: 'Поиск',
        loading: 'Загрузка...',
        noData: 'Нет данных',
        confirm: 'Подтвердить',
        yes: 'Да',
        no: 'Нет'
      },
      validation: {
        required: 'Обязательное поле',
        email: 'Введите корректный email',
        minLength: 'Минимум {length} символов',
        maxLength: 'Максимум {length} символов',
        passwordMismatch: 'Пароли не совпадают'
      },
      errors: {
        unauthorized: 'Необходимо авторизоваться',
        forbidden: 'Доступ запрещен',
        notFound: 'Страница не найдена',
        serverError: 'Ошибка сервера',
        networkError: 'Ошибка сети'
      }
    },
    auth: {
      login: 'Вход',
      register: 'Регистрация',
      logout: 'Выход',
      fio: 'ФИО',
      password: 'Пароль',
      passwordConfirm: 'Подтвердите пароль',
      role: 'Роль',
      group: 'Группа',
      loginSuccess: 'Вход выполнен успешно',
      loginError: 'Неверный логин или пароль',
      registerSuccess: 'Пользователь успешно зарегистрирован'
    },
    courses: {
      title: 'Курсы',
      aircraft: 'Класс ВС',
      category: 'Категория',
      description: 'Описание',
      startLearning: 'Начать обучение',
      startTest: 'Начать тест',
      noCourses: 'Нет доступных курсов'
    },
    tests: {
      title: 'Тестирование',
      question: 'Вопрос',
      answers: 'Ответы',
      next: 'Следующий вопрос',
      finish: 'Завершить тест',
      results: 'Результаты',
      score: 'Баллы',
      grade: 'Оценка',
      correctAnswer: 'Правильный ответ',
      wrongAnswer: 'Неправильный ответ'
    },
    users: {
      title: 'Пользователи',
      fio: 'ФИО',
      role: 'Роль',
      group: 'Группа',
      permissions: 'Разрешения',
      phone: 'Телефон',
      organization: 'Организация',
      position: 'Должность'
    },
    groups: {
      title: 'Группы',
      name: 'Название группы',
      description: 'Описание',
      students: 'Обучаемые',
      teacher: 'Инструктор',
      lessonType: 'Вид занятия',
      studyFrom: 'Дата начала',
      studyTo: 'Дата окончания',
      enroll: 'Записать на курсы'
    }
  },
  en: {
    app: {
      title: 'LMS - Learning Management System',
      menu: {
        home: 'Home',
        files: 'Files',
        categories: 'Categories',
        courses: 'Courses',
        classes: 'Aircraft Classes',
        calendar: 'Calendar',
        users: 'Users',
        groups: 'Groups',
        reguser: 'Register User',
        questions: 'Testing',
        questionsBank: 'Question Bank',
        settings: 'Settings',
        profile: 'Profile',
        logout: 'Logout'
      },
      common: {
        save: 'Save',
        cancel: 'Cancel',
        delete: 'Delete',
        edit: 'Edit',
        add: 'Add',
        search: 'Search',
        loading: 'Loading...',
        noData: 'No data',
        confirm: 'Confirm',
        yes: 'Yes',
        no: 'No'
      },
      validation: {
        required: 'Required field',
        email: 'Enter valid email',
        minLength: 'Minimum {length} characters',
        maxLength: 'Maximum {length} characters',
        passwordMismatch: 'Passwords do not match'
      },
      errors: {
        unauthorized: 'Authentication required',
        forbidden: 'Access denied',
        notFound: 'Page not found',
        serverError: 'Server error',
        networkError: 'Network error'
      }
    },
    auth: {
      login: 'Login',
      register: 'Register',
      logout: 'Logout',
      fio: 'Full Name',
      password: 'Password',
      passwordConfirm: 'Confirm Password',
      role: 'Role',
      group: 'Group',
      loginSuccess: 'Login successful',
      loginError: 'Invalid login or password',
      registerSuccess: 'User registered successfully'
    },
    courses: {
      title: 'Courses',
      aircraft: 'Aircraft Class',
      category: 'Category',
      description: 'Description',
      startLearning: 'Start Learning',
      startTest: 'Start Test',
      noCourses: 'No available courses'
    },
    tests: {
      title: 'Testing',
      question: 'Question',
      answers: 'Answers',
      next: 'Next Question',
      finish: 'Finish Test',
      results: 'Results',
      score: 'Score',
      grade: 'Grade',
      correctAnswer: 'Correct Answer',
      wrongAnswer: 'Wrong Answer'
    },
    users: {
      title: 'Users',
      fio: 'Full Name',
      role: 'Role',
      group: 'Group',
      permissions: 'Permissions',
      phone: 'Phone',
      organization: 'Organization',
      position: 'Position'
    },
    groups: {
      title: 'Groups',
      name: 'Group Name',
      description: 'Description',
      students: 'Students',
      teacher: 'Teacher',
      lessonType: 'Lesson Type',
      studyFrom: 'Start Date',
      studyTo: 'End Date',
      enroll: 'Enroll in Courses'
    }
  }
};

export const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'ru',
  fallbackLocale: 'ru',
  messages
});

export function createI18n() {
  return i18n;
}

export default i18n;
