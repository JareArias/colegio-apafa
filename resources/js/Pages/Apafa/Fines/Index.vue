<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    fines: Object,
    filters: Object,
});

const user = usePage().props.auth.user;
const search = ref(props.filters.search || '');

const handleSearch = () => {
    router.get(route('apafa.fines.index'), { search: search.value }, { preserveState: true, replace: true });
};

const markAsPaid = (fineId) => {
    if (confirm('¿Confirmar que esta multa ha sido pagada en tesorería?')) {
        router.post(route('apafa.fines.pay', fineId));
    }
};
</script>

<template>
    <Head title="Estado de Cuenta y Multas" />

    <AppLayout title="Registro de Sanciones e Inasistencias">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Estado de Cuenta / Registro de Multas
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Buscador (Visible solo para administradores) -->
                <div v-if="user.role === 'admin'" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <input 
                        v-model="search" 
                        @input="handleSearch"
                        type="text" 
                        placeholder="Buscar multa por DNI o nombre del padre..." 
                        class="w-full sm:w-80 rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>

                <!-- Tabla de Multas -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th v-if="user.role === 'admin'" class="p-3">Apoderado</th>
                                    <th class="p-3">Reunión / Motivo</th>
                                    <th class="p-3">Monto</th>
                                    <th class="p-3">Estado</th>
                                    <th v-if="user.role === 'admin'" class="p-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="fine in fines.data" :key="fine.id">
                                    <td v-if="user.role === 'admin'" class="p-3">
                                        <div class="font-bold text-gray-800">{{ fine.parent?.name }}</div>
                                        <div class="text-xs text-gray-400">DNI: {{ fine.parent?.dni }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-medium text-gray-900">{{ fine.meeting?.title }}</div>
                                        <div class="text-xs text-gray-400">{{ fine.meeting?.date }}</div>
                                    </td>
                                    <td class="p-3 font-mono font-bold text-red-600">
                                        S/ {{ parseFloat(fine.amount).toFixed(2) }}
                                    </td>
                                    <td class="p-3">
                                        <span 
                                            :class="fine.status === 'paid' 
                                                ? 'bg-emerald-100 text-emerald-700 border-emerald-200' 
                                                : 'bg-amber-100 text-amber-700 border-amber-200'"
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold border"
                                        >
                                            {{ fine.status === 'paid' ? 'Pagado' : 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td v-if="user.role === 'admin'" class="p-3 text-right">
                                        <button 
                                            v-if="fine.status === 'pending'"
                                            @click="markAsPaid(fine.id)"
                                            class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition shadow"
                                        >
                                            Marcar Pagado
                                        </button>
                                        <span v-else class="text-xs text-gray-400 italic">Sin acciones</span>
                                    </td>
                                </tr>
                                <tr v-if="fines.data.length === 0">
                                    <td :colspan="user.role === 'admin' ? 5 : 3" class="p-4 text-center text-gray-400">
                                        No se registraron multas ni inasistencias.
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