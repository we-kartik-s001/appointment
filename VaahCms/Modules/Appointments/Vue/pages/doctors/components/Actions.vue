<script  setup>
import {ref, reactive, watch, onMounted} from 'vue';
import {useRoute} from 'vue-router';

import { useDoctorStore } from '../../../stores/store-doctors'

const store = useDoctorStore();
const route = useRoute();

onMounted(async () => {
    store.getListSelectedMenu();
    store.getListBulkMenu();
    store.getCsvActions();
});

//--------selected_menu_state
const selected_menu_state = ref();
const toggleSelectedMenuState = (event) => {
    selected_menu_state.value.toggle(event);
};
//--------/selected_menu_state

//--------bulk_menu_state
const bulk_menu_state = ref();
const toggleBulkMenuState = (event) => {
    bulk_menu_state.value.toggle(event);
};
//--------/bulk_menu_state

//--------csv_actions_state
const csv_actions_state = ref();
const toggleCsvMenuState = (event) => {
    csv_actions_state.value.toggle(event);
};
//--------csv_actions_state

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

const importDoctors = (jsonData) => {
    store.importDoctors(jsonData);
}

watch(() => store.show_file_upload_dialog, (newVal) => {
    fileInput.value.click();
});
</script>

<template>
    <div>

        <!--actions-->
        <div :class="{'flex justify-content-between': store.isViewLarge()}" class="mt-2 mb-2">

            <!--left-->
            <div v-if="store.view === 'large'">

                <!--selected_menu-->
                <Button class="p-button-sm"
                    type="button"
                    @click="toggleSelectedMenuState"
                    data-testid="doctors-actions-menu"
                    aria-haspopup="true"
                    aria-controls="overlay_menu">
                    <i class="pi pi-angle-down"></i>
                    <Badge v-if="store.action.items.length > 0"
                           :value="store.action.items.length" />
                </Button>
                <Menu ref="selected_menu_state"
                      :model="store.list_selected_menu"
                      :popup="true" />
                <!--/selected_menu-->

                <!-- toggle button for import export -->
                <Button
                    type="button"
                    @click="toggleCsvMenuState"
                    data-testid="doctors-actions-bulk-menu"
                    aria-haspopup="true"
                    aria-controls="csv_actions"
                    class="ml-1 p-button-sm">
                    <i class="pi pi-ellipsis-v"></i>
                </Button>
                <Menu ref="csv_actions_state"
                      :model="store.csv_actions"
                      :popup="true" />

                <input type="file" ref="fileInput" @change="handleFileUpload" accept=".csv" style="display: none;" />

                <!-- toggle button for import export -->

            </div>
            <!--/left-->

            <!--right-->
            <div >


                <div class="grid p-fluid">


                    <div class="col-12">
                        <div class="p-inputgroup ">

                            <InputText v-model="store.query.filter.q"
                                       @keyup.enter="store.delayedSearch()"
                                       class="p-inputtext-sm"
                                       @keyup.enter.native="store.delayedSearch()"
                                       @keyup.13="store.delayedSearch()"
                                       data-testid="doctors-actions-search"
                                       placeholder="Search"/>
                            <Button @click="store.delayedSearch()"
                                    class="p-button-sm"
                                    data-testid="doctors-actions-search-button"
                                    icon="pi pi-search"/>
                            <Button
                                type="button"
                                class="p-button-sm"
                                :disabled="Object.keys(route.params).length"
                                data-testid="doctors-actions-show-filters"
                                @click="store.show_filters = !store.show_filters">
                                Filters
                                <Badge v-if="store.count_filters > 0" :value="store.count_filters"></Badge>
                            </Button>
                            <Button
                                type="button"
                                data-testid="doctors-actions-show-field-filters"
                                class="p-button-sm"
                                @click="store.showFieldFilters()">
                                Apply field filters
                                <Badge v-if="store.count_field_filters > 0" :value="store.count_field_filters"></Badge>
                            </Button>
                            <Button
                                type="button"
                                icon="pi pi-filter-slash"
                                data-testid="doctors-actions-reset-filters"
                                class="p-button-sm"
                                label="Reset"
                                @click="store.resetQuery()" />

                                <!--bulk_menu-->
                                <Button
                                    type="button"
                                    @click="toggleBulkMenuState"
                                    severity="danger" outlined
                                    data-testid="doctors-actions-bulk-menu"
                                    aria-haspopup="true"
                                    aria-controls="bulk_menu_state"
                                    class="ml-1 p-button-sm">
                                    <i class="pi pi-ellipsis-v"></i>
                                </Button>
                                <Menu ref="bulk_menu_state"
                                      :model="store.list_bulk_menu"
                                      :popup="true" />
                                <!--/bulk_menu-->

                        </div>
                    </div>

                </div>

            </div>
            <!--/right-->

        </div>
        <!--/actions-->

    </div>
</template>
