document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-btn');
    const editBtnText = document.getElementById('edit-btn-text');
    const editBtnIcon = document.getElementById('edit-btn-icon');
    const fields = ['title', 'location', 'priority', 'date', 'status', 'duration', 'summary'];

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

            editBtnText.innerText = 'Save Changes';
            editBtnIcon.className = 'fa-solid fa-check';
            editBtn.classList.add('save-mode');

        } else {
            fields.forEach(field => {
                const viewEl = document.getElementById(`view-${field}`);
                const editEl = document.getElementById(`edit-${field}`);
                
                if (viewEl && editEl) {
                    if (editEl.tagName === 'SELECT') {
                        viewEl.innerText = editEl.options[editEl.selectedIndex].text;
                    } else if (editEl.type === 'date') {
                        viewEl.innerText = editEl.value;
                    } else {
                        viewEl.innerText = editEl.value;
                    }
            
                    editEl.classList.add('hidden');
                    viewEl.classList.remove('hidden');
                }
            });
            editBtnText.innerText = 'Edit Disaster';
            editBtnIcon.className = 'fa-solid fa-pen';
            editBtn.classList.remove('save-mode');
        
        }
    });
});
