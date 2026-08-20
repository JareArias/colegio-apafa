<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    parent: Object,
});

const printCarnet = () => {
    window.print();
};
</script>

<template>
    <Head title="Mi Carnet Digital APAFA" />

    <AppLayout title="Carnet Digital APAFA">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mi Carnet de Apoderado
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-md mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Tarjeta del Carnet -->
                <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-900 text-white rounded-2xl shadow-2xl overflow-hidden border border-indigo-500/30 p-6 relative">
                    
                    <!-- Decoración de fondo -->
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none"></div>

                    <!-- Cabecera de la Tarjeta -->
                    <div class="flex items-center justify-between border-b border-indigo-400/30 pb-4 mb-6">
                        <div>
                            <h3 class="font-black text-lg tracking-wide uppercase">COLEGIO APAFA</h3>
                            <p class="text-xs text-indigo-200">Asociación de Padres de Familia</p>
                        </div>
                        <span class="bg-emerald-500/20 text-emerald-300 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-400/30">
                            APODERADO ACTIVO
                        </span>
                    </div>

                    <!-- Datos del Padre -->
                    <div class="space-y-1 mb-6 text-center sm:text-left">
                        <p class="text-xs text-indigo-300 font-medium uppercase tracking-wider">Nombre del Titular</p>
                        <h4 class="text-xl font-bold text-white">{{ parent.name }}</h4>
                        
                        <div class="pt-2 flex justify-between items-center text-sm">
                            <div>
                                <p class="text-xs text-indigo-300 uppercase">DNI / Documento</p>
                                <p class="font-mono font-bold text-indigo-100">{{ parent.dni }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Código QR Centrado -->
                    <div class="bg-white p-4 rounded-xl shadow-inner flex flex-col items-center justify-center my-4">
                        <qrcode-vue :value="parent.dni" :size="180" level="H" />
                        <p class="text-xs text-gray-500 font-medium mt-2">Muestra este código al ingresar</p>
                    </div>

                    <!-- Pie del Carnet -->
                    <div class="text-center pt-2">
                        <p class="text-[10px] text-indigo-300">Este carnet es personal e intransferible para el marcado de asistencia.</p>
                    </div>

                </div>

                <!-- Botón de Acción -->
                <div class="text-center">
                    <button 
                        @click="printCarnet" 
                        class="w-full bg-white hover:bg-gray-50 text-indigo-700 font-bold py-3 px-4 rounded-xl shadow-md border border-gray-200 transition flex items-center justify-center gap-2"
                    >
                        🖨️ Imprimir o Guardar Carnet
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>