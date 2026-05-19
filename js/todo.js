// Todo List Application - Local Storage - MUSICYOS

class TodoApp {
    constructor() {
        this.todos = [];
        this.currentFilter = 'all';
        this.sortByPriority = false;
        this.storageKey = 'musicyos_todos';
        
        this.initElements();
        this.loadFromStorage();
        this.attachEventListeners();
        this.render();
    }
    
    initElements() {
        this.todoInput = document.getElementById('todoInput');
        this.addBtn = document.getElementById('addBtn');
        this.todoList = document.getElementById('todoList');
        this.emptyState = document.getElementById('emptyState');
        this.prioritySelect = document.getElementById('prioritySelect');
        this.categorySelect = document.getElementById('categorySelect');
        
        // Stats
        this.totalTasksEl = document.getElementById('totalTasks');
        this.completedTasksEl = document.getElementById('completedTasks');
        this.remainingTasksEl = document.getElementById('remainingTasks');
        this.completionPercentEl = document.getElementById('completionPercent');
        
        // Filter
        this.filterBtns = document.querySelectorAll('.filter-btn');
        this.sortBtn = document.getElementById('sortBtn');
        this.clearCompletedBtn = document.getElementById('clearCompletedBtn');
        
        // Footer
        this.exportBtn = document.getElementById('exportBtn');
        this.importBtn = document.getElementById('importBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.fileInput = document.getElementById('fileInput');
    }
    
    attachEventListeners() {
        // Input events
        this.addBtn.addEventListener('click', () => this.addTodo());
        this.todoInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.addTodo();
        });
        
        // Filter events
        this.filterBtns.forEach(btn => {
            btn.addEventListener('click', (e) => this.setFilter(e.target.dataset.filter));
        });
        
        // Sort and clear
        this.sortBtn.addEventListener('click', () => this.toggleSort());
        this.clearCompletedBtn.addEventListener('click', () => this.clearCompleted());
        
