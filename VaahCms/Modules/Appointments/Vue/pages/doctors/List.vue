<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from 'vue-router';

import {useDoctorStore} from '../../stores/store-doctors'
import {useRootStore} from '../../stores/root'

import Actions from "./components/Actions.vue";
import Table from "./components/Table.vue";
import Filters from './components/Filters.vue'
import DoctorFilters from './components/DoctorFilters.vue';

const store = useDoctorStore();
const root = useRootStore();
const route = useRoute();
const chartData = ref(null);
const chartOptions = ref(null);

import { useConfirm } from "primevue/useconfirm";
const confirm = useConfirm();
const visible = ref(false);

onMounted(async () => {
    document.title = 'Doctors - Appointments';
    store.item = null;
    /**
     * call onLoad action when List view loads
     */
    await store.onLoad(route);

    /**
     * watch routes to update view, column width
     * and get new item when routes get changed
     */
    await store.watchRoutes(route);

    /**
     * watch states like `query.filter` to
     * call specific actions if a state gets
     * changed
     */
    await store.watchStates();

    /**
     * fetch assets required for the crud
     * operation
     */
    await store.getAssets();

    /**
     * fetch list of records
     */
    await store.getList();

    await store.getListCreateMenu();

    chartData.value = setChartData();
    chartOptions.value = setChartOptions();

});

//--------form_menu
const create_menu = ref();
const toggleCreateMenu = (event) => {
    create_menu.value.toggle(event);
};
//--------/form_menu

//-----------------------------------------------------------------------//
const setChartData = () => {
    const documentStyle = getComputedStyle(document.body);

    return {
        labels: ['Scheduled Appointments', 'Cancelled Appointments'],
        datasets: [
            {
                data: [totalActiveAppointments, totalCancelledAppointments],
                backgroundColor: [documentStyle.getPropertyValue('--green-500'), documentStyle.getPropertyValue('--red-500')],
                hoverBackgroundColor: [documentStyle.getPropertyValue('--green-400'), documentStyle.getPropertyValue('--red-400')]
            }
        ]
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');

    return {
        plugins: {
            legend: {
                labels: {
                    usePointStyle: true,
                    color: textColor
                }
            }
        }
    };
};

const totalActiveAppointments = computed(() => {
    if(store.list.data){
        return store.list.data.reduce((total, appointments) => {
            return total + appointments.active_appointments_count;
        }, 0);
    }
});

const totalCancelledAppointments = computed(() => {
    if(store.list.data){
        return store.list.data.reduce((total, appointments) => {
            return total + appointments.cancelled_appointments_count;
        }, 0);
    }
});

const totalRevenueGenerated = computed(() => {
    if(store.list.data){
        return store.list.data.reduce((total, appointments) => {
            return total + appointments.total_cost;
        }, 0);
    }
});

const fileInput = ref(null);

const openFileDialog = () => {
    fileInput.value.click();
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const contents = e.target.result;
            const jsonData = csvToJson(contents);
            console.log('Parsed JSON data:', jsonData);
            importDoctors(jsonData);
        };
        reader.readAsText(file);
    }
};

const csvToJson = (csv) => {
    const lines = csv.split('\n');
    const result = [];
    const headers = lines[0].split(',');

    for (let i = 1; i < lines.length; i++) {
        const obj = {};
        const currentLine = lines[i].split(',');
        for (let j = 0; j < headers.length; j++) {
            obj[headers[j].trim()] = currentLine[j] ? currentLine[j].trim() : '';
        }
        result.push(obj);
    }
    return result;
};

const exportDoctors = () => {
    store.exportDoctors();
}

const importDoctors = (jsonData) => {
    store.importDoctors(jsonData);
}
//-------------------------------------------------------------------------------
</script>
<template>

    <div class="grid" v-if="store.assets">
            <Sidebar v-model:visible="visible" header="" position="right">
                <template #header>
