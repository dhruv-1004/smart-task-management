<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
const isEditingId=ref(null);
const isDeletingId=ref(null);
const updateModel=ref(null);
const selectedTask=ref(null); 
const deleteModel=ref(null);
const props = defineProps({
    show: Boolean,
    task: Object,
    due: Boolean,
    isAdmin: String
});


const newTask = useForm({
    title: '',
    description: '',
    priority: '',
    due_date: '',
    status: 'Pending' 
});
const progress=ref(props.task.progress ?? 0);

const emit = defineEmits(['updated'])
 
const localProgress = ref(props.task.progress ?? 0)
const isSaving = ref(false)
 
// Sync if parent prop changes (e.g. after Inertia reload)
watch(() => props.task.progress, (val) => {
  localProgress.value = val ?? 0
})

const statusLabel = computed(() => {
  if (localProgress.value === 0)   return 'Pending'
  if (localProgress.value === 100) return 'Completed'
  return 'In-progress'
})
const badgeClass = computed(() => {
  if (localProgress.value === 0)   return 'bg-gray-100 text-gray-500'
  if (localProgress.value === 100) return 'bg-green-100 text-green-700'
  return 'bg-blue-100 text-blue-600'
})
const fillColor = computed(() => {
  const p = localProgress.value
  if (p === 0)  return '#D1D5DB'   // gray-300
  if (p < 30)   return '#93C5FD'   // blue-300
  if (p < 60)   return '#3B82F6'   // blue-500
  if (p < 100)  return '#1D4ED8'   // blue-700
  return '#16A34A'                  // green-600
})

function onDragEnd() {
  const status =
    localProgress.value === 0   ? 'Pending'    :
    localProgress.value === 100 ? 'Completed'  : 'In_progress'
 
  isSaving.value = true
 
  router.patch(
    `/tasks/${props.task.id}/progress`,
    { progress: localProgress.value, status },
    {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        emit('updated', { id: props.task.id, progress: localProgress.value, status })
      },
      onError: (errors) => {
        console.error('Failed to update task:', errors)
        // Revert slider to last saved value on failure
        localProgress.value = props.task.progress ?? 0
      },
      onFinish: () => {
        isSaving.value = false
      },
    }
  )
}
console.log("Tasks:::::", props.task.progress);
const form=useForm({}); 
const updateTask=(Task)=>{
   
    if(!isEditingId.value) return 

    newTask.put(`/tasks/${isEditingId.value}`,{
        onSuccess: () => {
      alert('Task Updated successfully!')
      // Optional: reset form
      newTask.reset();
      updateModel.value=false
      props.show=false
    },
    onError: (errors) => {
      console.log(errors) // show validation errors
    }
    })
}
const editTasks=(task)=>{
    
    selectedTask.value=task
    console.log(selectedTask.value)
    const Due_date=due_date(selectedTask);
    console.log("Diff",Due_date);
    updateModel.value=true
    console.log("Task",task);
    isEditingId.value=task.id
    console.log("ID:", isEditingId.value)
    newTask.title=task.title || ''
    newTask.description=task.description || ''
    newTask.priority=task.priority
    newTask.due_date=Due_date || ''
    console.log("NewTask:::",newTask)
}
const openDeleteModal=(task)=>{
    selectedTask.value=task;
    deleteModel.value=true;
}
const deleteTask = () => {
   if (!selectedTask.value) return;

    form.delete(route('tasks.destroy', selectedTask.value.id), {
        onSuccess: () => {
            deleteModel.value=false;
            show.value=false
            alert("Task Deleted Successfully");
        }
    });
};
//Dynamic Styles 
const statusClass=(status)=>{
    if (status==='Completed') return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
    return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200'
}

const priorityClass=(priority)=>{
    if(priority==='High') return 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'
    if(priority==='Normal') return 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200'
    return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
}



const getStatus=()=>{
    if(progress.value===100) return 'Completed'
    if(progress.value > 0) return 'In-progress'
    return 'Pending'
}




const saveProgress=()=>{
    router.put(`/tasks/${props.task.id}`,{
    progress: progress.value,
    status: getStatus()},{
    preserveScroll:true})
}
const due_date=(task)=>{

    const due= new Date(task.due_date);
    const now=new Date();
    const diff=due-now;
    const days = diff / (1000 * 60 * 60 * 24);
    console.log("Diff",days);
    return days;
}


