<script setup>
import { ref } from 'vue';
import { useForm, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    meetings: Array,
});

const page = usePage();
const selectedMeeting = ref(props.meetings.length > 0 ? props.meetings[0].id : '');
const dniInput = ref(null);

const form = useForm({
    meeting_id: selectedMeeting.value,
    dni: '',
});

const submitDni = () => {
    form.meeting_id = selectedMeeting.value;
    form.post(route('apafa.attendance.dni'), {
        onSuccess: () => {
            form.dni = '';
            if (dniInput.value) {
                dniInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <Head title="Control de Asistencia APAFA" />

    <AppLayout title="Asistencia APAFA">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Control de Asistencia - Reunión APAFA
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <!-- Mensaje de Éxito (Alerta Verde) -->
                    <div v-if="page.props.flash && page.props.flash.success" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-semibold rounded shadow-sm">
                        {{ page.props.flash.success }}
                    </div>

                    <!-- Mensaje de Error Flash si hubiese -->
                    <div v-if="page.props.flash && page.props.flash.error" class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 font-semibold rounded shadow-sm">
                        {{ page.props.flash.error }}
                    </div>

                    <!-- Selección de Reunión -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Seleccionar Reunión Activa</label>
                        <select v-model="selectedMeeting" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option v-for="meeting in meetings" :key="meeting.id" :value="meeting.id">
                                {{ meeting.title }} - ({{ meeting.meeting_date }})
                            </option>
                        </select>
                    </div>

                    <hr class="my-6" />

                    <!-- Formulario de marcado por DNI -->
                    <form @submit.prevent="submitDni" class="space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Ingresar DNI del Padre / Apoderado</label>
                            <input 
                                ref="dniInput"
                                v-model="form.dni" 
                                type="text" 
                                maxlength="8" 
                                placeholder="Escribe los 8 dígitos del DNI..." 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-lg tracking-widest"
                                required
                                autofocus
                            />
                            <span v-if="form.errors.dni" class="text-red-600 text-sm mt-1 block">
                                {{ form.errors.dni }}
                            </span>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md transition duration-150"
                        >
                            Registrar Asistencia
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </AppLayout>
</template>