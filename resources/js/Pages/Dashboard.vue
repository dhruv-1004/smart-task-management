<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Head,
    router
} from "@inertiajs/vue3";
import {
    ref,
    computed,
    onMounted
} from "vue";

//Define props
const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const localTasks = ref([...props.tasks]);

//Calculate Over_due
const isOverdue = (task) => {
    const due = new Date(task.due_date);
    const now = new Date(); // current date/time
    return due < now && task.status !== "Completed";
};

//filtering Tasks based on status
const overdueTasks = computed(() => props.tasks.filter(isOverdue));
const completedTasks = computed(() =>
    props.tasks.filter((task) => task.status === "Completed"),
);
const pendingTasks = computed(() =>
    props.tasks.filter((task) => task.status === "Pending"),
);

const viewALL = ref(false);
const showModal = ref(false);
const selectedTask = ref(null);
const isActive = ref(false);
const isCompleted = ref(false);
const isDue = ref(false);

const activeModal = ref([]);
const completedModal = ref([]);
const overDueModal = ref([]);

const confirmComplete = (task) => {
    selectedTask.value = task;
    showModal.value = true;
};
const completeTask = () => {
    router.post(
        `/tasks/${selectedTask.value.id}/complete`, {}, {
            onSuccess: () => {
                showModal.value = false;
            },
        },
    );
};

//Logic for All Task Button
const viewTask=()=>{
    viewALL.value=true;
  isActive.value=false;
  isCompleted.value=false;
  isDue.value=false;
}

// Logic for Active task Button

const activeTask = () => {
    isActive.value = true;
    viewALL.value=false;
    isCompleted.value = false;
    isDue.value = false;
    activeModal.value = pendingTasks.value;
};

// Logic to display Completed task Button
const completedTask = () => {
    isCompleted.value = true;
    viewALL.value=false;
    isActive.value = false;
    isDue.value = false;
    completedModal.value = completedTasks.value;
};

// Logic for Over_Due task Button
const dueTask = () => {
    isActive.value = false;
    viewALL.value=false;
    isCompleted.value = false;
    isDue.value = true;
    overDueModal.value = overdueTasks.value;
};
onMounted(()=>{
    viewTask();
})
</script>

<template>
<Head title="Dashboard" />

