<script setup>
//interface Tasks{
//    id:Number;
//    title:string;
//    description:String;
//    status: 'completed' | 'pending';
//    priority:'Low'|'Normal'|'High';
//    due_date:date
//}
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ref,
    onMounted,
    watch,
} from 'vue';
import {debounce} from 'lodash';
import { router } from '@inertiajs/vue3';
//import axios from 'axios';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
    tasks: Array,
    filters: Object
});
import TaskMenue from '@/Components/TaskMenue.vue';



const selectedTask=ref(null);   
const showModal = ref(false);
const isTaskMenu=ref(false);
const localTask=ref([]);
const due=ref(null);
watch(()=> props.tasks,(newTask)=>{
    localTask.value=newTask.map(task=>({
        ...task,
        isOpen:false
    }))
},{immediate:true});

const search = ref(props.filters.search || '')
const debouncedSearch = debounce((value) => {
    router.get('/tasks', { search: value }, {
        preserveState: true,
        replace: true,
        only: ['tasks', 'filters'],
    })
}, 200)

watch(search,(value)=>{
    debouncedSearch(value)
})
onMounted(() => {
    console.log("Props ", props.tasks);
});

console.log("user",props.tasks);

const isOverdue = (task) => {
    const due = new Date(task.due_date)
    const now = new Date() // current date/time
    return due < now && task.status==='Pending'
}
const form=useForm({});  

const newTask = useForm({
    title: '',
    description: '',
    priority: '',
    due_date: '',
    status: 'Pending'
});

const toggleDescription = (task) => {
    task.isOpen=!task.isOpen
    
};


const openTask = () => {
    showModal.value = true
}

const cancelTasks = () => {
    showModal.value = false
}


const createTask = () => {
   newTask.post('/tasks',{
    onSuccess: () => {
      alert('Task created successfully!')
      // Optional: reset form
      newTask.reset();
      showModal.value=false
    },
    onError: (errors) => {
      console.log(errors) // show validation errors
    }
  })
}


const taskMenue=(task)=>{
    isTaskMenu.value=true
    console.log("Task", task)
    selectedTask.value=task
    console.log("Selected", selectedTask.value)
    due.value=isOverdue(selectedTask.value);
    console.log("Due", due);
}

const closeModal=()=>{
    isTaskMenu.value=false;
}
</script>
<template>
<AuthenticatedLayout>
    <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Task
        </h2>
    </template>
    <div class="min-h-screen 
    bg-gray-50 dark:bg-gray-900 
    text-gray-800 dark:text-gray-100 mt-2
    py-8 transition-colors duration-300">
    <div 
    :class="{'blur-sm':showModal}" 
   class="max-w-7xl xl:max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-10 py-8
bg-white/80 dark:bg-gray-800/80
backdrop-blur-md
rounded-2xl
shadow-xl dark:shadow-black/40
transition-all duration-300">
        <!-- Header with title -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md
        border border-gray-200 dark:border-gray-700
        shadow-md rounded-xl px-6 py-4 flex justify-between items-center mb-6
        transition-all duration-300">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Task Lists</h1>
            <button @click="openTask()" 
            class="bg-indigo-600 hover:bg-indigo-700 
          dark:bg-indigo-500 dark:hover:bg-indigo-400
          text-white font-semibold px-4 py-2 rounded-lg
            shadow-md hover:shadow-indigo-500/40
            transition-all duration-200 hover:scale-105 active:scale-95" type="button">
                +Add
            </button>
        </div>

        <!-- Task List Container -->
        <div 
        class="bg-white/80 dark:bg-gray-800/80 
        backdrop-blur-md 
        shadow-lg shadow-gray-300/40 dark:shadow-black/40
        border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <!-- Search Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <input type="search" v-model="search" placeholder="Search Tasks..." 
                class="bg-white dark:bg-gray-800
              text-gray-800 dark:text-gray-100
                border border-gray-300 dark:border-gray-600
                rounded-lg px-4 py-2
                shadow-sm focus:shadow-indigo-400/40
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200">
                <select class="sm:w-1/4 
              bg-white dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                rounded-lg px-4 py-2
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200">
                    <option disabled value="">Filter</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In-progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <!-- Tasks List-->
            <div v-for="task in localTask" :key="task.id" class="spacey-4 mb-2">
                <!-- Task Item -->
                <div 
                class="border border-gray-200 dark:border-gray-700
              bg-white dark:bg-gray-800
                rounded-xl p-4
                flex flex-col sm:flex-row sm:items-center sm:justify-between
                transition-all duration-300
                hover:shadow-lg hover:-translate-y-1
              hover:bg-indigo-50 dark:hover:bg-gray-700">
                    <div class="flex flex-col sm:flex-row m:items-center sm:space-x-6 flex-1">
                        <button class="text-lg font-semibold text-gray-800 dark:text-gray-100 transition" @click="taskMenue(task)"
                        :class="{'line-through':task.status==='Completed'}">{{ task.title }}</button>

                        <div class="mt-2 sm:mt-0 flex flex-wrap gap-4 text-sm text-gray-600">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm" 
                            :class="{
                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': task.priority==='Low',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': task.priority==='Normal',
                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': task.priority==='High'}">{{ task.priority }}</span>
                            
                            <span class=" px-2 py-1  shadow-md shadow-blue-600/50 rounded-full border  hover:border-shadow-lg transition duration-200"
                            :class="{
                              'bg-blue-200 text-blue-700 dark:bg-blue-700 dark:text-blue-300': task.status==='Pending',
                              'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': task.status==='Completed'
                            }">{{ task.status }}</span>
                            
                            <span class="px-3 py-1 rounded-full text-xs font-medium border transition-all"
                            :class="{
                              'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300': !isOverdue(task),
                              'bg-red-100 text-red-600 border-red-400 dark:bg-red-900 dark:text-red-300 animate-pulse': isOverdue(task)}">{{ task.due_date }}</span>
                        </div>
                    </div>
                    <!---->
                    <button class="mt-4 sm:mt-0 sm:ml-4 
                  bg-indigo-600 dark:bg-indigo-500
                  hover:bg-indigo-700 dark:hover:bg-indigo-400
                  text-white px-3 py-1 rounded-lg
                    transition-all duration-200 
                    hover:scale-105 active:scale-95" @click="toggleDescription(task)">
                        <!-- Arrow Icon -->
                        <svg  :class="{'transform rotate-180': task.isOpen}" class="w-5 h-5 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                        
                    </button>
                </div>
                <Transition :duration="550" name="nested">
                    <div v-if="task.isOpen" class="bg-indigo-50 text-indigo-900 
                  dark:bg-indigo-900/40 dark:text-indigo-200
                    border border-indigo-200 dark:border-indigo-700
                    mb-2 px-6 py-4 rounded-b-xl
                    transition-all duration-300">
                        {{task.description}}
                    </div>
                </Transition>
            </div>
        </div>
        <div class="flex justify-end">
            <button 
        @click="search=''; statusFilters=''" 
        class="px-3 py-1 mt-3 bg-gray-300 dark:bg-gray-700 rounded hover:bg-gray-400 dark:hover:bg-gray-600 text-sm sm:text-base transition">
        Reset Filters
        </button>
        </div>
        
    </div>
  </div>

