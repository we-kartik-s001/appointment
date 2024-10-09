<script  setup>
import {useRootStore} from "../../stores/root";
import {useAppointmentStore} from "../../stores/store-appointments";
document.title = 'Appointments';
const store = useRootStore();
</script>
<template  >
  <div style="margin-top: 8px;" v-if="store.assets">
    <h1 class="text-2xl" style="margin-bottom: 8px;">Dashboard</h1>
      <Panel>
        <DataTable :value="store.assets.empty_item.appointments"
                   dataKey="id"
                   stripedRows
                   responsiveLayout="scroll"
                   v-model="store.assets.empty_item.appointments"
        >

            <Column field="name" header="Doctor's Name"
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

            <Column field="slot_timings" header="Slot timings"
                    class="overflow-wrap-anywhere"
                    :sortable="true">

                <template #body="prop">
                    <Badge v-if="prop.data.deleted_at"
                           value="Trashed"
                           severity="danger"></Badge>
                    {{new Date(prop.data.start_time).toLocaleTimeString()}} - {{new Date(prop.data.end_time).toLocaleTimeString()}}
                </template>

            </Column>

            <Column field="price" header="Price per slot"
                    class="overflow-wrap-anywhere"
                    :sortable="true">

                <template #body="prop">
                    <Badge v-if="prop.data.deleted_at"
                           value="Trashed"
                           severity="danger"></Badge>
                    ${{prop.data.price}}
                </template>

            </Column>

            <Column field="total_appointments" header="No. of appointments"
                    class="overflow-wrap-anywhere"
                    :sortable="true"
            >

                <template #body="prop">
                    <Badge v-if="prop.data.deleted_at"
                           value="Trashed"
                           severity="danger"></Badge>
                <Button v-if="prop.data.appointments.length > 0"
                        severity="help"
                        v-tooltip.right="'Click to view the scheduled appointments'"
                >
                    {{prop.data.appointments.length}}
                </Button>
                <p v-else style="color: red;">No Upcoming Appointment</p>
                </template>

            </Column>

            <Column field="amount" header="Amount"
                    class="overflow-wrap-anywhere"
                    :sortable="true"
            >

                <template #body="prop">
                    <Badge v-if="prop.data.deleted_at"
                           value="Trashed"
                           severity="danger"></Badge>
                    {{prop.data.appointments.length > 0 ? `$${prop.data.appointments.length*prop.data.price}` : `-`}}
                </template>

            </Column>

        </DataTable>
      </Panel>
  </div>

</template>


