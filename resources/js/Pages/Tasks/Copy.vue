<template>
    <div v-if="!isActive && !isCompleted && !isDue">
            <!-- Task List -->
            <div class="space-y-3" v-for="task in localTasks" :key="task.id">

                <!-- Task Item -->
                <div class="group relative flex items-center justify-between p-4 rounded-xl 
                bg-gray-50 dark:bg-gray-800/50 
                border border-gray-200 dark:border-gray-700
                hover:shadow-lg hover:-translate-y-0.5
                hover:bg-white dark:hover:bg-gray-700
                transition-all duration-300 overflow-hidden">

                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300
                bg-gradient-to-r from-blue-500/5 via-transparent to-purple-500/5"></div>
                    <div class="flex items-start gap-3 relative z-10">
                        <input type="checkbox" 
                        :checked="task.status === 'Completed'" 
                        @click.prevent="confirmComplete(task)"
                        class="w-5 h-5 accent-green-500 cursor-pointer 
                        transition duration-200 hover:scale-110 active:scale-95">

                        <div :class="{ 'line-through text-gray-400 opacity-50': task.status === 'Completed' }">
                            <p class="  text-sm font-semibold text-gray-800 dark:text-gray-100 
                            group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ task.title }}</p>
                            <p class="text-xs text-gray-500 w-13" :class="{'text-red-600 font-semibold animate-pulse': isOverdue(task)}">{{ task.due_date }}</p>
                        </div>
                    </div>
                    <div class="lex items-center gap-2 relative z-10">
                        <span v-if="isOverdue(task)" class="text-xs px-2 py-1 rounded-full 
                        bg-red-100 text-red-600 
                        dark:bg-red-900/30 dark:text-red-400
                        animate-pulse">
                            Overdue
                        </span>

                        <span class="text-xs px-3 py-1 rounded-full font-medium 
                        transition-all duration-300"
                        :class="task.status === 'Completed' 
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 scale-105' 
                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 group-hover:scale-105'">

                            {{ task.status }}
                        </span>
                    </div>
                </div>
            </div>
</template>