<!--                    <div style="display: flex; justify-content: space-between; align-items: center;">-->
<!--                        <h2 style="margin: 0;">Appointment Stats</h2>-->
<!--                    </div>-->
                </template>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="margin: 0;">Appointment Stats</h4>
                </div>

                <div v-if="store.list.data.length > 0" class="card flex justify-content-center">
                    <Chart type="pie" :data="chartData" :options="chartOptions" class="w-full md:w-30rem"/>
                </div>

                <div v-else>
                    <span>No Record Found</span>
                </div>
            </Sidebar>

        <div v-if="store.list.data" class="stat-block" style="display: flex; justify-content: center; align-items: center; height: 100%;    ">
            <Card style="width: 20rem; height: 10rem;margin-left: 10px; overflow: hidden">
                <template #title>
                    <span style="font-weight: bold; font-size: 20px;">Total Appointments</span>
                </template>
                <template #content>
                    <span style="font-weight: bold; font-size: 50px;">{{totalActiveAppointments + totalCancelledAppointments}}</span>
                </template>
            </Card>

            <Card style="width: 20rem; height: 10rem;margin-left: 10px; overflow: hidden">
                <template #title>
                    <span style="font-weight: bold; font-size: 20px;">Total Appointments Scheduled</span>
                </template>
                <template #content>
                   <span style="font-weight: bold; font-size: 50px;">{{totalActiveAppointments}}</span>
                </template>
            </Card>

            <Card style="width: 20rem; height: 10rem;margin-left: 10px; overflow: hidden">
                <template #title>
                    <span style="font-weight: bold; font-size: 20px;">Total Appointments Cancelled</span>
                </template>
                <template #content>
                    <span style="font-weight: bold; font-size: 50px;">{{totalCancelledAppointments}}</span>
                </template>
            </Card>

            <Card style="width: 20rem; height: 10rem;margin-left: 10px; overflow: hidden">
                <template #title>
                    <span style="font-weight: bold; font-size: 20px;">Total Revenue Generated</span>
                </template>
                <template #content>
                    <span style="font-weight: bold; font-size: 50px;">${{totalRevenueGenerated}}</span>
                </template>
            </Card>

            <Button v-tooltip:top="'Graphical Report'"
                    icon="pi pi-arrow-right"
                    @click="visible = true"
                    style="margin-left: 20px;"
                    severity="success"
                    rounded
            />
        </div>

<!--        <div class="card flex justify-content-center" style="margin: auto;width: 100%; height: 200px;">-->
<!--            <Chart v-if="chartData && chartOptions" type="doughnut" :data="chartData" :options="chartOptions" class="w-full md:w-30rem" />-->
<!--        </div>-->
        <div :class="'col-'+(store.show_filters?9:store.list_view_width)">
            <Panel class="is-small">

                <template class="p-1" #header>

                    <div class="flex flex-row">
                        <div >
                            <b class="mr-1">Doctors</b>
                            <Badge v-if="store.list && store.list.total > 0"
                                   :value="store.list.total">
                            </Badge>
                        </div>

                    </div>

                </template>

                <div class="card">
                    <Button @click="openFileDialog">Upload Doctors CSV</Button>
                    <input type="file" ref="fileInput" @change="handleFileUpload" accept=".csv" style="display: none;" />
                    <Button label="Export Doctors"
                            @click="exportDoctors"
                            style="margin-left: 5px;"
                    />
                </div>

                <template #icons>
                    <div class="p-inputgroup">
                    <Button data-testid="doctors-list-create"
                            class="p-button-sm"
                            @click="store.toForm()">
                        <i class="pi pi-plus mr-1"></i>
                        Create
                    </Button>

                    <Button data-testid="doctors-list-reload"
                            class="p-button-sm"
                            @click="store.getList()">
                        <i class="pi pi-refresh mr-1"></i>
                    </Button>

                    <!--form_menu-->

                    <Button v-if="root.assets && root.assets.module
                                                && root.assets.module.is_dev"
                        type="button"
                        @click="toggleCreateMenu"
                        class="p-button-sm"
                        data-testid="doctors-create-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                    <Menu ref="create_menu"
                          :model="store.list_create_menu"
                          :popup="true" />

                    <!--/form_menu-->

                    </div>

                </template>

                <Actions/>

                <Table/>

            </Panel>
        </div>

         <Filters/>
        <DoctorFilters />
        <RouterView/>
    </div>


</template>
