<script setup>
import {useAppointmentStore} from "../../../stores/store-appointments";
import {defineProps, watch, ref, onMounted, computed} from "vue";
const store = useAppointmentStore();

onMounted(() => {
    store.listDatabaseHeaders();
})

const props = defineProps({
    activeindex: {
        type: Number,
    }
})
let active_index = ref(0);
const uploadedFile = ref(null);
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        uploadedFile.value = file;
    }
};
const removeFile = ()=>{
    uploadedFile.value = null;
}

const uploadFile = ()=>{
    if(!uploadedFile.value){
        alert('File should be uploaded!!!');
    }
    const file = uploadedFile.value;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const contents = e.target.result;
            const jsonData = csvToJson(contents);
            store.getUploadedCsvHeaders(jsonData);
        };
        reader.readAsText(file);
    }
}

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

// store.field_mappers.doctor_fields_mapper = computed(() => {
//     return store.headers.doctors.map((header, index) => {
//         return {
//             header: header,
//             selectedValue: store.csv_headers[index] || null
//         };
//     });
// })
//
// store.field_mappers.patient_fields_mapper = computed(() => {
//     return store.headers.patients.map((header, index) => {
//         return {
//             header: header,
//             selectedValue: store.csv_headers[index] || null
//         };
//     });
// })

store.field_mappers.appointments_fields_mapper = computed(() => {
    return store.headers.appointments.map((header, index) => {
        return {
            header: header,
            selectedValue: store.csv_headers[index] || null
        };
    });
})

const mapHeaders = () => {
    store.mapHeadersHandler();
}
watch(
    () => props.activeindex, (newValue) => {
        active_index = props.activeindex
    }
);
</script>

<template>
    <div class="csv-import-dialog">
        <div v-if="active_index === 0">
            <div class="upload-section">
                <input type="file" id="file-upload" class="file-input" @change="handleFileUpload"/>
                <label for="file-upload" class="upload-label">
                    <span>Drag and drop files here or click to upload</span>
                </label>

                <div v-if="uploadedFile" class="mt-4">
                    <div>
                        {{ uploadedFile.name }}
                        <Button severity="danger"
                                @click="removeFile(uploadedFile)"
                                v-tooltip:right="'Remove File'"
                        >
                            &cross;
                        </Button>
                        <Button class="pi-file-export ml-2"
                                severity="success"
                                @click="uploadFile"
                                v-tooltip:right="'Upload File'"
                        >
                            do it
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="active_index === 1">
            <div v-if="store.csv_headers">
<!--                    <div v-for="(header,index) in store.headers.doctors">-->
<!--                       <span v-if="header != 'id'">-->
<!--                            {{header}}-->
<!--                        <Dropdown class="w-225"-->
<!--                                  v-model="store.csv_headers[index]"-->
<!--                                  :options="store.csv_headers"-->
<!--                                  placeholder="Select Your Headers"-->
<!--                                  filter-->
<!--                        />-->
<!--                        </span>-->
<!--                    </div>-->
<!--                    <div v-for="(header,index) in store.headers.patients">-->
<!--                        <span v-if="header != 'id'">-->
<!--                            {{header}}-->
<!--                        <Dropdown class="w-225"-->
<!--                                  v-model="store.csv_headers[index]"-->
<!--                                  :options="store.csv_headers"-->
<!--                                  placeholder="Select Your Headers"-->
<!--                                  filter-->
<!--                        />-->
<!--                        </span>-->
<!--                    </div>-->
<!--                    <div v-for="(header,index) in store.headers.appointments">-->
<!--                        <span v-if="header != 'id'">-->
<!--                            {{header}}-->
<!--                        <Dropdown class="w-225"-->
<!--                                  v-model="store.csv_headers[index]"-->
<!--                                  :options="store.csv_headers"-->
<!--                                  placeholder="Select Your Headers"-->
<!--                                  filter-->
<!--                        />-->
<!--                        </span>-->
<!--                    </div>-->
                        <div class="flex justify-center my-8">
                            <div class="overflow-x-auto w-full max-w-4xl">
                                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                                    <thead class="bg-gray-200 text-gray-700">
                                    <tr class="align-center">
                                        <th class="py-3 px-6 border-b text-left">Database Mapper</th>
                                        <th class="py-3 px-6 border-b text-left">CSV Headers</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(header, index) in store.headers.appointments" :key="index" class="hover:bg-gray-100 transition-colors duration-200">
                                        <td class="py-3 px-6 border-b text-center" v-if="header != 'id'">
                                            <span v-if="header === 'patient_id'">Patient Email</span>
                                            <span v-if="header === 'doctor_id'">Doctor Email</span>
                                            <span v-if="header === 'date_time'">Time Slot</span>
                                        </td>
                                        <td class="py-3 px-6 border-b text-center" v-if="header != 'id'">
                                            <Dropdown class="w-full"
                                                      :options="store.csv_headers"
                                                      placeholder="Select Your Headers"
                                                      filter
                                            />
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <Button @click="mapHeaders">Map Data</Button>
                    <div>

                    </div>
                </div>
                <div v-else>
                    <h3>Nothing to map</h3>
                </div>
        </div>
        <div v-else-if="active_index === 2">
            Page 2
        </div>
    </div>

    <div class="flex justify-center">
        <div class="button-panel">
            <div class="flex space-x-4 md:space-x-8">
                <Button @click="store.previousImportStep">Previous</Button>
                <Button @click="store.nextImportStep">Next</Button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.csv-import-dialog{
    margin-bottom: 10px;
}

.upload-section {
    width: 500px;              /* Set a fixed width */
    height: 300px;             /* Set a fixed height */
    border: 2px dashed #737475; /* Dashed border for the upload area */
    border-radius: 8px;        /* Rounded corners */
    padding: 20px;             /* Padding inside the upload section */
    text-align: center;        /* Center text */
    background-color: #f9f9f9; /* Light background color */
    transition: border-color 0.3s; /* Smooth transition for border color */
    display: flex;             /* Use flexbox for centering */
    flex-direction: column;    /* Arrange items in a column */
    justify-content: center;    /* Center items vertically */
    align-items: center;       /* Center items horizontally */
    margin: 0 auto;           /* Center the section horizontally on the page */
}

.upload-section:hover {
    border-color: #737475; /* Change border color on hover */
}

.file-input {
    display: none; /* Hide the default file input */
}

.upload-label {
    display: inline-block; /* Make label behave like a block element */
    padding: 20px;        /* Padding inside the label */
    background-color: #e7f1ff; /* Light blue background */
    border: 1px solid #737475; /* Solid border */
    border-radius: 18px;   /* Rounded corners */
    cursor: pointer;      /* Pointer cursor on hover */
}
</style>
