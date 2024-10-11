<script setup>
import {useDoctorStore} from "../../../stores/store-doctors";
import {ref, watch, watchEffect, onMounted, onUnmounted, computed} from "vue";
const store = useDoctorStore();
const props = defineProps({
    // appointments: {
    //     type: Array,
    //     default: [],
    // },
    // visibility: {
    //     type: Boolean,
    //     default: false
    // },
    doctorid: {
        type: Number,
        default: null
    }
});

const appointment_details = computed(() => {
    const object = store.list.data.find(ele => ele.id === props.doctorid);
    return object ? object.appointments : []; // Ensure it returns an array
});
console.log('drawer appointments',appointment_details.value);
console.log('drawer',props.doctorid);
</script>

<template>
    <TabView>
        <TabPanel header="Booked">
            <table>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr v-for="(appointment,index) in appointment_details" :key="index">
                    <td v-if="appointment.status">{{appointment.patient.name}}</td>
                    <td v-if="appointment.status">{{appointment.patient.email}}</td>
                    <td v-if="appointment.status">{{appointment.patient.phone}}</td>
                    <td v-if="appointment.status">{{new Date(appointment.date_time).toLocaleTimeString()}}</td>
                    <Divider type="solid" separator="horizontal" />
                </tr>
            </table>
        </TabPanel>

        <TabPanel header="Cancelled">
            <table>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr v-for="(appointment,index) in appointment_details" :key="index">
                    <td v-if="!appointment.status">{{appointment.patient.name}}</td>
                    <td v-if="!appointment.status">{{appointment.patient.email}}</td>
                    <td v-if="!appointment.status">{{appointment.patient.phone}}</td>
                    <td v-if="!appointment.status">{{new Date(appointment.date_time).toLocaleTimeString()}}</td>
                    <Divider type="solid" separator="horizontal" />
                </tr>
            </table>
        </TabPanel>
    </TabView>

<!--    <div v-if="appointment_details.length > 0">-->
<!--    <DataTable :value="[1,2,3]"-->
<!--               class="p-datatable-sm p-datatable-hoverable-rows"-->
<!--               :nullSortOrder="-1"-->
<!--               stripedRows-->
<!--               responsiveLayout="scroll">-->

<!--        <Column field="name" header="Name"-->
<!--                class="overflow-wrap-anywhere"-->
<!--                :sortable="true">-->

<!--            <template #body="prop">-->
<!--                {{prop}}-->
<!--            </template>-->
<!--        </Column>-->

<!--    </DataTable>-->
<!--    </div>-->
</template>

