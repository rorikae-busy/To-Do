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
            <div class="card-wrapper">
                <img src="../image/peek-up.png" class="up-sticker" alt="Sticker">

            <div class="list-card" id="notes-list">
                <p class="empty-state">Loading notes...</p>
            </div>
        </div>


<?php include 'footer.php'; ?>

<!-- ── ADD / EDIT MODAL ── -->
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <img src="../image/writing.png" class="modal-sticker" alt="sticker">
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
let editingNoteId = null;

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
            <button class="btn-edit" onclick="editNote(event, ${n.id})" title="Edit">
                <img src="../image/pencil.png" alt="Edit">
            </button>
            <button class="btn-delete" onclick="deleteNote(event, ${n.id})" title="Delete">
                <img src="../image/trashbin.png" alt="Delete">
            </button>
        `;
        list.appendChild(div);
    });
}

// Open add modal
function openAddModal() {
    editingNoteId = null;
    document.querySelector('#noteModal .modal-title').textContent = 'New Note';
    document.getElementById('modal-note-title').value   = '';
    document.getElementById('modal-note-content').value = '';
    noteModal.show();
    setTimeout(() => document.getElementById('modal-note-title').focus(), 300);
}

// Save note (POST or PUT)
async function saveNote() {
    const title   = document.getElementById('modal-note-title').value.trim();
    const content = document.getElementById('modal-note-content').value.trim();
    if (!title) { showToast('Please enter a title!'); return; }

    if (editingNoteId) {
        // Edit existing
        await fetch(API, {
            method:  'PUT',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ id: editingNoteId, title, content })
        });
        showToast('Note updated');
    } else {
        // Add new
        await fetch(API, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ title, content })
        });
        showToast('Note saved!');
    }
    editingNoteId = null;
    noteModal.hide();
    loadNotes();
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

// Edit note (open modal pre-filled)
async function editNote(e, id) {
    e.stopPropagation();
    const res  = await fetch(`${API}?id=${id}`);
    const data = await res.json();
    editingNoteId = id;
    document.querySelector('#noteModal .modal-title').textContent = 'Edit Note';
    document.getElementById('modal-note-title').value   = data.title;
    document.getElementById('modal-note-content').value = data.content || '';
    noteModal.show();
    setTimeout(() => document.getElementById('modal-note-title').focus(), 300);
}

// Delete note (DELETE)
async function deleteNote(e, id) {
    e.stopPropagation();
    if (!confirm('Delete this note?')) return;
    await fetch(`${API}?id=${id}`, { method: 'DELETE' });
    loadNotes();
    showToast('Note deleted');
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
