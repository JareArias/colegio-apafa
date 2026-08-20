<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    activeMeeting: { type: Object, default: null },
    totalParents: { type: Number, default: 0 },
    totalAttendees: { type: Number, default: 0 },
    absentParents: { type: Number, default: 0 },
    quorumPercentage: { type: Number, default: 0 },
    recentAttendances: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Dashboard APAFA" />

    <AppLayout title="Panel Principal">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Panel General - Control APAFA
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Alerta de Reunión Activa -->
                <div v-if="activeMeeting" class="bg-indigo-900 text-white p-6 rounded-2xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <span class="bg-emerald-500 text-emerald-950 font-black text-xs px-3 py-1 rounded-full uppercase tracking-wider">Reunión Activa Hoy</span>
                        <h3 class="text-2xl font-black mt-2">{{ activeMeeting.title }}</h3>
                        <p class="text-indigo-200 text-sm mt-1">Fecha programada: {{ activeMeeting.meeting_date }}</p>
                    </div>
                    <Link :href="route('apafa.attendance.index')" class="bg-white text-indigo-900 font-bold px-5 py-2.5 rounded-xl hover:bg-indigo-50 transition text-sm shadow">
                        Ir al Escáner de Asistencia →
                    </Link>
                </div>

                <div v-else class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg text-amber-800">
                    <p class="font-bold">No hay ninguna reunión activa seleccionada.</p>
                    <p class="text-sm">Ve al menú de <strong>Gestión de Reuniones</strong> para activar una asamblea.</p>
                </div>

                <!-- Métricas Clave (Tarjetas) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase">Padres Registrados</p>
                        <p class="text-3xl font-black text-gray-800 mt-2">{{ totalParents }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <p class="text-xs font-bold text-emerald-600 uppercase">Asistentes Marcados</p>
                        <p class="text-3xl font-black text-emerald-600 mt-2">{{ totalAttendees }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <p class="text-xs font-bold text-rose-500 uppercase">Pendientes / Ausentes</p>
                        <p class="text-3xl font-black text-rose-500 mt-2">{{ absentParents }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <p class="text-xs font-bold text-indigo-600 uppercase">% Quórum Alcanzado</p>
                        <p class="text-3xl font-black text-indigo-600 mt-2">{{ quorumPercentage }}%</p>
                    </div>

                </div>

                <!-- Barra de Progreso del Quórum -->
                <div v-if="activeMeeting" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2">
                    <div class="flex justify-between text-sm font-bold">
                        <span class="text-gray-700">Progreso de Asistencia (Quórum)</span>
                        <span class="text-indigo-600">{{ totalAttendees }} de {{ totalParents }} Padres</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500" :style="{ width: quorumPercentage + '%' }"></div>
                    </div>
                </div>

                <!-- Tabla de Marcaciones Recientes -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4">Últimas Asistencias Registradas</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="p-3">Padre / Apoderado</th>
                                    <th class="p-3">DNI</th>
                                    <th class="p-3">Hora de Ingreso</th>
                                    <th class="p-3 text-right">Método</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="item in recentAttendances" :key="item.id">
                                    <td class="p-3 font-semibold text-gray-800">{{ item.user.name }}</td>
                                    <td class="p-3 font-mono">{{ item.user.dni }}</td>
                                    <td class="p-3">{{ new Date(item.scanned_at).toLocaleTimeString() }}</td>
                                    <td class="p-3 text-right">
                                        <span class="bg-indigo-100 text-indigo-800 font-bold px-2 py-0.5 rounded text-xs uppercase">
                                            {{ item.registered_by }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="recentAttendances.length === 0">
                                    <td colspan="4" class="p-4 text-center text-gray-400">
                                        No hay ingresos registrados en esta reunión aún.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<!-- <script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template> -->
