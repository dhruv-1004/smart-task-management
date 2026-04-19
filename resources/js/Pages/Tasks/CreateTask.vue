<template>
  <transition name="modal">
    <div
      v-if="show"
      @click.self="$emit('close')"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
    >
      <!-- Modal -->
      <div
        class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all"
      >
        <!-- Header -->
        <div class="flex justify-between items-center p-5 border-b">
          <h2 class="text-xl font-bold tracking-tight">
            {{ task?.title }}
          </h2>

          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-black text-2xl transition"
          >
            ✕
          </button>
        </div>

        <!-- Body -->
        <div class="p-5 space-y-5 overflow-y-auto">
          <!-- Description -->
          <p class="text-gray-600 leading-relaxed">
            {{ task?.description }}
          </p>

          <!-- Status + Priority -->
          <div class="flex flex-wrap gap-3">
            <!-- Status Badge -->
            <span
              class="px-3 py-1 rounded-full text-sm font-medium"
              :class="statusClass(task?.status)"
            >
              {{ task?.status }}
            </span>

            <!-- Priority Badge -->
            <span
              class="px-3 py-1 rounded-full text-sm font-medium"
              :class="priorityClass(task?.priority)"
            >
              {{ task?.priority }} Priority
            </span>
          </div>

          <!-- Due Date -->
          <div class="text-sm text-gray-500">
            ⏳ Due: <span class="font-medium text-gray-700">{{ task?.due_date }}</span>
          </div>

          <!-- Progress Indicator -->
          <div>
            <p class="text-sm font-medium mb-1">Progress</p>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="h-2 rounded-full transition-all duration-500"
                :class="progressColor(task?.status)"
                :style="{ width: progressWidth(task?.status) }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t flex justify-between items-center">
          <button
            class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition"
          >
            Edit
          </button>

          <button
            @click="$emit('close')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

  

<script setup>
defineProps({
  show: Boolean,
  task: Object
})

// 🎨 Dynamic Styles
const statusClass = (status) => {
  if (status === 'completed') return 'bg-green-100 text-green-700'
  if (status === 'overdue') return 'bg-red-100 text-red-700'
  return 'bg-yellow-100 text-yellow-700'
}

const priorityClass = (priority) => {
  if (priority === 'high') return 'bg-red-100 text-red-700'
  if (priority === 'medium') return 'bg-orange-100 text-orange-700'
  return 'bg-green-100 text-green-700'
}

// 📊 Fake progress logic (you can improve later)
const progressWidth = (status) => {
  if (status === 'completed') return '100%'
  if (status === 'overdue') return '80%'
  return '40%'
}

const progressColor = (status) => {
  if (status === 'completed') return 'bg-green-500'
  if (status === 'overdue') return 'bg-red-500'
  return 'bg-yellow-500'
}
</script>

<style>
/* ✨ Animation */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.25s ease;
}

.modal-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}

.modal-leave-to {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}
</style>  