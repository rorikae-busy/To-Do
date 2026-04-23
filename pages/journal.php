<?php
// pages/journal.php — Journal Page (full separate page)
$activePage = 'journal';
$pageTitle  = 'Journal';
$pageLabel  = 'JOURNAL';
include 'header.php';
?>
        <div class="todo-wrapper">
            <!-- ── ADD JOURNAL ── -->
            <div class="container-wrapper">
                <p>Write a journal today…</p>
                <button class="btn-plus" id="btn-add-journal" title="Add journal">+</button>
            </div>
        </div>
            <!-- ── SECTION LABEL ── -->
            <div class="section-heading">My recent journals</div>

            <!-- ── JOURNAL LIST ── -->
            <div class="list-card" id="journal-list">
                <p class="empty-state">Loading journals…</p>
            </div>

<?php include 'footer.php'; ?>

<!-- ── ADD / EDIT MODAL ── -->
<div class="modal fade" id="entryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">New Journal Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-3">
                <input type="text" id="modal-entry-title" class="form-control"
                       placeholder="Title…" maxlength="200">
                <textarea id="modal-entry-content" class="form-control"
                          placeholder="Write your journal here…"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-modal-save"   onclick="saveJournal()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- ── VIEW MODAL ── -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-2">
                <div class="view-date-label" id="view-date"></div>
                <div class="view-content-box" id="view-content"></div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// ─── JOURNAL PAGE SCRIPT ────────────────────────────────

const API = '../api/journals.php';
const entryModal = new bootstrap.Modal(document.getElementById('entryModal'));
const viewModal  = new bootstrap.Modal(document.getElementById('viewModal'));

// Load all journals
async function loadJournals() {
    const res  = await fetch(API);
    const data = await res.json();
    const list = document.getElementById('journal-list');
    list.innerHTML = '';

    if (!data.length) {
        list.innerHTML = '<p class="empty-state">No journals yet. Start writing!</p>';
        return;
    }

    data.forEach(j => {
        const div = document.createElement('div');
        div.className = 'entry-item';
        div.innerHTML = `
            <span class="star-icon">★</span>
            <span class="entry-title" onclick="viewJournal(${j.id})">${esc(j.title)}</span>
            <span class="entry-date">${fmtDate(j.created_at)}</span>
            <button class="btn-delete" onclick="deleteJournal(event, ${j.id})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        `;
        list.appendChild(div);
    });
}

// Open add modal from input bar
function openAddModal() {
    document.getElementById('modal-title').textContent        = 'New Journal Entry';
    document.getElementById('modal-entry-title').value        = '';
    document.getElementById('modal-entry-content').value      = '';
    entryModal.show();
    setTimeout(() => document.getElementById('modal-entry-title').focus(), 300);
}

// Save journal (POST)
async function saveJournal() {
    const title   = document.getElementById('modal-entry-title').value.trim();
    const content = document.getElementById('modal-entry-content').value.trim();
    if (!title) { showToast('Please enter a title!'); return; }

    await fetch(API, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ title, content })
    });
    entryModal.hide();
    loadJournals();
    showToast('Journal saved! 📓');
}

// View journal (GET single)
async function viewJournal(id) {
    const res  = await fetch(`${API}?id=${id}`);
    const data = await res.json();
    document.getElementById('view-title').textContent   = data.title;
    document.getElementById('view-date').textContent    = 'Written on ' + fmtDate(data.created_at);
    document.getElementById('view-content').textContent = data.content || '(No content written)';
    viewModal.show();
}

// Delete journal (DELETE)
async function deleteJournal(e, id) {
    e.stopPropagation();
    if (!confirm('Delete this journal entry?')) return;
    await fetch(`${API}?id=${id}`, { method: 'DELETE' });
    loadJournals();
    showToast('Journal deleted 🗑');
}

document.getElementById('btn-add-journal').addEventListener('click', openAddModal);

// Helpers
function fmtDate(str) {
    const d = new Date(str);
    return `${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}/${d.getFullYear()}`;
}
function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

// Init
loadJournals();
</script>
</body>
</html>
