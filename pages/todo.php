<?php
// pages/todo.php — Todo Page (full separate page)
$activePage = 'todo';
$pageTitle  = 'Todo';
$pageLabel  = 'TO-DO';
include 'header.php';
?>
        <div class="todo-wrapper">
            <!-- ── PROGRESS BOX ── -->
            <div class="progress-box">
                <div class="progress-label" id="progress-label">Progression &nbsp; 0/0</div>
                <div class="progress">
                    <div class="progress-bar" id="progress-bar" style="width:0%"></div>
                </div>
            </div>

            <!-- ── ADD TASK ── -->
            <div class="input-bar">
                <input type="text" id="task-input" class="form-control"
                       placeholder="Write your task here…" maxlength="200" autocomplete="off">
                <button class="btn-plus" id="btn-add-task" title="Add task">+</button>
            </div>
        </div>
            <!-- ── TODO LIST ── -->
            <div class="card-wrapper">
                <img src="/image/peek-left.png" class="left-sticker" alt="Sticker">

            <div class="list-card" id="todo-list">
                <p class="empty-state">Loading tasks…</p>
            </div>
        </div>

<?php include 'footer.php'; ?>

<script>
// ─── TODO PAGE SCRIPT ───────────────────────────────────

const API = '../api/todos.php';

// Load all todos from API
async function loadTodos() {
    const res  = await fetch(API);
    const data = await res.json();
    const list = document.getElementById('todo-list');
    list.innerHTML = '';

    if (!data.length) {
        list.innerHTML = '<p class="empty-state">No tasks yet. Add one above! </p>';
        updateProgress(0, 0);
        return;
    }

    const done = data.filter(t => t.is_done == 1).length;
    updateProgress(done, data.length);

    data.forEach(todo => {
        const div = document.createElement('div');
        div.className = 'todo-item' + (todo.is_done == 1 ? ' done' : '');
        div.innerHTML = `
            <button class="btn-star" onclick="toggleTodo(${todo.id}, ${todo.is_done == 1 ? 0 : 1})" title="Toggle done">
                ${todo.is_done == 1 ? '★' : '☆'}
            </button>
            <span class="task-label">${esc(todo.task)}</span>
            <button class="btn-delete" onclick="deleteTodo(${todo.id})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        `;
        list.appendChild(div);
    });
}

// Update progress bar
function updateProgress(done, total) {
    const pct = total === 0 ? 0 : Math.round((done / total) * 100);
    document.getElementById('progress-bar').style.width  = pct + '%';
    document.getElementById('progress-label').innerHTML  = `Progression &nbsp; ${done}/${total}`;
}

// Add new task via POST
async function addTask() {
    const input = document.getElementById('task-input');
    const task  = input.value.trim();
    if (!task) return;
    await fetch(API, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ task })
    });
    input.value = '';
    loadTodos();
    showToast('Task added ✅');
}

// Toggle done via PUT
async function toggleTodo(id, newState) {
    await fetch(API, {
        method:  'PUT',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ id, is_done: newState })
    });
    loadTodos();
}

// Delete via DELETE
async function deleteTodo(id) {
    if (!confirm('Delete this task?')) return;
    await fetch(`${API}?id=${id}`, { method: 'DELETE' });
    loadTodos();
    showToast('Task deleted 🗑');
}

// Enter key support
document.getElementById('task-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') addTask();
});
document.getElementById('btn-add-task').addEventListener('click', addTask);

// Escape HTML helper
function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

// Toast helper
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

// Init
loadTodos();
</script>
</body>
</html>
