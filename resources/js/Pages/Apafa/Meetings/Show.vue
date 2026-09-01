<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    meeting: Object,
});

const finishMeeting = (meetingId) => {
    if (confirm('¿Estás seguro de finalizar la reunión? Se generarán las multas automáticamente a los padres inasistentes.')) {
        router.post(route('apafa.meetings.finish', meetingId));
    }
};
</script>

<template>
    <!-- Botón para finalizar -->
    <button 
        v-if="meeting.status !== 'finished'"
        @click="finishMeeting(meeting.id)"
        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition"
    >
        Cerrar Reunión y Generar Multas
    </button>
    <span v-else class="px-3 py-1 bg-gray-200 text-gray-700 text-sm font-semibold rounded-full">
        Reunión Finalizada
    </span>
</template>