import '@fortawesome/fontawesome-free/css/all.min.css';
import './bootstrap';
import '../css/app.css';
import Alpine from 'alpinejs';
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";


window.Alpine = Alpine;

Alpine.start();

window.TomSelect = TomSelect;

function appendAndSelect(selectId, item) {
    const select = document.getElementById(selectId);
    if (!select) return;

    const option = document.createElement('option');
    option.value = item.id;
    option.textContent = item.name;
    option.selected = true;

    select.appendChild(option);
}

/* AUTHOR CREATED */
document.addEventListener('author-created', e => {
    appendAndSelect('authorSelect', e.detail);
});

/* PUBLICATION CREATED */
document.addEventListener('publication-created', e => {
    appendAndSelect('publicationSelect', e.detail);
});

/* CATEGORY CREATED */
document.addEventListener('category-created', e => {
    appendAndSelect('categorySelect', e.detail);
});


window.showToast = function (message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast || !toastMessage) return;

    const alert = toast.querySelector('.alert');

    // Update message
    toastMessage.textContent = message;

    // Update alert type
    alert.className = `alert alert-${type}`;

    // Show toast
    toast.classList.remove('hidden');

    // Auto-hide
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
};