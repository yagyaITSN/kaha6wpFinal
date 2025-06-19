<div class="category_filter">
  <select id="ratingFilter">
    <option value="all">All Ratings</option>
    <option value="asc">Low to High</option>
    <option value="desc">High to Low</option>
  </select>
</div>

<script>
  jQuery(document).ready(function($) {
    $('#ratingFilter').on('change', function() {
      const selectedValue = $(this).val();
      const $cardsContainer = $('.category-cards');
      const $cards = $cardsContainer.find('.card[data-rating]');

      if (selectedValue === 'all') {
        // Reset to original order (might need adjustment based on your needs)
        $cards.sort(function(a, b) {
          return $(a).index() - $(b).index();
        }).appendTo($cardsContainer);
        return;
      }

      // Convert to array for sorting
      const cardsArray = $cards.get();

      cardsArray.sort(function(a, b) {
        const ratingA = parseFloat($(a).data('rating')) || 0;
        const ratingB = parseFloat($(b).data('rating')) || 0;

        if (selectedValue === 'asc') {
          return ratingA - ratingB;
        } else {
          return ratingB - ratingA;
        }
      });

      // Re-append sorted cards
      $(cardsArray).appendTo($cardsContainer);
    });
  });
</script>