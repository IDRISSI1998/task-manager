<template>
    <div class="container mx-auto p-6 bg-gray-100 min-h-screen rounded shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">Task Manager</h1>

        <!-- Feedback Messages -->
        <div class="mb-4">
            <p v-if="successMessage" class="text-green-500 font-bold text-center">
                {{ successMessage }}
            </p>
            <p v-if="errorMessage" class="text-red-500 font-bold text-center">
                {{ errorMessage }}
            </p>
        </div>

        <!-- Add New Task -->
        <div class="flex justify-center mb-6">
            <input
                v-model="newTask"
                type="text"
                class="border border-gray-300 rounded p-2 w-2/3 md:w-1/2"
                placeholder="Enter a new task"
            />
            <button
                @click="addTask"
                class="ml-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >
                Add Task
            </button>
        </div>

        <!-- Search Bar -->
        <div class="flex justify-center mb-6">
            <input
                v-model="searchQuery"
                type="text"
                class="border border-gray-300 rounded p-2 w-2/3 md:w-1/2"
                placeholder="Search tasks..."
            />
        </div>

        <!-- Filters -->
        <div class="flex justify-center mb-4 space-x-4">
            <button
                @click="filterStatus = 'all'"
                class="px-4 py-2 rounded"
                :class="filterStatus === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-300'"
            >
                All
            </button>
            <button
                @click="filterStatus = 'completed'"
                class="px-4 py-2 rounded"
                :class="filterStatus === 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-300'"
            >
                Completed
            </button>
            <button
                @click="filterStatus = 'incomplete'"
                class="px-4 py-2 rounded"
                :class="filterStatus === 'incomplete' ? 'bg-blue-500 text-white' : 'bg-gray-300'"
            >
                Incomplete
            </button>
        </div>

        <!-- Loading Indicator -->
        <div v-if="loading" class="text-center text-gray-500 mb-4">
            <p>Loading...</p>
        </div>

        <!-- Task List -->
        <div v-if="tasks.length" class="bg-white p-4 rounded shadow-md">
            <ul>
                <li
                    v-for="task in filteredTasks"
                    :key="task.id"
                    class="flex justify-between items-center py-2 border-b border-gray-200"
                >
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            v-model="task.completed"
                            @change="toggleTask(task.id, task.completed)"
                            class="mr-3"
                        />
                        <span
                            :class="{ 'line-through text-gray-500': task.completed }"
                            class="text-lg"
                        >
                            {{ task.title }}
                        </span>
                    </div>
                    <button
                        @click="deleteTask(task.id)"
                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                    >
                        Delete
                    </button>
                </li>
            </ul>
        </div>
        <p v-else class="text-center text-gray-600">No tasks to display. Add one above!</p>
    </div>
</template>


<script>
    import axios from 'axios';

    export default {
    name: 'TaskManager',
    data() {
        return {
            tasks: [], // All tasks fetched from the backend
            newTask: '', // New task input value
            searchQuery: '', // Search query input value
            successMessage: '', // Success feedback message
            errorMessage: '', // Error feedback message
            filterStatus: 'all', // Current filter: 'all', 'completed', or 'incomplete'
            loading: false, // Tracks loading state
        };
    },
    computed: {
        // Filter tasks based on the search query
        filteredTasks() {
            return this.tasks.filter(task => {
                if (this.filterStatus === 'completed') return task.completed;
                if (this.filterStatus === 'incomplete') return !task.completed;
                return true; // 'all'
            }).filter(task =>
                task.title.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
    },
    mounted() {
        this.fetchTasks(); // Fetch all tasks on component load
    },
    methods: {
        fetchTasks() {
            this.loading = true; // Start loading
            axios.get('/tasks')
                .then(response => {
                    this.tasks = response.data;
                })
                .catch(error => {
                    if (error.response) {
                        this.errorMessage = `Error: ${error.response.data.message}`;
                    } else {
                        this.errorMessage = 'Network error!';
                    }
                    setTimeout(() => (this.errorMessage = ''), 3000);
                })
                .finally(() => {
                    this.loading = false; // Stop loading
                });
        },
        addTask() {
            if (this.newTask.trim() === '') {
                this.errorMessage = 'Task title cannot be empty!';
                setTimeout(() => (this.errorMessage = ''), 3000);
                return;
            }
            this.loading = true; // Start loading
            axios.post('/tasks', { title: this.newTask })
                .then(response => {
                    this.tasks.push(response.data);
                    this.newTask = '';
                    this.successMessage = 'Task added successfully!';
                    setTimeout(() => (this.successMessage = ''), 3000);
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        // Extract validation errors
                        const errors = error.response.data.errors;
                        this.errorMessage = Object.values(errors).flat().join(' ');
                    } else {
                        this.errorMessage = 'An unexpected error occurred!';
                    }
                    setTimeout(() => (this.errorMessage = ''), 5000);
                })
                .finally(() => {
                    this.loading = false; // Stop loading
                });
        },
        toggleTask(id, completed) {
            axios.put(`/tasks/${id}`, { completed })
                .then(() => {
                    const task = this.tasks.find(t => t.id === id);
                    if (task) task.completed = completed;
                })
                .catch(() => {
                    this.errorMessage = 'Error toggling task!';
                    setTimeout(() => (this.errorMessage = ''), 3000);
                });
        },
        deleteTask(id) {
            axios.delete(`/tasks/${id}`)
                .then(() => {
                    this.tasks = this.tasks.filter(t => t.id !== id);
                    this.successMessage = 'Task deleted successfully!';
                    setTimeout(() => (this.successMessage = ''), 3000);
                })
                .catch(() => {
                    this.errorMessage = 'Error deleting task!';
                    setTimeout(() => (this.errorMessage = ''), 3000);
                });
            },
        },
    };
</script>

    <style scoped>
    .line-through {
        text-decoration: line-through;
    }
</style>
