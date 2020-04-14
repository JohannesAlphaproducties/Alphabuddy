function confirmDelete() {
    Swal.fire({
        title: 'Weet u het zeker?',
        text: "U zal dit niet kunnen terugdraaien?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ja, verwijder!',
        cancelButtonText: 'Annuleer'
    }).then((result) => {
        if (result.value) {
            $('#deleteForm').submit();
        }
    })
}