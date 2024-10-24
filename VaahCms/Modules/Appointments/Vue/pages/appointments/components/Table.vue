<script setup>
import { vaah } from '../../../vaahvue/pinia/vaah'
import { useAppointmentStore } from '../../../stores/store-appointments'
import {onMounted, ref} from "vue";
import {addMinutes} from 'date-fns';
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ImportCsvDialog from "./ImportCsvDialog.vue";
import useMobileView from "../../../mixins/MobileMixin.js";
const confirm = useConfirm();
const toast = useToast();
const { isMobile } = useMobileView();
const store = useAppointmentStore();
const useVaah = vaah();
const csv_import_dialog = ref();

onMounted(() => {
    store.listCsvImportSteps();
})

const deleteAppointment = (action,data) => {
    confirm.require({
        message: 'Are you sure you want to delete the appointment?',
        header: 'Delete Appointment',
        acceptProps: {
            label: 'Save'
        },
        accept: () => {
            if(store.itemAction(action, data)){
                toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted', life: 3000 });
            }
        },
        reject: () => {
           return
        }
    });
};

const cancelAppointment = (action,data) => {
    confirm.require({
        message: 'Do you want to cancel this appointment?',
        header: 'Appointment Cancellation',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            if(store.itemAction(action, data)){
                toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Appointment Cancelled Successfully', life: 3000 });
            }
        },
        reject: () => {
            return
        }
    });
};

const onSidebarHide = () => {
    store.show_file_upload_dialog = false;
}
</script>

<template>
    <Sidebar v-model:visible="store.show_file_upload_dialog"
             position="full"
             ref="csv_import_dialog"
             @hide="onSidebarHide"
    >
        <Steps :model="store.steps"
               aria-label="Form Steps"
               class="steps-custom mb-5"
               v-model:activeStep="store.active_index"
        />
        <ImportCsvDialog :activeindex="store.active_index"/>
    </Sidebar>

    <div v-if="store.list">
        <!--table-->
         <DataTable v-if="!isMobile" :value="store.list.data"
                   dataKey="id"
                   :rowClass="store.setRowClass"
                   class="p-datatable-sm p-datatable-hoverable-rows"
                   :nullSortOrder="-1"
                   v-model:selection="store.action.items"
                   stripedRows
                   responsiveLayout="scroll">

            <Column selectionMode="multiple"
                    v-if="store.isViewLarge()"
                    headerStyle="width: 3em">
            </Column>

            <Column field="id" header="ID" :style="{width: '80px'}" :sortable="true">
            </Column>

            <Column field="name" header="Name"
                    class="overflow-wrap-anywhere"
                    :sortable="true">

                <template #body="prop">
                    {{prop.data.patient.name}}
                </template>

            </Column>

             <Column field="doctor" header="Doctor"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     {{prop.data.doctor.name}} - {{prop.data.doctor.specialization}}
                 </template>

             </Column>

             <Column field="slot" header="Slot"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     {{new Date(prop.data.date_time).toLocaleTimeString()}} - {{new Date(addMinutes(new Date(prop.data.date_time),15)).toLocaleTimeString()}}
                 </template>

             </Column>

             <Column field="status" header="Status"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="!prop.data.status"
                            value="Cancelled"
                            severity="danger"></Badge>
                     <Badge v-else
                            value="Booked"
                            severity="success"></Badge>
                 </template>

             </Column>

                <Column field="created_at" header="Created At"
                        v-if="store.isViewLarge()"
                        style="width:150px;"
                        :sortable="true">

                    <template #body="prop">
                        {{useVaah.strToSlug(prop.data.created_at)}}
                    </template>

                </Column>

            <Column field="actions" style="width:150px;"
                    :style="{width: store.getActionWidth() }"
                    :header="store.getActionLabel()">

                <template #body="prop">
                    <div class="p-inputgroup ">

                        <p v-for="permission in store.assets.permission">
                        <Button v-if=" permission == 'appointments-can-create-patients' "
                                class="p-button-tiny p-button-text"
                                data-testid="appointments-table-to-view"
                                v-tooltip.top="'View'"
                                @click="store.toView(prop.data)"
                                icon="pi pi-eye" />
                        </p>

                        <p v-for="permission in store.assets.permission">
                        <Button v-if=" permission == 'appointments-can-create-patients' "
                                class="p-button-tiny p-button-text"
                                data-testid="appointments-table-to-edit"
                                v-tooltip.top="'Update'"
                                @click="store.toEdit(prop.data)"
                                icon="pi pi-pencil" />
                        </p>

                        <Button class="p-button-tiny p-button-danger p-button-text"
                                data-testid="appointments-table-action-trash"
                                v-if="store.isViewLarge()"
                                @click = "deleteAppointment('trash', prop.data)"
                                v-tooltip.top="'Trash'"
                                icon="pi pi-trash" />

                        <Button
                            v-if="prop.data.status"
                            class="p-button-danger p-button-text"
                            data-testid="appointments-table-to-cacnel"
                            @click = "cancelAppointment('cancel', prop.data)"
                            v-tooltip.top="'Cancel Appointment'"
                            icon="pi pi-times" />
                    </div>
                </template>


            </Column>

             <template #empty>
                 <div class="text-center py-3">
                     No records found.
                 </div>
             </template>

        </DataTable>

        <span v-else-if="isMobile">
            <Card v-for="(data, index) in store.list.data" :key="index" style="margin-bottom: 20px; background-color: #e4e8f1">
                <template #content>
                    <h3>{{data.patient.name.concat("\'s")}} Appointment</h3>
                    <ul>
                        <li>Doctor: {{data.doctor.name}}</li>
                        <li>Specialization: {{data.doctor.specialization}}</li>
                        <li>Date: {{new Date(data.date_time).toLocaleDateString(undefined,{ year: 'numeric', month: '2-digit', day: '2-digit'})}}</li>
                        <li>Time Slot: {{new Date(data.date_time).toLocaleTimeString(undefined,{ hour: '2-digit', minute: '2-digit', hour12: true })}} - {{new Date(addMinutes(new Date(data.date_time),15)).toLocaleTimeString(undefined,{ hour: '2-digit', minute: '2-digit', hour12: true })}}</li>
                        <li>
                            Status:  <Badge v-if="!data.status"
                                            value="Cancelled"
                                            severity="danger"></Badge>
                                     <Badge v-else
                                            value="Booked"
                                            severity="success"></Badge>
                        </li>
                    </ul>
                </template>

                 <template #footer>
                     <div class="p-inputgroup ">
                        <Button class="p-button-tiny p-button-danger p-button-text w-full"
                                data-testid="appointments-table-action-trash"
                                v-if="store.isViewLarge()"
                                @click = "deleteAppointment('trash', data)"
                                v-tooltip.top="'Trash'"
                                icon="pi pi-trash" />

                        <Button
                            v-if="data.status"
                            class="p-button-danger p-button-text w-full"
                            data-testid="appointments-table-to-cacnel"
                            @click = "cancelAppointment('cancel', data)"
                            v-tooltip.top="'Cancel Appointment'"
                            icon="pi pi-times" />
                    </div>
                </template>

            </Card>
        </span>
        <!--/table-->

        <!--paginator-->
        <Paginator v-if="store.query.rows"
                   v-model:rows="store.query.rows"
                   :totalRecords="store.list.total"
                   :first="((store.query.page??1)-1)*store.query.rows"
                   @page="store.paginate($event)"
                   :rowsPerPageOptions="store.rows_per_page"
                   class="bg-white-alpha-0 pt-2">
        </Paginator>
        <!--/paginator-->

    </div>

</template>
