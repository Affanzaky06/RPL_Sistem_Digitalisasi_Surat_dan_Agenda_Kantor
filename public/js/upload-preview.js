document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-upload');
    const uploadPrompt = document.getElementById('upload-prompt');
    const filePreview = document.getElementById('file-preview');
    const fileNameDisplay = document.getElementById('file-name');
    const btnRemove = document.getElementById('btn-remove-file');

    // Pastikan elemennya ada di halaman ini sebelum menjalankan fungsi
    if (fileInput) {
        // Ketika user memilih file
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                // Ambil nama file
                fileNameDisplay.textContent = this.files[0].name;
                
                // Sembunyikan tombol awal, munculkan kotak preview
                uploadPrompt.classList.add('d-none');
                filePreview.classList.remove('d-none');
                filePreview.classList.add('d-flex');
            }
        });

        // Ketika user menekan tombol Hapus (Tong Sampah)
        btnRemove.addEventListener('click', function() {
            // Kosongkan memori input file
            fileInput.value = '';
            
            // Sembunyikan kotak preview, munculkan tombol awal kembali
            uploadPrompt.classList.remove('d-none');
            filePreview.classList.add('d-none');
            filePreview.classList.remove('d-flex');
        });
    }
});