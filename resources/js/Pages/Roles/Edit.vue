<template>
    <AppLayout title="Редактирование роли">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-semibold mb-6">Редактирование роли: {{ role.name }}</h2>

                        <form @submit.prevent="updateRole" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Название роли</label>
                                <input type="text" id="name" v-model="form.name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Разрешения</label>
                                <div class="border rounded-md p-4 max-h-96 overflow-y-auto">
                                    <div v-for="perm in permissions" :key="perm.id" class="flex items-center mb-2">
                                        <input type="checkbox" :id="'perm-' + perm.id" :value="perm.id" v-model="form.permissions"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"/>
                                        <label :for="'perm-' + perm.id" class="ml-3 block text-sm text-gray-700">
                                            {{ perm.name }} <span class="text-gray-500 text-xs">({{ perm.description || '' }})</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4">
                                <Link :href="route('roles.index')" class="text-gray-600 hover:text-gray-900">Отмена</Link>
                                <button type="submit" :disabled="processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Сохранить
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    role: Object,
    permissions: Array,
    rolePermissions: Array
});

const form = useForm({
    name: props.role.name,
    permissions: props.rolePermissions || []
});

const updateRole = () => {
    form.put(route('roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect handled by Inertia
        }
    });
};
</script>
