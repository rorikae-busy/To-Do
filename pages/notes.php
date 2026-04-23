<?php
// pages/notes.php — Notes Page (full separate page)
$activePage = 'notes';
$pageTitle  = 'Notes';
$pageLabel  = 'NOTES';
include 'header.php';
?>
            <!-- ── ADD NOTE ── -->
            <div class="container-wrapper">
                <p>Write a note today…</p>
                <button class="btn-plus" id="btn-add-note" title="Add note">+</button>
            </div>
        
            <!-- ── SECTION LABEL ── -->
            <div class="section-heading">notes</div>

            <!-- ── NOTES LIST ── -->
            <div class="list-card" id="notes-list">
                <p class="empty-state">Loading notes…</p>
            </div>

<?php include 'footer.php'; ?>

<!-- ── ADD / EDIT MODAL ── -->
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-3">
                <input type="text" id="modal-note-title" class="form-control"
                       placeholder="Title…" maxlength="200">
                <textarea id="modal-note-content" class="form-control"
                          placeholder="Write your note here…"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-modal-save"   onclick="saveNote()">Save</button>
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
// ─── NOTES PAGE SCRIPT ──────────────────────────────────

const API = '../api/notes.php';
const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));

// Load all notes
async function loadNotes() {
    const res  = await fetch(API);
    const data = await res.json();
    const list = document.getElementById('notes-list');
    list.innerHTML = '';

    if (!data.length) {
        list.innerHTML = '<p class="empty-state">No notes yet. Jot something down!</p>';
        return;
    }

    data.forEach(n => {
        const div = document.createElement('div');
        div.className = 'entry-item';
        div.innerHTML = `
            <span class="star-icon">★</span>
            <span class="entry-title" onclick="viewNote(${n.id})">${esc(n.title)}</span>
            <span class="entry-date">${fmtDate(n.created_at)}</span>
            <button class="btn-delete" onclick="deleteNote(event, ${n.id})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        `;
        list.appendChild(div);
    });
}

// Open add modal
function openAddModal() {
    document.getElementById('modal-note-title').value   = '';
    document.getElementById('modal-note-content').value = '';
    noteModal.show();
    setTimeout(() => document.getElementById('modal-note-title').focus(), 300);
}

// Save note (POST)
async function saveNote() {
    const title   = document.getElementById('modal-note-title').value.trim();
    const content = document.getElementById('modal-note-content').value.trim();
    if (!title) { showToast('Please enter a title!'); return; }

    await fetch(API, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ title, content })
    });
    noteModal.hide();
    loadNotes();
    showToast('Note saved!');
}

// View note (GET single)
async function viewNote(id) {
    const res  = await fetch(`${API}?id=${id}`);
    const data = await res.json();
    document.getElementById('view-title').textContent   = data.title;
    document.getElementById('view-date').textContent    = 'Written on ' + fmtDate(data.created_at);
    document.getElementById('view-content').textContent = data.content || '(No content written)';
    viewModal.show();
}

// Delete note (DELETE)
async function deleteNote(e, id) {
    e.stopPropagation();
    if (!confirm('Delete this note?')) return;
    await fetch(`${API}?id=${id}`, { method: 'DELETE' });
    loadNotes();
    showToast('Note deleted 🗑');
}

// Trigger modal from input bar
document.getElementById('btn-add-note').addEventListener('click', openAddModal);

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
loadNotes();
</script>
</body>
</html>