        // Footer events
        this.exportBtn.addEventListener('click', () => this.exportTodos());
        this.importBtn.addEventListener('click', () => this.fileInput.click());
        this.fileInput.addEventListener('change', (e) => this.importTodos(e));
        this.resetBtn.addEventListener('click', () => this.resetAll());
    }
    
    addTodo() {
        const text = this.todoInput.value.trim();
        
        if (!text) {
            this.showNotification('Please enter a task', 'warning');
            return;
        }
        
        const todo = {
            id: Date.now(),
            text: text,
            completed: false,
            priority: this.prioritySelect.value,
            category: this.categorySelect.value,
            createdAt: new Date().toLocaleString(),
            completedAt: null
        };
        
        this.todos.unshift(todo);
        this.saveToStorage();
        this.render();
        
        this.todoInput.value = '';
        this.todoInput.focus();
        this.showNotification('Task added successfully', 'success');
    }
    
    deleteTodo(id) {
        if (confirm('Are you sure you want to delete this task?')) {
            this.todos = this.todos.filter(todo => todo.id !== id);
            this.saveToStorage();
            this.render();
            this.showNotification('Task deleted', 'success');
        }
    }
    
    toggleTodo(id) {
        const todo = this.todos.find(t => t.id === id);
        if (todo) {
            todo.completed = !todo.completed;
            todo.completedAt = todo.completed ? new Date().toLocaleString() : null;
            this.saveToStorage();
            this.render();
        }
    }
    
    editTodo(id) {
        const todo = this.todos.find(t => t.id === id);
        if (!todo) return;
        
        const newText = prompt('Edit task:', todo.text);
        if (newText && newText.trim()) {
            todo.text = newText.trim();
            this.saveToStorage();
            this.render();
            this.showNotification('Task updated', 'success');
        }
    }
    
    setFilter(filter) {
        this.currentFilter = filter;
        
        this.filterBtns.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
        
        this.render();
    }
    
    toggleSort() {
        this.sortByPriority = !this.sortByPriority;
        this.sortBtn.style.opacity = this.sortByPriority ? '1' : '0.6';
        this.render();
    }
    
    getFilteredTodos() {
        let filtered = this.todos;
        
        // Apply filter
        if (this.currentFilter === 'active') {
            filtered = filtered.filter(todo => !todo.completed);
        } else if (this.currentFilter === 'completed') {
            filtered = filtered.filter(todo => todo.completed);
        }
        
        // Apply sort
        if (this.sortByPriority) {
            const priorityOrder = { high: 0, medium: 1, low: 2 };
            filtered.sort((a, b) => {
                if (a.completed === b.completed) {
                    return priorityOrder[a.priority] - priorityOrder[b.priority];
                }
                return a.completed - b.completed;
            });
        }
        
        return filtered;
    }
    
    updateStats() {
        const total = this.todos.length;
        const completed = this.todos.filter(t => t.completed).length;
        const remaining = total - completed;
        const percent = total === 0 ? 0 : Math.round((completed / total) * 100);
        
        this.totalTasksEl.textContent = total;
        this.completedTasksEl.textContent = completed;
        this.remainingTasksEl.textContent = remaining;
        this.completionPercentEl.textContent = percent + '%';
    }
    
    clearCompleted() {
        const completedCount = this.todos.filter(t => t.completed).length;
        
        if (completedCount === 0) {
            this.showNotification('No completed tasks to clear', 'info');
            return;
        }
        
        if (confirm(`Clear ${completedCount} completed task(s)?`)) {
            this.todos = this.todos.filter(t => !t.completed);
            this.saveToStorage();
            this.render();
            this.showNotification('Completed tasks cleared', 'success');
        }
    }
    
    exportTodos() {
        if (this.todos.length === 0) {
            this.showNotification('No tasks to export', 'warning');
            return;
        }
        
        const dataStr = JSON.stringify(this.todos, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `todos_${new Date().toISOString().split('T')[0]}.json`;
        link.click();
        URL.revokeObjectURL(url);
        
        this.showNotification('Tasks exported successfully', 'success');
    }
    
    importTodos(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const importedTodos = JSON.parse(e.target.result);
                
                if (!Array.isArray(importedTodos)) {
                    throw new Error('Invalid format');
                }
                
                if (confirm(`Import ${importedTodos.length} task(s)? This will add to existing tasks.`)) {
                    this.todos = [...importedTodos, ...this.todos];
                    this.saveToStorage();
                    this.render();
                    this.showNotification('Tasks imported successfully', 'success');
                }
            } catch (error) {
                this.showNotification('Error importing file', 'error');
            }
        };
        
        reader.readAsText(file);
        event.target.value = '';
    }
    
    resetAll() {
        if (confirm('Are you absolutely sure? This will delete ALL tasks permanently.')) {
            if (confirm('This action cannot be undone. Proceed?')) {
                this.todos = [];
                this.saveToStorage();
                this.render();
                this.showNotification('All tasks deleted', 'success');
            }
        }
    }
    
    render() {
        this.updateStats();
        
        const filteredTodos = this.getFilteredTodos();
        
        // Clear list
        this.todoList.innerHTML = '';
        
        // Show/hide empty state
        if (this.todos.length === 0) {
            this.emptyState.classList.add('visible');
        } else {
            this.emptyState.classList.remove('visible');
        }
        
        // If no filtered results but have todos
        if (filteredTodos.length === 0 && this.todos.length > 0) {
            const emptyMsg = document.createElement('div');
            emptyMsg.style.cssText = `
                text-align: center;
                padding: 40px;
                color: var(--text-secondary);
            `;
            emptyMsg.textContent = 'No tasks match this filter';
            this.todoList.appendChild(emptyMsg);
            return;
        }
        
        // Render todos
        filteredTodos.forEach(todo => {
            const li = this.createTodoElement(todo);
            this.todoList.appendChild(li);
        });
    }
    
    createTodoElement(todo) {
        const li = document.createElement('li');
        li.className = `todo-item ${todo.completed ? 'completed' : ''}`;
        li.dataset.id = todo.id;
        
        li.innerHTML = `
            <input 
                type="checkbox" 
                class="todo-checkbox" 
                ${todo.completed ? 'checked' : ''}
                aria-label="Toggle task completion"
            >
            <span class="priority-badge ${todo.priority}"></span>
            <div class="todo-content">
                <span class="todo-text">${this.escapeHtml(todo.text)}</span>
                <span class="todo-category">${todo.category}</span>
            </div>
            <span class="todo-date">${new Date(todo.createdAt).toLocaleDateString()}</span>
            <div class="todo-actions">
                <button class="todo-btn edit" title="Edit task">✏️</button>
                <button class="todo-btn delete" title="Delete task">🗑</button>
            </div>
        `;
        
        // Event listeners
        const checkbox = li.querySelector('.todo-checkbox');
        const editBtn = li.querySelector('.edit');
        const deleteBtn = li.querySelector('.delete');
        
        checkbox.addEventListener('change', () => this.toggleTodo(todo.id));
        editBtn.addEventListener('click', () => this.editTodo(todo.id));
        deleteBtn.addEventListener('click', () => this.deleteTodo(todo.id));
        
        return li;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.todos));
        } catch (error) {
            console.error('Error saving to storage:', error);
            this.showNotification('Error saving tasks', 'error');
        }
    }
    
    loadFromStorage() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                this.todos = JSON.parse(stored);
            }
        } catch (error) {
            console.error('Error loading from storage:', error);
            this.todos = [];
        }
    }
    
    showNotification(message, type = 'info') {
        // Simple notification using alert (you can enhance this with a custom toast)
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Create a temporary notification element
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#00d97e' : type === 'error' ? '#ff3860' : type === 'warning' ? '#ffa502' : '#00d4ff'};
            color: #000;
            border-radius: 8px;
            font-weight: 600;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        `;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(300px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(300px);
        }
    }
`;
document.head.appendChild(style);

// Initialize app on DOM load
document.addEventListener('DOMContentLoaded', () => {
    new TodoApp();
});
