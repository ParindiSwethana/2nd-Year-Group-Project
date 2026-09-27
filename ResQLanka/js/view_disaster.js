document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-btn');
    const editBtnText = document.getElementById('edit-btn-text');
    const editBtnIcon = document.getElementById('edit-btn-icon');
    const saveBtn = document.getElementById('save-btn'); 
    
    const fields = ['title', 'location', 'priority', 'date', 'status', 'duration', 'summary'];

    if (editBtn) {
        editBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            const isEditing = editBtn.classList.contains('save-mode');

            if (!isEditing) {
            
                fields.forEach(field => {
                    const viewEl = document.getElementById(`view-${field}`);
                    const editEl = document.getElementById(`edit-${field}`);
                    if (viewEl && editEl) {
                        viewEl.classList.add('hidden');
                        editEl.classList.remove('hidden');
                    }
                });

                if (editBtnText) editBtnText.innerText = 'Cancel Edit';
                if (editBtnIcon) editBtnIcon.className = 'fa-solid fa-xmark';
                editBtn.classList.add('save-mode');
                if (saveBtn) saveBtn.classList.remove('hidden');

            } else {
                fields.forEach(field => {
                    const viewEl = document.getElementById(`view-${field}`);
                    const editEl = document.getElementById(`edit-${field}`);
                    
                    if (viewEl && editEl) {
                        editEl.classList.add('hidden');
                        viewEl.classList.remove('hidden');
                    }
                });
               
                if (editBtnText) editBtnText.innerText = 'Edit Disaster';
                if (editBtnIcon) editBtnIcon.className = 'fa-solid fa-pen';
                editBtn.classList.remove('save-mode');
            
                if (saveBtn) saveBtn.classList.add('hidden');
            }
        });
    }
});