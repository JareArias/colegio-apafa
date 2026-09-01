<script setup>
import { ref } from 'vue';
import { useForm, Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    meetings: Array,
});

const page = usePage();

// Estado Modal Crear Reunión
const showCreateModal = ref(false);

// Estado Modal Confirmar Cierre / Multa
const showConfirmModal = ref(false);
const selectedMeetingId = ref(null);

// Estado Modal Detalle de Asistentes (NUEVO)
const showAttendeesModal = ref(false);
const selectedMeetingTitle = ref('');
const selectedMeetingAttendances = ref([]);

const form = useForm({
    title: '',
    description: '',
    meeting_date: new Date().toISOString().substr(0, 10),
    start_time: '08:00',
    tolerance_minutes: 15,
    is_active: true,
});

// Métodos Modal Crear
const openCreateModal = () => {
    form.reset();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const submit = () => {
    form.post(route('apafa.meetings.store'), {
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

const toggleStatus = (meetingId) => {
    router.patch(route('apafa.meetings.toggle', meetingId));
};

// Métodos Modal Confirmar Cierre / Multar
const openConfirmModal = (meetingId) => {
    selectedMeetingId.value = meetingId;
    showConfirmModal.value = true;
};

const closeConfirmModal = () => {
    showConfirmModal.value = false;
    selectedMeetingId.value = null;
};

const confirmFinishMeeting = () => {
    if (selectedMeetingId.value) {
        router.post(route('apafa.meetings.finish', selectedMeetingId.value), {}, {
            onSuccess: () => closeConfirmModal(),
        });
    }
};

// Métodos Modal Detalle de Asistentes (NUEVO)
const openAttendeesModal = (meeting) => {
    selectedMeetingTitle.value = meeting.title;
    selectedMeetingAttendances.value = meeting.attendances || [];
    showAttendeesModal.value = true;
};

const closeAttendeesModal = () => {
    showAttendeesModal.value = false;
    selectedMeetingTitle.value = '';
    selectedMeetingAttendances.value = [];
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
                    @click="openCreateModal" 
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
                                <th class="p-4 text-center">Acciones</th>
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
                                    <!-- Botón interactivo para abrir la lista de asistentes -->
                                    <button 
                                        @click="openAttendeesModal(meeting)"
                                        class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold px-3 py-1 rounded-full text-xs transition inline-flex items-center gap-1 cursor-pointer"
                                        title="Ver detalle de asistencias"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ meeting.attendances_count }} Asistentes
                                    </button>
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
                                    <template v-if="meeting.status === 'finished'">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                                            🔒 Finalizada / Multas Generadas
                                        </span>
                                    </template>

                                    <template v-else>
                                        <button 
                                            @click="toggleStatus(meeting.id)"
                                            :class="[
                                                'text-xs font-bold px-3 py-1.5 rounded transition shadow-sm',
                                                meeting.is_active ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                            ]"
                                        >
                                            {{ meeting.is_active ? 'Desactivar' : 'Activar' }}
                                        </button>

                                        <button 
                                            @click="openConfirmModal(meeting.id)"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition shadow"
                                        >
                                            Cerrar / Multar
                                        </button>
                                    </template>
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
        <div v-if="showCreateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50">
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
                        <label class="block text-sm font-medium text-gray-700">Hora de Inicio</label>
                        <input 
                            v-model="form.start_time" 
                            type="time" 
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                            required 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tolerancia (Minutos)</label>
                        <input 
                            v-model="form.tolerance_minutes" 
                            type="number" 
                            min="0" 
                            max="120"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                            placeholder="Ej. 15"
                        />
                        <p class="text-xs text-gray-400 mt-1">Pasado este tiempo, el marcado registrará "Tardanza".</p>
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
                        <button type="button" @click="closeCreateModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700 shadow">
                            Guardar Reunión
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Confirmar Cierre / Generación de Multas -->
        <div v-if="showConfirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-gray-900">¿Finalizar la reunión?</h3>
                <p class="text-sm text-gray-600">
                    Esta acción cambiará el estado de la reunión a finalizada y generará automáticamente las multas para todos los apoderados inasistentes.
                </p>

                <div class="flex justify-end space-x-3 pt-3 border-t">
                    <button 
                        type="button" 
                        @click="closeConfirmModal" 
                        class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="button" 
                        @click="confirmFinishMeeting" 
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow transition"
                    >
                        Sí, cerrar y multar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal para Ver Asistentes (NUEVO) -->
        <div v-if="showAttendeesModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6 space-y-4 max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Lista de Asistencia</h3>
                        <p class="text-xs text-gray-500">{{ selectedMeetingTitle }}</p>
                    </div>
                    <button @click="closeAttendeesModal" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                </div>

                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold sticky top-0">
                            <tr>
                                <th class="p-3">Apoderado</th>
                                <th class="p-3 text-center">Hora de Registro</th>
                                <th class="p-3 text-center">Condición</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="attendance in selectedMeetingAttendances" :key="attendance.id" class="hover:bg-gray-50">
                                <td class="p-3 font-semibold text-gray-800">
                                    {{ attendance.user ? attendance.user.name : (attendance.parent_name ?? 'Apoderado') }}
                                </td>
                                <td class="p-3 text-center font-mono text-xs">
                                    {{ attendance.registered_at ?? attendance.created_at ?? '---' }}
                                </td>
                                <td class="p-3 text-center">
                                    <!-- AQUÍ ESTÁ TU CÓDIGO INTEGRADO -->
                                    <span 
                                        :class="[
                                            'px-2.5 py-1 text-xs font-bold rounded-full',
                                            attendance.status === 'present' 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-amber-100 text-amber-800'
                                        ]"
                                    >
                                        {{ attendance.status === 'present' ? 'Puntual' : 'Tardanza' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="selectedMeetingAttendances.length === 0">
                                <td colspan="3" class="p-6 text-center text-gray-400">
                                    Aún no hay asistencias registradas para esta reunión.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end border-t pt-3">
                    <button @click="closeAttendeesModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>