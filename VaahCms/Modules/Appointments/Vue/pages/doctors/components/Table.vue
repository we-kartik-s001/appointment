<script setup>
import { vaah } from '../../../vaahvue/pinia/vaah'
import { useDoctorStore } from '../../../stores/store-doctors'
import {ref, computed} from "vue";
import DrawerContent from './DrawerContent.vue';
const store = useDoctorStore();
const useVaah = vaah();
const visible = ref(false);
const doctorid = ref(null);
const showAppointmentDetails = (id) => {
    visible.value = !visible.value;
    doctorid.value = id;
    console.log(doctorid.value);
}
</script>

<template>
    <Dialog v-if="store.csv_records_status.reporting_errors" v-model:visible="store.show_reporting_log" modal header="Errors" :style="{ width: '50rem' }">
        The following fields have error in some of the records -
<!--        <p v-for="(error,index) in store.csv_records_status.reporting_errors" :key="index">-->
<!--            {{error}}-->
<!--        </p>-->
        <br><br>
        <p v-if="store.csv_records_status.total_records">
            Total Records Processed : {{store.csv_records_status.total_records}}
        </p>
        <p v-if="store.csv_records_status.successful_records">
            Total Successful Records {{store.csv_records_status.successful_records}}
        </p>
        <p v-if="store.csv_records_status.failed_records">
            Total Failed Records {{store.csv_records_status.failed_records}}
        </p>
    </Dialog>
    <div v-if="store.list">
        <!--table-->
         <DataTable :value="store.list.data"
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
                    <Badge v-if="prop.data.deleted_at"
                           value="Trashed"
                           severity="danger"></Badge>
                    {{prop.data.name}}
                </template>

            </Column>

             <Column field="email" header="Email"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     {{prop.data.email}}
                 </template>

             </Column>

             <Column field="phone" header="Phone"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     {{prop.data.phone}}
                 </template>

             </Column>

             <Column field="specialization" header="Specialization"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     {{prop.data.specialization}}
                 </template>

             </Column>

             <Column field="price" header="Price per slot"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     {{prop.data.price ? '$'+prop.data.price : '-'}}
                 </template>

             </Column>

             <Column field="appointments" header="No. of appointments"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     <Button v-tooltip.right="'Click to view the appointments'"
                             v-if="prop.data.active_appointments_count > 0"
                             @click="showAppointmentDetails(prop.data.id)"
                             severity="help"
                     >
                        {{prop.data.active_appointments_count}}
                     </Button>
                     <p v-else>No upcoming appointments</p>
                 </template>

             </Column>

             <Column field="start_time" header="Start Time"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     {{new Date(prop.data.start_time).toLocaleTimeString()}}
                 </template>

             </Column>

             <Column field="end_time" header="End Time"
                     class="overflow-wrap-anywhere"
                     :sortable="true">

                 <template #body="prop">
                     <Badge v-if="prop.data.deleted_at"
                            value="Trashed"
                            severity="danger"></Badge>
                     {{new Date(prop.data.end_time).toLocaleTimeString()}}
                 </template>

             </Column>

             <Column field="is_active" v-if="store.isViewLarge()"
                     :sortable="true"
                     style="width:100px;"
                     header="Is Active">

                 <template #body="prop">
                     <InputSwitch v-model.bool="prop.data.is_active"
                                  data-testid="doctors-table-is-active"
                                  v-bind:false-value="0"  v-bind:true-value="1"
                                  class="p-inputswitch-sm"
                                  @input="store.toggleIsActive(prop.data)">
                     </InputSwitch>
                 </template>
             </Column>

            <Column field="actions" style="width:150px;"
                    :style="{width: store.getActionWidth() }"
                    :header="store.getActionLabel()">

                <template #body="prop">
                    <div class="p-inputgroup ">

                        <Button class="p-button-tiny p-button-text"
                                data-testid="doctors-table-to-view"
                                v-tooltip.top="'View'"
                                @click="store.toView(prop.data)"
                                icon="pi pi-eye" />

                        <Button class="p-button-tiny p-button-text"
                                data-testid="doctors-table-to-edit"
                                v-tooltip.top="'Update'"
                                @click="store.toEdit(prop.data)"
                                icon="pi pi-pencil" />

                        <Button class="p-button-tiny p-button-danger p-button-text"
                                data-testid="doctors-table-action-trash"
                                v-if="store.isViewLarge() && !prop.data.deleted_at"
                                @click="store.itemAction('trash', prop.data)"
                                v-tooltip.top="'Trash'"
                                icon="pi pi-trash" />


                        <Button class="p-button-tiny p-button-success p-button-text"
                                data-testid="doctors-table-action-restore"
                                v-if="store.isViewLarge() && prop.data.deleted_at"
                                @click="store.itemAction('restore', prop.data)"
                                v-tooltip.top="'Restore'"
                                icon="pi pi-replay" />


                    </div>

                </template>


            </Column>

             <template #empty>
                 <div class="text-center py-3">
                     No records found.
                 </div>
             </template>

        </DataTable>
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

        <Sidebar v-model:visible="visible" header="Appointments" position="right" role="region" style="width: auto;  width: 500px;">
            <DrawerContent :doctorid = "doctorid" />
        </Sidebar>
    </div>
</template>
