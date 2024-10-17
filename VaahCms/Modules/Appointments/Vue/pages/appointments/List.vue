<script setup>
import {onMounted, reactive, ref} from "vue";
import {useRoute} from 'vue-router';

import {useAppointmentStore} from '../../stores/store-appointments'
import {useRootStore} from '../../stores/root'

import Actions from "./components/Actions.vue";
import Table from "./components/Table.vue";
import Filters from './components/Filters.vue'

const store = useAppointmentStore();
const root = useRootStore();
const route = useRoute();

import { useConfirm } from "primevue/useconfirm";
const confirm = useConfirm();


onMounted(async () => {
    document.title = 'Appointments - Appointments';
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

});

//--------form_menu
const create_menu = ref();
const toggleCreateMenu = (event) => {
    create_menu.value.toggle(event);
};
//--------/form_menu

// const fileInput = ref(null);
//
// const openFileDialog = () => {
//     fileInput.value.click();
// };
//
// const handleFileUpload = (event) => {
//     const file = event.target.files[0];
//     if (file) {
//         const reader = new FileReader();
//         reader.onload = (e) => {
//             const contents = e.target.result;
//             const jsonData = csvToJson(contents);
//             console.log('Parsed JSON data:', jsonData);
//             importAppointments(jsonData);
//         };
//         reader.readAsText(file);
//     }
// };
//
// const csvToJson = (csv) => {
//     const lines = csv.split('\n');
//     const result = [];
//     const headers = lines[0].split(',');
//
//     for (let i = 1; i < lines.length; i++) {
//         const obj = {};
//         const currentLine = lines[i].split(',');
//         for (let j = 0; j < headers.length; j++) {
//             obj[headers[j].trim()] = currentLine[j] ? currentLine[j].trim() : '';
//         }
//         result.push(obj);
//     }
//     return result;
// };
//
// const exportAppointments = () => {
//     store.exportAppointments();
// }
//
// const importAppointments = (jsonData) => {
//     store.exportAppointments(jsonData);
// }

</script>
<template>

    <div class="grid" v-if="store.assets">

        <div :class="'col-'+(store.show_filters?9:store.list_view_width)">
            <Panel class="is-small">

                <template class="p-1" #header>

                    <div class="flex flex-row">
                        <div >
                            <b class="mr-1">Appointments</b>
                            <Badge v-if="store.list && store.list.total > 0"
                                   :value="store.list.total">
                            </Badge>
                        </div>

                    </div>

                </template>

<!--                <div class="card">-->
<!--                    <Button @click="openFileDialog"-->
<!--                            style="background-color: #224e90; color: #fff;"-->
<!--                            label="Import CSV"-->
<!--                    />-->

<!--                    <input type="file" ref="fileInput" @change="handleFileUpload" accept=".csv" style="display: none;" />-->
<!--                    <Button @click="exportAppointments"-->
<!--                            style="margin-left: 5px; background-color: #c46862; color: #fff;"-->
<!--                            label="Export CSV"-->
<!--                    />-->
<!--                </div>-->

                <template #icons>

                    <div class="p-inputgroup">

                    <Button data-testid="appointments-list-create"
                            class="p-button-sm"
                            @click="store.toForm()"
                            v-if="store.assets.permission.includes('appointments-can-create-patients')"
                    >
                        <i class="pi pi-plus mr-1"></i>
                        Create
                    </Button>

                    <Button data-testid="appointments-list-reload"
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
                        data-testid="appointments-create-menu"
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

        <RouterView/>

    </div>


</template>
