<script setup>
import { ref } from 'vue';
import { useForm, Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    meetings: Array,
});

const page = usePage();
const showModal = ref(false);

const form = useForm({
    title: '',
    description: '',
    meeting_date: new Date().toISOString().substr(0, 10),
    is_active: true,
});

const openModal = () => {
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submit = () => {
    form.post(route('apafa.meetings.store'), {
        onSuccess: () => {
            closeModal();
        },
    });
};

const toggleStatus = (meetingId) => {
    router.patch(route('apafa.meetings.toggle', meetingId));
};
</script>

<template>
    <Head title="Gestión de Reuniones - APAFA" />

    <AppLayout title="Gestión de Reuniones">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestión de Reuniones APAFA
                </h2>
                <button 
                    @click="openModal" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow transition text-sm"
                >
                    + Nueva Reunión
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Alerta Flash -->
                <div v-if="page.props.flash && page.props.flash.success" class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-semibold rounded shadow-sm">
                    {{ page.props.flash.success }}
                </div>

                <!-- Tabla de Reuniones -->
                <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                            <tr>
                                <th class="p-4">Título de la Reunión</th>
                                <th class="p-4">Fecha</th>
                                <th class="p-4 text-center">Asistentes</th>
                                <th class="p-4 text-center">Estado</th>
                                <th class="p-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="meeting in meetings" :key="meeting.id" class="hover:bg-gray-50">
                                <td class="p-4 font-semibold text-gray-800">
                                    {{ meeting.title }}
                                    <span v-if="meeting.description" class="block text-xs font-normal text-gray-400 mt-1">
                                        {{ meeting.description }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono">{{ meeting.meeting_date }}</td>
                                <td class="p-4 text-center">
                                    <span class="bg-indigo-100 text-indigo-800 font-bold px-2.5 py-1 rounded-full text-xs">
                                        {{ meeting.attendances_count }} Asistentes
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span 
                                        :class="[
                                            'px-3 py-1 text-xs font-bold rounded-full',
                                            meeting.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'
                                        ]"
                                    >
                                        {{ meeting.is_active ? 'ACTIVA' : 'INACTIVA' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button 
                                        @click="toggleStatus(meeting.id)"
                                        :class="[
                                            'text-xs font-bold px-3 py-1.5 rounded transition shadow-sm',
                                            meeting.is_active ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                        ]"
                                    >
                                        {{ meeting.is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="meetings.length === 0">
                                <td colspan="5" class="p-6 text-center text-gray-400">
                                    No hay reuniones registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Modal para Crear Reunión -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Crear Nueva Reunión APAFA</h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Título / Nombre de Asamblea</label>
                        <input v-model="form.title" type="text" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Ej. Asamblea General Ordinaria" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha del Evento</label>
                        <input v-model="form.meeting_date" type="date" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                        <textarea v-model="form.description" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="2" placeholder="Detalles de la agenda..."></textarea>
                    </div>

                    <div class="flex items-center">
                        <input v-model="form.is_active" id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 text-sm text-gray-700 font-medium">Establecer como reunión activa actual</label>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700 shadow">
                            Guardar Reunión
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AppLayout>
</template>