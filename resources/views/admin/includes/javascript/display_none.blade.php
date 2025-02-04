<script>
    $(document).ready(function () {
        $(document).on('mouseenter', 'tr', function () {
            $(this).find('.dropdown-toggle i').removeClass('d-none');
        }).on('mouseleave', 'tr', function () {
            $(this).find('.dropdown-toggle i').addClass('d-none');
        });
    });
</script>
