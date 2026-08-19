<script setup>
import { ref, computed, onUnmounted } from 'vue';
import { useForm, Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Html5Qrcode } from 'html5-qrcode';

const props = defineProps({
    meetings: Array,
    attendances: Array,
    selectedMeetingId: Number,
});

const page = usePage();
const selectedMeeting = ref(props.selectedMeetingId || (props.meetings.length > 0 ? props.meetings[0].id : ''));
const dniInput = ref(null);
const searchFilter = ref('');
const activeTab = ref('dni'); // 'dni' o 'qr'

// Lógica de Escáner QR
const scannerActive = ref(false);
let html5QrCode = null;

const formDni = useForm({
    meeting_id: selectedMeeting.value,
    dni: '',
});

const formQr = useForm({
    meeting_id: selectedMeeting.value,
    qr_code: '',
});

const changeMeeting = () => {
    router.get(route('apafa.attendance.index'), { meeting_id: selectedMeeting.value }, { preserveState: true });
};

const submitDni = () => {
    formDni.meeting_id = selectedMeeting.value;
    formDni.post(route('apafa.attendance.dni'), {
        onSuccess: () => {
            formDni.dni = '';
            if (dniInput.value) dniInput.value.focus();
        },
    });
};

// Iniciar Cámara para QR
const startScanner = async () => {
    scannerActive.value = true;
    setTimeout(async () => {
        try {
            html5QrCode = new Html5Qrcode("reader");
            await html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    // Al detectar lectura QR exitosa:
                    onQrScanned(decodedText);
                },
                (errorMessage) => {
                    // Errores menores de cuadro no detectado (ignorar)
                }
            );
        } catch (err) {
            console.error("Error al iniciar cámara:", err);
        }
    }, 300);
};

// Detener Cámara
const stopScanner = async () => {
    if (html5QrCode && scannerActive.value) {
        try {
            await html5QrCode.stop();
            html5QrCode = null;
        } catch (err) {
            console.error("Error al detener cámara:", err);
        }
    }
    scannerActive.value = false;
};

const switchTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'qr') {
        startScanner();
    } else {
        stopScanner();
    }
};

const onQrScanned = (qrCodeText) => {
    formQr.meeting_id = selectedMeeting.value;
    formQr.qr_code = qrCodeText;
    
    // Pausamos momentáneamente el escaneo para procesar
    if (html5QrCode) html5QrCode.pause();

    formQr.post(route('apafa.attendance.qr'), {
        onFinish: () => {
            setTimeout(() => {
                if (html5QrCode && scannerActive.value) {
                    html5QrCode.resume();
                }
            }, 1500); // Espera 1.5s antes de volver a escanear otro
        }
    });
};

onUnmounted(() => {
    stopScanner();
});

const filteredAttendances = computed(() => {
    if (!searchFilter.value) return props.attendances;
    const query = searchFilter.value.toLowerCase();
    return props.attendances.filter(item => 
        item.user.name.toLowerCase().includes(query) ||
        item.user.dni.includes(query)
    );
});

const formatTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};
</script>

