function formatText(command, value = null) {
    document.execCommand(command, false, value);
}

function changeFont(fontName) {
    formatText('fontName', fontName);
    closeAllDropdowns();
}

function uploadImage(input) {
    const formData = new FormData();
    formData.append('image', input.files[0]);
    fetch('upload.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.execCommand('insertImage', false, data.url);
            } else alert('Image upload failed.');
        });
}

function submitContent() {
    const content = document.getElementById('editor').innerHTML;
    const title = document.getElementById('postTitle').value;
    document.getElementById('editorContent').value = content;
    document.getElementById('postTitleHidden').value = title;
    document.forms[0].submit();
}

function toggleDropdown(dropdownId) {
    document.getElementById(dropdownId).style.display =
        document.getElementById(dropdownId).style.display === "block" ? "none" : "block";
}

function toggleMarkdown() {
    const editor = document.getElementById('editor');
    if (editor.contentEditable === "true") {
        editor.contentEditable = "false";
        editor.textContent = editor.innerHTML;
    } else {
        editor.contentEditable = "true";
        editor.innerHTML = editor.textContent;
    }
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text(document.getElementById('editor').innerText, 10, 10);
    doc.save("document.pdf");
}

function showFindReplace() {
    const findText = prompt("Find:");
    const replaceText = prompt("Replace with:");
    const content = document.getElementById('editor').innerHTML;
    document.getElementById('editor').innerHTML = content.replace(new RegExp(findText, "g"), replaceText);
}

function updateWordAndCharCount() {
    const text = document.getElementById('editor').innerText;
    document.getElementById('wordCount').textContent = text.split(/\s+/).filter(Boolean).length;
    document.getElementById('charCount').textContent = text.length;
}

document.getElementById('editor').addEventListener('input', updateWordAndCharCount);
