<script setup>
import {useDoctorStore} from "../../../stores/store-doctors";
import {computed} from "vue";
const store = useDoctorStore();
const props = defineProps({
    doctorid: {
        type: Number,
        default: null
    }
});

const appointment_details = computed(() => {
    const object = store.list.data.find(ele => ele.id === props.doctorid);
    return object ? object.appointments : []; // Ensure it returns an array
});
</script>

<template>
    <TabView>
        <TabPanel header="Booked">
            <table class="border-2 min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <td>Patient Name</td>
                    <td>Patient Email</td>
                    <td>Patient Phone</td>
                    <td>Booked Slot</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(appointment,index) in appointment_details" :key="index">
                    <td v-if="appointment.status">{{appointment.patient.name}}</td>
                    <td v-if="appointment.status">{{appointment.patient.email}}</td>
                    <td v-if="appointment.status">{{appointment.patient.phone}}</td>
                    <td v-if="appointment.status">{{new Date(appointment.date_time).toLocaleTimeString()}}</td>
                </tr>
                </tbody>
            </table>
        </TabPanel>

        <TabPanel header="Cancelled">
            <table class="border-2 min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <td>Patient Name</td>
                        <td>Patient Email</td>
                        <td>Patient Phone</td>
                        <td>Booked Slot</td>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="(appointment,index) in appointment_details" :key="index">
                    <td v-if="!appointment.status">{{appointment.patient.name}}</td>
                    <td v-if="!appointment.status">{{appointment.patient.email}}</td>
                    <td v-if="!appointment.status">{{appointment.patient.phone}}</td>
                    <td v-if="!appointment.status">{{new Date(appointment.date_time).toLocaleTimeString()}}</td>
                </tr>
                </tbody>
            </table>
        </TabPanel>
    </TabView>
</template>

<style scoped>

</style>