<template>
    <Head title="Control de Asistencia APAFA" />

    <AppLayout title="Asistencia APAFA">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Control de Asistencia - Reuniones APAFA
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Alertas Flash -->
                <div v-if="page.props.flash && page.props.flash.success" class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-semibold rounded shadow-sm flex items-center justify-between">
                    <span>{{ page.props.flash.success }}</span>
                </div>
                <div v-if="page.props.flash && page.props.flash.error" class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 font-semibold rounded shadow-sm">
                    {{ page.props.flash.error }}
                </div>
                <div v-if="formQr.errors.qr" class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 font-semibold rounded shadow-sm">
                    {{ formQr.errors.qr }}
                </div>

                <!-- Selección de Reunión y Total -->
                <div class="bg-white p-6 shadow-md sm:rounded-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Reunión Activa:</label>
                        <select v-model="selectedMeeting" @change="changeMeeting" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-medium">
                            <option v-for="meeting in meetings" :key="meeting.id" :value="meeting.id">
                                {{ meeting.title }} — ({{ meeting.meeting_date }})
                            </option>
                        </select>
                    </div>
                    <div class="bg-indigo-50 px-6 py-3 rounded-lg border border-indigo-100 text-center">
                        <span class="text-xs uppercase font-bold text-indigo-500 block">Total Asistentes</span>
                        <span class="text-3xl font-extrabold text-indigo-700">{{ attendances.length }}</span>
                    </div>
                </div>

                <!-- Grid Principal -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Panel Izquierdo: Pestañas de Registro (DNI / QR) -->
                    <div class="bg-white p-6 shadow-md sm:rounded-lg h-fit">
                        
                        <!-- Selector de Modo -->
                        <div class="flex border-b mb-6">
                            <button 
                                @click="switchTab('dni')" 
                                :class="['w-1/2 py-2 font-bold text-sm border-b-2 text-center transition', activeTab === 'dni' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600']"
                            >
                                Por DNI
                            </button>
                            <button 
                                @click="switchTab('qr')" 
                                :class="['w-1/2 py-2 font-bold text-sm border-b-2 text-center transition', activeTab === 'qr' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600']"
                            >
                                Escáner QR
                            </button>
                        </div>

                        <!-- Opción 1: Formulario DNI -->
                        <div v-show="activeTab === 'dni'">
                            <form @submit.prevent="submitDni" class="space-y-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">DNI del Padre / Apoderado</label>
                                    <input 
                                        ref="dniInput"
                                        v-model="formDni.dni" 
                                        type="text" 
                                        maxlength="8" 
                                        placeholder="Ingrese 8 dígitos..." 
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xl tracking-widest text-center font-mono"
                                        required
                                        autofocus
                                    />
                                    <span v-if="formDni.errors.dni" class="text-red-600 text-sm mt-1 block font-medium">
                                        {{ formDni.errors.dni }}
                                    </span>
                                </div>

                                <button 
                                    type="submit" 
                                    :disabled="formDni.processing"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md transition duration-150 shadow"
                                >
                                    Marcar Asistencia
                                </button>
                            </form>
                        </div>

                        <!-- Opción 2: Escáner QR -->
                        <div v-show="activeTab === 'qr'" class="space-y-4 text-center">
                            <p class="text-xs text-gray-500 mb-2">Apunta el código QR del padre o carnet hacia la cámara</p>
                            
                            <div id="reader" class="overflow-hidden rounded-lg border border-gray-200 bg-black min-h-[250px]"></div>

                            <p v-if="formQr.processing" class="text-indigo-600 font-bold text-sm animate-pulse">
                                Verificando código QR...
                            </p>
                        </div>

                    </div>

                    <!-- Panel Derecho: Lista en Tiempo Real -->
                    <div class="lg:col-span-2 bg-white p-6 shadow-md sm:rounded-lg">
                         <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                            <h3 class="text-lg font-bold text-gray-800">Lista de Asistentes</h3>
                            
                            <div class="flex items-center gap-2">
                                <a 
                                    :href="route('apafa.attendance.export.pdf', selectedMeeting)" 
                                    target="_blank"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-xs transition flex items-center gap-1 shadow"
                                >
                                    Descargar PDF
                                </a>

                                <input 
                                    v-model="searchFilter" 
                                    type="text" 
                                    placeholder="Buscar por nombre o DNI..." 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                />
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                                    <tr>
                                        <th class="p-3">#</th>
                                        <th class="p-3">Padre / Apoderado</th>
                                        <th class="p-3">DNI</th>
                                        <th class="p-3">Hora Ingreso</th>
                                        <th class="p-3">Método</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="(item, index) in filteredAttendances" :key="item.id" class="hover:bg-gray-50">
                                        <td class="p-3 font-mono text-gray-400">{{ attendances.length - index }}</td>
                                        <td class="p-3 font-semibold text-gray-800">{{ item.user.name }}</td>
                                        <td class="p-3 font-mono">{{ item.user.dni }}</td>
                                        <td class="p-3 font-medium text-indigo-600">{{ formatTime(item.scanned_at) }}</td>
                                        <td class="p-3">
                                            <span 
                                                :class="[
                                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                                    item.registered_by === 'self_qr' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                                                ]"
                                            >
                                                {{ item.registered_by === 'self_qr' ? 'CÓDIGO QR' : 'DNI MANUAL' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredAttendances.length === 0">
                                        <td colspan="5" class="p-6 text-center text-gray-400">
                                            No hay registros de asistencia para mostrar.
                                        </td>
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