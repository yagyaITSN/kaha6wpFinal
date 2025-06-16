<?php
if (!is_front_page()) {
    $heightStyle = 'itsn-banner-height';
}
$keyword = isset($_GET['s']) ? $_GET['s'] : '';
$business_type = isset($_GET['business-type']) ? $_GET['business-type'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
?>
<form method="GET" action="<?php echo esc_url(home_url('/')); ?>"
    class="search-form bg-white rounded-4 shadow p-3 p-md-4">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <input
                type="text"
                name="s"
                class="form-control custom-form"
                placeholder="Company Name"
                value="<?php echo esc_attr($keyword); ?>">
        </div>
        <div class="col-12 col-md-3">
            <select
                id="business-type-unique"
                name="business-type"
                class="form-control">
                <option value="" <?php selected('', $business_type); ?>>Business Type</option>
                <?php
                $business_terms = get_terms('ait-items', array('hide_empty' => false));
                foreach ($business_terms as $term) {
                    echo '<option value="' . esc_attr($term->name) . '" ' . selected($term->name, $business_type, false) . '>' . esc_html($term->name) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select
                id="location-unique"
                name="location"
                class="form-control">
                <option value="" <?php selected('', $location); ?>>Location</option>
                <?php
                $location_terms = get_terms('ait-locations', array('hide_empty' => false));
                foreach ($location_terms as $term) {
                    echo '<option value="' . esc_attr($term->name) . '" ' . selected($term->name, $location, false) . '>' . esc_html($term->name) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-custom-red w-100 py-2">Search</button>
        </div>
    </div>
</form>