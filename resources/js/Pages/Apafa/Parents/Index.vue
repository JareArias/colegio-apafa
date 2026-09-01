<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    parents: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const form = useForm({
    file: null,
});

const handleSearch = () => {
    router.get(route('apafa.parents.index'), { search: search.value }, { preserveState: true, replace: true });
};

const submitImport = () => {
    if (!form.file) return;
    form.post(route('apafa.parents.import'), {
        onSuccess: () => {
            form.reset();
            alert('¡Padrón importado correctamente!');
        },
    });
};
</script>

<template>
    <Head title="Padrón de Padres" />

    <AppLayout title="Padrón General de Apoderados">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Padrón de Padres de Familia y Estudiantes
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Card de Carga Masiva (Excel) -->
                <div class="bg-indigo-900 text-white p-6 rounded-2xl shadow-lg flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold">Importación Masiva desde Excel</h3>
                        <p class="text-xs text-indigo-200 mt-1">
                            El archivo Excel debe tener las cabeceras: <code>dni_padre</code>, <code>nombre_padre</code>, <code>dni_alumno</code>, <code>nombre_alumno</code>, <code>grado</code>, <code>seccion</code>.
                        </p>
                    </div>

                    <form @submit.prevent="submitImport" class="flex items-center gap-2 w-full md:w-auto">
                        <input 
                            type="file" 
                            @change="e => form.file = e.target.files[0]" 
                            accept=".xlsx, .xls, .csv" 
                            class="text-xs text-indigo-200 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-800 file:text-white hover:file:bg-indigo-700 cursor-pointer"
                            required
                        />
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-emerald-500 hover:bg-emerald-600 font-bold px-4 py-2 rounded-xl text-xs transition shadow disabled:opacity-50"
                        >
                            {{ form.processing ? 'Cargando...' : 'Subir Excel' }}
                        </button>
                    </form>
                </div>

                <!-- Buscador y Tabla -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <div class="flex justify-between items-center">
                        <input 
                            v-model="search" 
                            @input="handleSearch"
                            type="text" 
                            placeholder="Buscar por DNI o nombre del padre..." 
                            class="w-full sm:w-80 rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="p-3">DNI Padre</th>
                                    <th class="p-3">Apoderado</th>
                                    <th class="p-3">Hijos / Estudiantes Asociados</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="parent in parents.data" :key="parent.id">
                                    <td class="p-3 font-mono font-bold text-gray-800">{{ parent.dni || 'Sin DNI' }}</td>
                                    <td class="p-3 font-medium text-gray-900">{{ parent.name }}</td>
                                    <td class="p-3">
                                        <div v-if="parent.students.length > 0" class="flex flex-wrap gap-1">
                                            <span 
                                                v-for="student in parent.students" 
                                                :key="student.id"
                                                class="bg-indigo-50 text-indigo-700 text-xs px-2 py-1 rounded-md font-medium border border-indigo-100"
                                            >
                                                🎓 {{ student.name }} ({{ student.grade }} "{{ student.section }}")
                                            </span>
                                        </div>
                                        <span v-else class="text-xs text-gray-400">Sin hijos vinculados</span>
                                    </td>
                                </tr>
                                <tr v-if="parents.data.length === 0">
                                    <td colspan="3" class="p-4 text-center text-gray-400">
                                        No se encontraron registros.
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