</script>
<template>
    <transition name="modal">
        <div v-if="show" @click.self="$emit('close')"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-white/10 backdrop-blur-sm px-4">
        
         <!-- Modal -->
            <div class="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all">

                <!-- Header -->
                 <div class="flex justify-between items-center p-5 border-b">
                    <h2 class="text-xl font-bold tracking-tight">{{ task?.title }}</h2>
                    <button @click="$emit('close')" class="text-gray-400 dark:text-gray-700 hover:text-black text-2xl transition hover:scale-x-100 hover:scale-y-100">
                         ✕
                    </button>
                 </div>

                 <!-- Body -->
                  <div class="p-5 space-y-5 overflow-t-auto">
                    <!-- Description-->
                     <p class="text-gray-600 dark:text-gray-200 font-semibold leading-relaxed">
                        {{ task?.description }}
                     </p>

                     <!-- Status+priority-->
                      <div class="flex flex-wrap gap-3">
                        <!-- Status Badge-->
                         <span class="px-3 py-1  rounded-full text-sm font-medium"
                         :class="statusClass(task?.status)">{{ task?.status }}</span>

                         <!-- Priority Badge -->
                          <span
                          class="px-3 py-1 rounded-full text-sm font-medium"
                          :class="priorityClass(task?.priority)">{{ task?.priority }}</span>
                      </div>

                      <!-- Due Date -->
                       <div class="text-sm text-gray-600 dark:text-gray-200">
                        ⏳ Due: <span class="font-medium text-gray-700 dark:text-gray-200"
                                :class="{'text-red-600': props.due}">{{ task?.due_date }}</span>
                       </div>
                          <div class="flex items-start justify-between gap-3 mb-5">
     
                        <span
                          class="flex-shrink-0 text-xs font-medium px-3 py-1 rounded-full transition-all duration-300"
                          :class="badgeClass"
                        >
                          {{ statusLabel }}
                        </span>
                            </div>
                       <!-- Progress Indicator -->
                       <div class="mb-2">
                          <div class="flex justify-between text-xs text-gray-300 mb-0.5">
                            <span>0%</span>
                            <span>50%</span>
                            <span>100%</span>
                          </div>
                      
                          <div class="h-2 rounded-full overflow-hidden">
                            <div
                              class="h-full rounded-full transition-all duration-150"
                              :style="{ width: localProgress + '%', backgroundColor: fillColor }"
                            />
                          </div>
                      
                          <input
                            type="range"
                            min="0"
                            max="100"
                            step="1"
                            v-model.number="localProgress"
                            @change="onDragEnd"
                            class="w-full mt-2 accent-blue-500 cursor-pointer"
                          />
                        </div>
                    
                        <!-- Progress % and hint -->
                        <div class="flex items-baseline justify-between mb-3">
                          <span
                            class="text-2xl font-bold transition-colors duration-300"
                            :style="{ color: fillColor }"
                          >
                            {{ localProgress }}%
                          </span>
                        </div>
                    </div>

                   <!-- Footer --> 
                  <div class="p-4 border-t flex justify-between items-center">
                    <div class="flex gap-2" >
                    <button v-if="props.isAdmin==='admin'" class="mt-4 sm:mt-0 sm:ml-4 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md transition" @click="editTasks(task)">Edit</button>
                    <button v-if="props.isAdmin==='admin'" class="mt-4 sm:mt-0 sm:ml-4 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md transition" @click="openDeleteModal(task)">Delete</button>
                    </div>
                    
                    <button
                    @click="$emit('close')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition ">
                    Close
                    </button>
                  </div>
            </div>
    
        </div>
    </transition>

    <!-- Update Task-->
    <transition name="modal" v-if="props.isAdmin==='admin'">
        <div v-if="updateModel" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 sm:p-0">
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded shadow-lg w-full max-w-md sm:max-w-lg relative overflow-auto max-h-[90vh]">
            <h2 class="text-lg sm:text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">UpdateTask</h2>
            <form @submit.prevent="updateTask" class="space-y-6">
              
                <div>
                    <label class="block mb-1 text-sm sm:text-base font-medium  text-gray-800 dark:text-gray-200 ">Title:</label>
                    <input v-model="newTask.title" placeholder="Enter Title" type="text" class="w-full px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                focus:shadow-indigo-400/30
                transition-all duration-200" required />
                </div>
                <div>
                    <label class="block mb-1 text-sm sm:text-base font-medium  text-gray-800 dark:text-gray-200">Description:</label>
                    <textarea v-model="newTask.description" placeholder="Write your description about the task..." class="w-full min-h-[80px] px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200" required />
                </div>
                <div>
                    <label class="block mb-1 text-sm sm:text-base font-medium  text-gray-800 dark:text-gray-200">Priority:</label>
                    <select v-model="newTask.priority" class="w-full px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200">
                        <option disabled value="">Select Priority</option>
                    <option value="Low">🟢 Low</option>
                    <option value="Normal">🟡 Normal</option>
                    <option value="High">🔴 High</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm sm:text-base font-medium  text-gray-800 dark:text-gray-200">Due_date:</label>
                    <input v-model="newTask.due_date" type="number"  class="w-full px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200" placeholder="Enter number days to complete the task" required />
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="updateModel=!updateModel" class="px-3 py-1 sm:px-4 sm:py-2 bg-gray-300 rounded dark:bg-gray-700 dark:hover:bg-gray-600 hover:bg-gray-400 text-sm sm:text-base">Cancel</button>
                    <button type="submit" class="px-3 py-1 sm:px-4 sm:py-2 bg-blue-600 dark:bg-blue-800 text-white  rounded hover:bg-blue-500 dark:hover:bg-blue-600 text-sm sm:text-base">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    </transition>
    <!-- Delete Task -->
    <transition name="modal">
            <div v-if="deleteModel" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 px-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-xl shadow-lg p-6">

        <!-- Title -->
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200  mb-4">
            Confirm Deletion
        </h2>

        <!-- Message -->
        <p class="text-gray-600 dark:text-gray-300 mb-6"
        >
            <span>Are you sure you want to delete </span> <span class="font-extrabold dark:text-gray-50 text-gray-800 "> {{selectedTask.title}}</span> <span> task ? This action cannot be undone.</span>
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            
            <button 
                @click="deleteModel=!deleteModel"
                class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-400 text-gray-700 hover:bg-gray-300 dark:hover:bg-gray-300 transition w-full sm:w-auto"
            >
                Cancel
            </button>

            <button 
                @click="deleteTask"
                
                class="px-4 py-2 rounded bg-red-500 dark:bg-red-700 dark:hover:bg-red-500 text-white hover:bg-red-600 transition w-full sm:w-auto"
            >
               
                <span >Delete</span>
            </button>

        </div>
    </div>
</div>
    </transition>
    
</template>

<style>
.modal-enter-active,
.modal-leave-active{
    transition: all 0.25 ease;
}

.modal-enter-from{
    opacity:0;
    transform: scale(0.9) translateY(20px);
}
.modal-leave-to{
    opacity:0;
    transform: scale(0.9) translateY(20px);
}
</style>