<transition name="fade">
   <div v-if="showModal"  
@click.self="cancelTasks"
class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">

    <!-- Modal Box -->
    <div class="bg-white dark:bg-gray-900 
    w-full max-w-lg rounded-2xl 
    shadow-2xl dark:shadow-black/50
    border border-gray-200 dark:border-gray-700
    p-6 sm:p-8
    transition-all duration-300
    scale-100">

        <!-- Header -->
        <h2 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-100">
            Add New Task
        </h2>

        <!-- Form -->
        <form @submit.prevent="createTask" class="space-y-5">

            <!-- Title -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Title
                </label>
                <input 
                v-model="newTask.title"
                type="text"
                placeholder="Enter task title..."
                class="w-full px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                focus:shadow-indigo-400/30
                transition-all duration-200"
                required />
            </div>

            <!-- Description -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Description
                </label>
                <textarea 
                v-model="newTask.description"
                placeholder="Write something about this task..."
                class="w-full min-h-[80px] px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200"
                required />
            </div>

            <!-- Priority -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Priority
                </label>
                <select 
                v-model="newTask.priority"
                class="w-full px-3 py-2 rounded-lg
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

            <!-- Due Date -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Due Days
                </label>
                <input 
                v-model="newTask.due_date"
                type="number"
                placeholder="Enter number of days..."
                class="w-full px-3 py-2 rounded-lg
                bg-gray-50 dark:bg-gray-800
                border border-gray-300 dark:border-gray-600
                text-gray-800 dark:text-gray-100
                focus:ring-2 focus:ring-indigo-500
                transition-all duration-200"
                required />
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4">

                <!-- Cancel -->
                <button 
                type="button" 
                @click="cancelTasks"
                class="px-4 py-2 rounded-lg
                bg-gray-200 hover:bg-gray-300
                dark:bg-gray-700 dark:hover:bg-gray-600
                text-gray-800 dark:text-gray-200
                transition-all duration-200
                hover:scale-105 active:scale-95">
                    Cancel
                </button>

                <!-- Create -->
                <button 
                type="submit"
                class="px-4 py-2 rounded-lg
                bg-indigo-600 hover:bg-indigo-700
                dark:bg-indigo-500 dark:hover:bg-indigo-400
                text-white font-semibold
                shadow-md hover:shadow-indigo-500/40
                transition-all duration-200
                hover:scale-105 active:scale-95">
                    Create Task
                </button>

            </div>

        </form>
    </div>
</div>
</transition>


<TaskMenue 
:show="isTaskMenu"
:task="selectedTask"
@close="closeModal"
:due="due"
/>
</AuthenticatedLayout>



</template>

<style scoped>
.nested-enter-active,
.nested-leave-active {
    transition: all 0.3s ease-in-out;
}

.nested-leave-active {
    transition-delay: 0.05s;
}

.nested-enter-from,
.nested-leave-to {
    transform: translateY(30px);
    opacity: 0;
}
* {
  transition: background-color 0.3s ease, 
              color 0.3s ease, 
              border-color 0.3s ease,
              box-shadow 0.3s ease;
}
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.25s ease;
}

.fade-scale-enter-from {
  opacity: 0;
  transform: scale(0.9);
}

.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
