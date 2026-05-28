<template>
    <AppLayout title="Управление ролями">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Роли и разрешения</h2>
                    <Link :href="route('roles.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Создать роль
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Разрешения</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="role in roles" :key="role.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ role.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-for="(perm, index) in role.permissions" :key="perm.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-2 mb-2">
                                                {{ perm.name }}
                                            </span>
                                            <span v-if="role.permissions.length === 0" class="text-gray-400 italic">Нет разрешений</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('roles.edit', role.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Редактировать</Link>
                                            <button @click="deleteRole(role.id)" class="text-red-600 hover:text-red-900">Удалить</button>
                                        </td>
                                    </tr>
                                    <tr v-if="roles.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Роли не найдены</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    roles: Array
});

const deleteRole = (id) => {
    if (confirm('Вы уверены, что хотите удалить эту роль?')) {
        axios.delete(route('roles.destroy', id))
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                alert('Ошибка при удалении: ' + (error.response?.data?.message || error.message));
            });
    }
};
</script>
