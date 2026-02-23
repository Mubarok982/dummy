</div> <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
  window.setTimeout(function() {
    $(".alert-info, .alert-success, .alert-danger").fadeTo(500, 0).slideUp(500, function(){
      $(this).remove(); 
    });
  }, 3000);
</script>

<script>
  // Enable sorting by clicking table headers with class 'sortable'
  (function($){
    $(function(){
      $('th.sortable').css('cursor','pointer');

      $('table').on('click', 'th.sortable', function(){
        var sortBy = $(this).data('sort');
        if (!sortBy) return;
        var params = new URLSearchParams(window.location.search);
        var current = params.get('sort_by');
        var order = params.get('sort_order') || 'asc';
        if (current === sortBy) {
          order = (order === 'asc') ? 'desc' : 'asc';
        } else {
          order = 'asc';
        }
        params.set('sort_by', sortBy);
        params.set('sort_order', order);
        params.delete('page');
        window.location.search = params.toString();
      });

      // Add a small indicator for the active sorted column
      var params = new URLSearchParams(window.location.search);
      var cur = params.get('sort_by');
      var ord = params.get('sort_order') || 'asc';
      if (cur) {
        $('th.sortable').each(function(){
          if ($(this).data('sort') == cur) {
            $(this).append(' <small class="text-muted">(' + ord + ')</small>');
          }
        });
      }
    });
  })(jQuery);
</script>

</body>
</html>