<AuthenticatedLayout>
    <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-50">
            Dashboard
        </h2>
    </template>

    <div class="p-4 sm:p-6 min-h-screen mt-3 bg-gray-100 dark:bg-gray-900 transition-colors duration-500 ease-in-out">
        <!-- Heading -->
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-50 mb-6">
            Task Overview
        </h1>

        <!-- Cards Container -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Active Tasks Card -->
            <button @click="activeTask" class="group bg-white dark:bg-gray-800 text-left rounded-2xl shadow-md flex items-center justify-between overflow-hidden border border-transparent hover:shadow-xl hover:-translate-y-1 dark:hover:bg-gray-700 dark:hover:border-blue-500 transition-all duration-300">
                <!-- shadow-[0_0_6px_#3b82f6,0_0_12px_#3b82f6,0_0_20px_#3b82f6] -->
                <div class="w-1.5 h-36 bg-blue-500 group-hover:bg-gradient-to-b group-hover:from-blue-400 group-hover:to-blue-600 transition-all duration-300"></div>
                <div class="flex flex-col p-4 w-full">
                    <h2 class="text-gray-500 dark:text-gray-400 text-sm">
                        Active Tasks
                    </h2>
                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ pendingTasks.length }}
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/15 text-blue-600 dark:text-blue-400 mr-3 p-3 rounded-full group-hover:scale-110 group-hover:rotate-6 transition duration-300">
                    📋
                </div>
            </button>

            <!-- Completed Tasks Card -->
            <!-- shadow-[0_0_6px_#22c55e,0_0_12px_#22c55e,0_0_20px_#22c55e] -->
            <button @click="completedTask" class="group bg-white dark:bg-gray-800 text-left rounded-2xl shadow-md flex items-center justify-between overflow-hidden border border-transparent hover:shadow-xl hover:-translate-y-1 dark:hover:bg-gray-700 dark:hover:border-green-500 transition-all duration-300" >
                <div class="w-1.5 h-36 bg-green-500 group-hover:bg-gradient-to-b group-hover:from-green-400 group-hover:to-green-600 transition-all duration-300"></div>
                <div class="flex flex-col p-4 w-full">
                    <h2 class="text-gray-500 dark:text-gray-400 text-sm">
                        Completed Tasks
                    </h2>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        {{ completedTasks.length }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/15 text-green-600 dark:text-green-400 mr-3 p-3 rounded-full group-hover:scale-110 group-hover:rotate-6 transition duration-300">
                    ✅
                </div>
            </button>

            <!-- Overdue Tasks Card -->
            <!--  shadow-[0_0_6px_#ef4444,0_0_12px_#ef4444,0_0_20px_#ef4444] -->
            <button @click="dueTask" class="group bg-white dark:bg-gray-800 text-left rounded-2xl shadow-md flex items-center justify-between overflow-hidden border border-transparent hover:shadow-xl hover:-translate-y-1 dark:hover:bg-gray-700 dark:hover:border-red-500 transition-all duration-300" >
                <div class="w-1.5 h-36 bg-red-500 group-hover:bg-gradient-to-b group-hover:from-red-400 group-hover:to-red-600 transition-all duration-300"></div>
                <div class="flex flex-col p-4 w-full">
                    <h2 class="text-gray-500 dark:text-gray-400 text-sm">
                        Overdue Tasks
                    </h2>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        {{ overdueTasks.length }}
                    </p>
                </div>
                <div class="bg-red-100 dark:bg-red-900/15 text-red-600 dark:text-red-400 mr-3 p-3 rounded-full group-hover:scale-110 group-hover:rotate-6 transition duration-300">
                    ⚠️
                </div>
            </button>
        </div>

        <!-- Display Tasks-->
        <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 p-5 w-full mt-10">
            <!-- Header -->
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 tracking-wide flex items-center gap-2">
                    <span class="text-blue-500">📋</span>
                    My Tasks
                </h2>
                <button @click="viewTask()" class="text-sm text-blue-600 hover:underline">
                    View All
                </button>
                </div>

                <!-- All Tasks -->
                <div v-if="viewALL">
                    <!-- Task List -->
                    <div class="space-y-3" v-for="task in localTasks" :key="task.id">
                        <!-- Task Item -->
                        <div class="group relative flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:-translate-y-0.5 hover:bg-white dark:hover:bg-gray-700 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300 bg-gradient-to-r from-blue-500/5 via-transparent to-purple-500/5"></div>
                            <div class="flex items-start gap-3 relative z-10">
                                <input type="checkbox" :checked="task.status === 'Completed'" @click.prevent="confirmComplete(task)" class="w-5 h-5 accent-green-500 cursor-pointer transition duration-200 hover:scale-110 active:scale-95" />

                                <div :class="{
                                            'line-through text-gray-400 opacity-50':
                                                task.status === 'Completed',
                                        }">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                        {{ task.title }}
                                    </p>
                                    <p class="text-xs text-gray-500 w-13" :class="{
                                                'text-red-600 font-semibold animate-pulse':
                                                    isOverdue(task),
                                            }">
                                        {{ task.due_date }}
                                    </p>
                                </div>
                            </div>
                            <div class="lex items-center gap-2 relative z-10">
                                <span v-if="isOverdue(task)" class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                    Overdue
                                </span>

                                <span class="text-xs px-3 py-1 rounded-full font-medium transition-all duration-300" :class="
                                            task.status === 'Completed'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 scale-105'
                                                : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 group-hover:scale-105'
                                        ">
                                    {{ task.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            

            <!-- Header -->

            <!-- Display Active Tasks-->
            <div v-if="isActive">
                <!-- Task List -->
                <div class="space-y-3" v-for="task in activeModal" :key="task.id">
                    <!-- Task Item -->
                    <div class="group relative flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:-translate-y-0.5 hover:bg-white dark:hover:bg-gray-700 transition-all duration-300 overflow-hidden">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300 bg-gradient-to-r from-green-500/5 via-transparent to-emerald-500/5"></div>

                        <div class="flex items-start gap-3 relative z-10">
                            <input type="checkbox" :checked="task.status === 'Completed'" @click.prevent="confirmComplete(task)" class="w-5 h-5 accent-green-500 cursor-pointer transition duration-200 hover:scale-110 active:scale-95" />

                            <div :class="{
                                        'line-through text-gray-400 opacity-50':
                                            task.status === 'Completed',
                                    }">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                    {{ task.title }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" :class="{
                                            'text-red-500 font-semibold animate-pulse':
                                                isOverdue(task),
                                        }">
                                    {{ task.due_date }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 relative z-10">
                            <span v-if="isOverdue(task)" class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                Overdue
                            </span>

                            <span class="text-xs px-3 py-1 rounded-full font-medium transition-all duration-300" :class="
                                        task.status === 'Completed'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 scale-105'
                                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 group-hover:scale-105'
                                    ">
                                {{ task.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display Completed Tasks-->
            <div v-if="isCompleted">
                <div class="space-y-3" v-for="task in completedModal" :key="task.id">
                    <!-- Task Item -->
                    <div class="group relative flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:-translate-y-0.5 hover:bg-white dark:hover:bg-gray-700 transition-all duration-300 overflow-hidden">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300 bg-gradient-to-r from-green-500/5 via-transparent to-emerald-500/5"></div>
                        <div class="flex items-start gap-3 relative z-10">
                            <input type="checkbox" :checked="task.status === 'Completed'" @click.prevent="confirmComplete(task)" class="w-5 h-5 accent-green-500 cursor-pointer transition duration-200 hover:scale-110 active:scale-95" />

                            <div :class="{
                                        'line-through text-gray-400 opacity-50':
                                            task.status === 'Completed',
                                    }">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition">
                                    {{ task.title }}
                                </p>

                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" :class="{
                                            'text-red-500 font-semibold animate-pulse':
                                                isOverdue(task),
                                        }">
                                    {{ task.due_date }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 relative z-10">
                            <span v-if="isOverdue(task)" class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                Overdue
                            </span>

                            <span class="text-xs px-3 py-1 rounded-full font-medium transition-all duration-300" :class="
                                        task.status === 'Completed'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 scale-105'
                                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                                    ">
                                {{ task.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display OverDue Tasks-->
            <div v-if="isDue">
                <div class="space-y-3" v-for="task in overDueModal" :key="task.id">
                    <!-- Task Item -->
                    <div class="group relative flex items-center justify-between p-4 rounded-xl 
                    bg-gray-50 dark:bg-gray-800/50 
                    border border-gray-200 dark:border-gray-700
                    hover:shadow-lg hover:-translate-y-0.5
                    hover:bg-white dark:hover:bg-gray-700
                    transition-all duration-300 overflow-hidden">

                        <!-- 🔴 Hover Glow -->
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300
                        bg-gradient-to-r from-red-500/5 via-transparent to-pink-500/5"></div>

                        <!-- LEFT -->
                        <div class="flex items-start gap-3 relative z-10">
                        
                            <!-- Checkbox -->
                            <input 
                                type="checkbox" 
                                :checked="task.status === 'Completed'" 
                                @click.prevent="confirmComplete(task)"
                                class="w-5 h-5 accent-green-500 cursor-pointer 
                                transition duration-200 hover:scale-110 active:scale-95">
                        
                            <!-- Text -->
                            <div :class="{ 'line-through text-gray-400 opacity-50': task.status === 'Completed' }">

                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 
                                group-hover:text-red-600 dark:group-hover:text-red-400 transition">
                                    {{ task.title }}
                                </p>
                            
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                   :class="{'text-red-500 font-semibold animate-pulse': isOverdue(task)}">
                                    {{ task.due_date }}
                                </p>
                            </div>
                        </div>

                        <!-- RIGHT -->
                        <div class="flex items-center gap-2 relative z-10">
                        
                            <!-- Overdue Badge -->
                            <span 
                                v-if="isOverdue(task)" 
                                class="text-xs px-2 py-1 rounded-full 
                                bg-red-100 text-red-600 
                                dark:bg-red-900/30 dark:text-red-400
                                animate-pulse">
                                Overdue
                            </span>
                        
                            <!-- Status -->
                            <span 
                                class="text-xs px-3 py-1 rounded-full font-medium 
                                transition-all duration-300"
                                :class="task.status === 'Completed' 
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 scale-105' 
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 group-hover:scale-105'">

                                {{ task.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Confirmation For Markdown to complete-->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-80 scale-95 animate-[fadeIn_.2s_ease-out_forwards]">
                <h2 class="text-lg font-semibold mb-3">
                    Mark as Completed?
                </h2>

                <div class="flex justify-end gap-3 mt-4">
                    <button @click="showModal = false">Cancel</button>
                    <button @click="completeTask" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>
