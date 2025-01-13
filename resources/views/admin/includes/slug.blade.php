<script>
    $(document).ready(function() {
        $('#title').on('input', function() {
            var title = $(this).val();
            var slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            $('#slug').val(slug);
        });
    });
</script>
